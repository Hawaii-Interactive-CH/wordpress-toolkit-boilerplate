import fs from 'fs';
import path from 'path';
import { config } from "dotenv";

// Load environment variables
config();

let rootPaths = [];

const users = [
    'martin',
    'home',
    'root',
    'wladimirdudan',
    'laura',
    'gaelle',
]

users.forEach(user => {
    const paths = [
        `/Users/${user}/Web/${process.env.APP_NAME}/app/public/wp-content/plugins/wordpress-toolkit-plugin`,
        `/Users/${user}/Local Sites/${process.env.APP_NAME}/app/public/wp-content/plugins/wordpress-toolkit-plugin`,
        `/Users/${user}/Sites/${process.env.APP_NAME}/app/public/wp-content/plugins/wordpress-toolkit-plugin`,
        `/Users/${user}/Web/${process.env.APP_NAME}/app/public/wp-content/plugins/wordpress-toolkit-plugin-main`,
        `/Users/${user}/Local Sites/${process.env.APP_NAME}/app/public/wp-content/plugins/wordpress-toolkit-plugin-main`,
        `/Users/${user}/Sites/${process.env.APP_NAME}/app/public/wp-content/plugins/wordpress-toolkit-plugin-main`,
    ];
    rootPaths.push(...paths);
});


const settings = {
    "intelephense.environment.includePaths": rootPaths
};

// Ensure .vscode directory exists
const vscodeDirectory = path.join('./', '.vscode');
if (!fs.existsSync(vscodeDirectory)) {
    fs.mkdirSync(vscodeDirectory);
}

// Write settings.json file
fs.writeFileSync(
    path.join(vscodeDirectory, 'settings.json'),
    JSON.stringify(settings, null, 4)
);