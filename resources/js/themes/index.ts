export { dark } from './dark';
export { light } from './light';
export { sepia } from './sepia';

import type { Dark } from './dark';
import type { Light } from './light';
import type { Sepia } from './sepia';

export type Theme = Dark | Light | Sepia;
