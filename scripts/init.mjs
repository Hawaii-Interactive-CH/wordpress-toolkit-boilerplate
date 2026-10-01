/**
 * Prepares a new project from the boilerplate: theme name and description,
 * package names, .env, optional fresh git repository, dependencies.
 *
 *   npm run init
 *   npm run init -- --name="Mon site" --description="Site de …" --git-init --yes
 *
 * Options:
 *   --name, --description  Skip the matching question
 *   --git-init             Replace the boilerplate git history with a new repository
 *   --skip-install         Don't run npm install / composer install
 *   --yes                  Don't ask anything, use the options and the defaults
 */

import { copyFileSync, existsSync, readFileSync, rmSync, writeFileSync } from "node:fs";
import { execSync } from "node:child_process";
import { basename } from "node:path";
import { createInterface } from "node:readline/promises";

const args = Object.fromEntries(
    process.argv.slice(2).map((arg) => {
        const [key, ...value] = arg.replace(/^--/, "").split("=");
        return [key, value.length ? value.join("=") : true];
    }),
);

// Lines are read through the async iterator, which buffers them: rl.question()
// drops answers that arrive before the question when stdin is piped
const rl = args.yes ? null : createInterface({ input: process.stdin });
const lines = rl?.[Symbol.asyncIterator]();

async function prompt(question) {
    process.stdout.write(question);
    const { value, done } = await lines.next();
    if (!process.stdin.isTTY) {
        process.stdout.write("\n");
    }
    return done ? "" : value.trim();
}

async function ask(question, fallback) {
    if (!rl) {
        return fallback;
    }
    const answer = await prompt(`${question}${fallback ? ` (${fallback})` : ""} : `);
    return answer || fallback;
}

async function confirm(question) {
    if (!rl) {
        return false;
    }
    const answer = (await prompt(`${question} (o/N) : `)).toLowerCase();
    return ["o", "oui", "y", "yes"].includes(answer);
}

function slugify(value) {
    return value
        .normalize("NFD")
        .replace(/[̀-ͯ]/g, "")
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/^-|-$/g, "");
}

function update(file, transform) {
    const before = readFileSync(file, "utf8");
    const after = transform(before);
    if (after !== before) {
        writeFileSync(file, after);
        console.log(`✓ ${file}`);
    }
}

function updateJson(file, transform) {
    update(file, (content) => JSON.stringify(transform(JSON.parse(content)), null, 4) + "\n");
}

function run(command) {
    console.log(`\n$ ${command}`);
    execSync(command, { stdio: "inherit" });
}

function available(command) {
    try {
        execSync(`${command} --version`, { stdio: "ignore" });
        return true;
    } catch {
        return false;
    }
}

// Defaults: the current style.css values, unless they are still the boilerplate's
const style = readFileSync("toolkit/style.css", "utf8");
const currentName = style.match(/^Theme Name:\s*(.*)$/m)?.[1].trim();
const currentDescription = style.match(/^Description:\s*(.*)$/m)?.[1].trim();
const defaultName = currentName && currentName !== "Toolkit" ? currentName : basename(process.cwd());
const defaultDescription = currentDescription && currentDescription !== "Shaped with love." ? currentDescription : "";

const name = args.name ?? (await ask("Nom du site", defaultName));
const description = args.description ?? (await ask("Description du thème", defaultDescription));
const gitInit = args["git-init"] ?? (await confirm("Supprimer l'historique git du boilerplate et créer un nouveau dépôt ?"));
rl?.close();

const slug = slugify(name);
if (!slug) {
    console.error("Nom invalide.");
    process.exit(1);
}

console.log("");

update("toolkit/style.css", (css) =>
    css
        .replace(/^Theme Name:.*$/m, `Theme Name: ${name}`)
        .replace(/^Description:.*$/m, `Description: ${description || name}`)
        .replace(/^Version:.*$/m, "Version: 1.0.0"),
);

updateJson("package.json", (pkg) => ({ ...pkg, name: slug, version: "1.0.0" }));

updateJson("composer.json", (composer) => ({
    ...composer,
    name: `${composer.name.split("/")[0]}/${slug}`,
    description: description || name,
}));

if (!existsSync(".env")) {
    copyFileSync(".env.example", ".env");
    console.log("✓ .env (copié depuis .env.example)");
}

if (gitInit) {
    rmSync(".git", { recursive: true, force: true });
    run("git init -q");
    console.log("✓ Nouveau dépôt git (aucun commit)");
}

if (!args["skip-install"]) {
    run("npm install");

    if (available("composer")) {
        run("composer install");
    } else {
        console.log("\n⚠ Composer introuvable : lancez `composer install` plus tard (PHPStan et autocomplétion PHP).");
    }
}

console.log(`
Projet « ${name} » prêt. Étapes suivantes :
  1. Activer le thème « ${name} », le plugin WordPress Toolkit et ACF Pro dans WordPress
  2. Compléter .env si besoin
  3. npm run dev
`);
