import { execSync } from 'child_process';
import { readdirSync, mkdirSync } from 'fs';
import { join, relative } from 'path';

const INPUT_DIR = '_docs_uni/diagrams';
const OUTPUT_DIR = '_docs_uni/diagrams/output';

function findMmdFiles(dir) {
    const entries = readdirSync(dir, { withFileTypes: true });
    return entries.flatMap(entry => {
        const fullPath = join(dir, entry.name);
        if (entry.isDirectory() && entry.name !== 'output') {
            return findMmdFiles(fullPath);
        }
        if (entry.isFile() && entry.name.endsWith('.mmd')) {
            return [fullPath];
        }
        return [];
    });
}

const mmdc = process.platform === 'win32'
    ? 'node_modules\\.bin\\mmdc.cmd'
    : 'node_modules/.bin/mmdc';

const files = findMmdFiles(INPUT_DIR);
mkdirSync(OUTPUT_DIR, { recursive: true });

for (const file of files) {
    const rel = relative(INPUT_DIR, file);
    const outName = rel.replace(/[/\\]/g, '-').replace('.mmd', '.png');
    const outPath = join(OUTPUT_DIR, outName);
    console.log(`Rendering ${file} -> ${outPath}`);
    execSync(`"${mmdc}" -i "${file}" -o "${outPath}"`, { stdio: 'inherit' });
}

console.log(`\nDone. ${files.length} diagram(s) written to ${OUTPUT_DIR}`);
