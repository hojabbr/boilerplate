#!/usr/bin/env python3
"""
Convert variable TTFs from resources/fonts/ to woff2 in public/fonts/.
Requires: pip install -r scripts/requirements-fonts.txt (or pip install fonttools brotli)

Run from repo root: python3 scripts/fonts-to-woff2.py

Alternative (no Python): npm run fonts:woff2 (uses scripts/fonts-to-woff2.mjs)
"""

from pathlib import Path


def main() -> None:
    root = Path(__file__).resolve().parent.parent
    resources = root / "resources" / "fonts"
    public_fonts = root / "public" / "fonts"
    public_fonts.mkdir(parents=True, exist_ok=True)

    pairs = [
        (
            resources / "Inter" / "Inter-VariableFont_opsz,wght.ttf",
            public_fonts / "inter-variable.woff2",
        ),
        (
            resources / "Vazirmatn" / "Vazirmatn-VariableFont_wght.ttf",
            public_fonts / "vazirmatn-variable.woff2",
        ),
    ]

    try:
        from fontTools.ttLib import TTFont
    except ImportError:
        raise SystemExit(
            "fonttools is required. Install with: pip install fonttools brotli"
        ) from None

    for src, dst in pairs:
        if not src.exists():
            raise SystemExit(f"Source font not found: {src}")
        font = TTFont(src)
        font.flavor = "woff2"
        font.save(dst)
        print(f"Wrote {dst.relative_to(root)}")


if __name__ == "__main__":
    main()
