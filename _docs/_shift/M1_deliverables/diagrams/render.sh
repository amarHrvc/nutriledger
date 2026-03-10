#!/bin/bash
# Render all .mmd files to PNG using mermaid-cli
# Run from this directory: bash render.sh
# Requires: Node.js + npx (no install needed)

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
OUT_DIR="$SCRIPT_DIR/rendered"

mkdir -p "$OUT_DIR"

for file in "$SCRIPT_DIR"/*.mmd; do
    filename=$(basename "$file" .mmd)
    echo "Rendering $filename..."
    npx --yes @mermaid-js/mermaid-cli mmdc \
        -i "$file" \
        -o "$OUT_DIR/$filename.png" \
        --width 1200 \
        --backgroundColor white
done

echo ""
echo "Done. PNGs written to: $OUT_DIR"
