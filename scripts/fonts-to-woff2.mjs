#!/usr/bin/env node
/**
 * Convert variable TTFs from resources/fonts/ to woff2 in public/fonts/.
 * Requires: npm install ttf2woff2 (or run as npm run fonts:woff2)
 *
 * Run from repo root: node scripts/fonts-to-woff2.mjs
 */

import { readFile, writeFile, mkdir } from 'node:fs/promises';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const root = join(__dirname, '..');

const pairs = [
  [
    join(root, 'resources/fonts/Inter/Inter-VariableFont_opsz,wght.ttf'),
    join(root, 'public/fonts/inter-variable.woff2'),
  ],
  [
    join(root, 'resources/fonts/Vazirmatn/Vazirmatn-VariableFont_wght.ttf'),
    join(root, 'public/fonts/vazirmatn-variable.woff2'),
  ],
];

const ttf2woff2 = (await import('ttf2woff2')).default;

await mkdir(join(root, 'public/fonts'), { recursive: true });

for (const [src, dst] of pairs) {
  const input = await readFile(src);
  const output = ttf2woff2(input);
  await writeFile(dst, output);
  console.log('Wrote', dst.replace(root, '').replace(/^\//, ''));
}
