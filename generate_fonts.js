import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Get the directory name of the current module file
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const FONT_DIR = path.join(__dirname, 'src/scss/assets/fonts');
const OUTPUT_FILE = path.join(__dirname, 'src/scss/partials/base/_typography.scss');

// Slugify function to convert font names to a CSS-friendly format
const slugify = (str) => {
    return str
        .toString()
        .toLowerCase()
        .replace(/\s+/g, '-')        // Replace spaces with -
        .replace(/[^\w\-]+/g, '')   // Remove all non-word characters
        .replace(/\-\-+/g, '-')     // Replace multiple - with single -
        .replace(/^-+/, '')         // Trim - from start of text
        .replace(/-+$/, '');        // Trim - from end of text
};

// Read the existing SCSS content if the file exists
let existingScssContent = '';
if (fs.existsSync(OUTPUT_FILE)) {
    existingScssContent = fs.readFileSync(OUTPUT_FILE, 'utf8');
}

// Define the markers for the generated content
const generatedContentStart = '\\/\\*\\*\n\\* Generated font-face and mixin declarations\n\\*\\/';
const generatedContentEnd = '\\/\\*\\* End of generated content \\*\\*\\/';

// Remove existing generated content
const generatedContentRegex = new RegExp(`${generatedContentStart}[\\s\\S]*?${generatedContentEnd}`, 'g');
existingScssContent = existingScssContent.replace(generatedContentRegex, '');

// Generate new content
let newScssContent = `/**
* Generated font-face and mixin declarations
*/\n\n`;

const fonts = fs.readdirSync(FONT_DIR).filter(file => file.endsWith('.ttf'));

fonts.forEach(fontFile => {
    const fontName = path.basename(fontFile, '.ttf');
    const fontSlug = slugify(fontName);
    const fontFace = `@font-face {
    font-family: "${fontName}";
    src: url("@fonts/${fontName}.woff") format("woff"),
         url("@fonts/${fontName}.woff2") format("woff2");
    font-style: normal;
}\n\n`;

    const mixin = `@mixin ${fontSlug} {
    font-family: "${fontName}", serif;
}\n\n`;

    newScssContent += fontFace + mixin;
});

newScssContent += `/** End of generated content **/\n`;

// Place the new content before the body block
const bodyBlockRegex = /(\n\s*body\s*\{[\s\S]*?\})/g;
const combinedScssContent = existingScssContent.replace(bodyBlockRegex, `\n${newScssContent.trim()}\n$1`);

// Write the combined content to the SCSS file
fs.writeFileSync(OUTPUT_FILE, combinedScssContent.trim() + '\n');

console.log(`SCSS content generated and written to ${OUTPUT_FILE}`);
