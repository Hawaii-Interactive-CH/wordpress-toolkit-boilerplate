/**
 * Smoke test: requests the main views of a running WordPress site and fails
 * on unexpected status codes, PHP errors, empty pages or invalid JSON-LD.
 *
 * URLs are discovered through the REST API, so it works on any install:
 *   SMOKE_BASE_URL=http://localhost:10220 npm run test:smoke
 */

const BASE_URL = (process.env.SMOKE_BASE_URL || "http://localhost:8888").replace(/\/$/, "");

const PHP_ERROR = /<b>(Fatal error|Parse error|Warning|Notice|Deprecated)<\/b>:|There has been a critical error/;

async function api(path) {
    const response = await fetch(`${BASE_URL}/wp-json/wp/v2/${path}`);
    if (!response.ok) {
        throw new Error(`REST ${path} -> HTTP ${response.status}`);
    }
    return response.json();
}

async function first(path) {
    const items = await api(path);
    return items[0] ?? null;
}

async function discover() {
    const pages = [
        { name: "home", url: "/" },
        { name: "search", url: "/?s=a", listing: true },
        { name: "404", url: `/smoke-test-missing-${Date.now()}/`, status: 404 },
    ];

    const post = await first("posts?per_page=1");
    if (post) {
        const year = post.date.slice(0, 4);
        pages.push(
            { name: "post", url: post.link },
            { name: "author archive", url: `/?author=${post.author}`, listing: true },
            { name: "date archive", url: `/?m=${year}`, listing: true },
        );
    }

    const page = await first("pages?per_page=1");
    if (page) {
        pages.push({ name: "page", url: page.link });
    }

    const category = await first("categories?per_page=1&hide_empty=true");
    if (category) {
        pages.push({ name: "category archive", url: category.link, listing: true });
    }

    const types = Object.values(await api("types")).filter(
        (type) => !["post", "page", "attachment"].includes(type.slug) && !type.slug.startsWith("wp_") && type.slug !== "nav_menu_item",
    );
    for (const type of types) {
        const item = await first(`${type.rest_base}?per_page=1`).catch(() => null);
        if (item) {
            pages.push(
                { name: `${type.slug} single`, url: item.link },
                { name: `${type.slug} archive`, url: `/?post_type=${type.slug}`, listing: true },
            );
        }
    }

    // Attachment-only taxonomies (e.g. the plugin's media_category) have no
    // meaningful front-end archive: WordPress does not list attachments there.
    const taxonomies = Object.values(await api("taxonomies")).filter(
        (taxonomy) =>
            !["category", "post_tag", "nav_menu", "wp_pattern_category"].includes(taxonomy.slug) &&
            !taxonomy.types.every((type) => type === "attachment"),
    );
    for (const taxonomy of taxonomies) {
        const term = await first(`${taxonomy.rest_base}?per_page=1&hide_empty=true`).catch(() => null);
        if (term) {
            pages.push({ name: `${taxonomy.slug} archive`, url: term.link, listing: true });
        }
    }

    return pages;
}

function check(page, response, html) {
    const errors = [];
    const expected = page.status ?? 200;

    if (response.status !== expected) {
        errors.push(`HTTP ${response.status}, expected ${expected}`);
    }

    const phpError = html.match(PHP_ERROR);
    if (phpError) {
        errors.push(`PHP error: ${phpError[0].replace(/<\/?b>/g, "")}`);
    }

    const mains = html.match(/<main[\s>]/g) ?? [];
    if (mains.length !== 1) {
        errors.push(`${mains.length} <main> elements, expected 1`);
    }

    if (!/<h1[^>]*>[\s\S]*?\S[\s\S]*?<\/h1>/.test(html) && page.name !== "home" && page.name !== "search") {
        errors.push("no <h1>, the template probably rendered nothing");
    }

    if (page.listing && !html.includes("<article")) {
        errors.push("listing without any <article>");
    }

    const jsonLd = html.match(/<script type="application\/ld\+json">([\s\S]*?)<\/script>/);
    if (!jsonLd) {
        errors.push("missing JSON-LD");
    } else {
        try {
            JSON.parse(jsonLd[1]);
        } catch (error) {
            errors.push(`invalid JSON-LD: ${error.message}`);
        }
    }

    return errors;
}

const pages = await discover();
let failures = 0;

for (const page of pages) {
    const url = page.url.startsWith("http") ? page.url : BASE_URL + page.url;
    const response = await fetch(url, { redirect: "follow" });
    const errors = check(page, response, await response.text());

    if (errors.length) {
        failures++;
        console.log(`✗ ${page.name.padEnd(28)} ${url}`);
        errors.forEach((error) => console.log(`    - ${error}`));
    } else {
        console.log(`✓ ${page.name.padEnd(28)} ${url}`);
    }
}

console.log(`\n${pages.length - failures}/${pages.length} pages OK`);
process.exit(failures ? 1 : 0);
