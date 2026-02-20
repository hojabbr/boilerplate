export { dark } from './dark';
export { light } from './light';
export { sepia } from './sepia';

import type { dark as darkTheme } from './dark';
import type { light as lightTheme } from './light';
import type { sepia as sepiaTheme } from './sepia';

export type Theme = darkTheme | lightTheme | sepiaTheme;
