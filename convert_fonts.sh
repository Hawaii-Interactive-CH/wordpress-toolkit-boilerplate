#!/bin/bash

# Directory containing the TTF files
FONT_DIR="./src/scss/assets/fonts"

# Check if FontForge is installed
if ! command -v fontforge &> /dev/null; then
    echo "FontForge could not be found. Please install it first."
    echo "On Ubuntu, you can install it with 'sudo apt install fontforge'."
    echo "On macOS, you can install it with 'brew install fontforge'."
    exit 1
fi

# Convert each TTF file to WOFF and WOFF2 if they don't already exist
for ttf_file in "$FONT_DIR"/*.ttf; do
    # Extract the base name without extension
    base_name=$(basename "$ttf_file" .ttf)

    # Convert to WOFF if the file doesn't already exist
    if [ ! -f "$FONT_DIR/$base_name.woff" ]; then
        fontforge -lang=ff -c "Open('$ttf_file'); Generate('$FONT_DIR/$base_name.woff')"
        echo "Converted $ttf_file to $base_name.woff"
    else
        echo "$base_name.woff already exists. Skipping..."
    fi

    # Convert to WOFF2 if the file doesn't already exist
    if [ ! -f "$FONT_DIR/$base_name.woff2" ]; then
        fontforge -lang=ff -c "Open('$ttf_file'); Generate('$FONT_DIR/$base_name.woff2')"
        echo "Converted $ttf_file to $base_name.woff2"
    else
        echo "$base_name.woff2 already exists. Skipping..."
    fi
done

# Generate CSS file with font-face rules
echo "Generating CSS file..."
node generate_fonts.js
