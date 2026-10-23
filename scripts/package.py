"""Package source without runtime state or real credentials (Python standard library)."""
from pathlib import Path
import argparse
import zipfile

parser = argparse.ArgumentParser()
parser.add_argument("--output", required=True)
parser.add_argument("--include-local-config", action="store_true")
args = parser.parse_args()
root = Path(__file__).resolve().parent.parent
target = Path(args.output).resolve()
if target.exists():
    raise SystemExit(f"Refusing to overwrite existing archive: {target}")
count = 0
with zipfile.ZipFile(target, "x", zipfile.ZIP_DEFLATED) as archive:
    for path in sorted(root.rglob("*")):
        if not path.is_file():
            continue
        relative = path.relative_to(root).as_posix()
        if relative.startswith((".git/", "tests/results/")) or relative == "config/test.php":
            continue
        if relative == "config/local.php" and not args.include_local_config:
            continue
        if relative.startswith("storage/") and relative != "storage/.gitkeep":
            continue
        if relative.startswith("uploads/") and relative not in ("uploads/.htaccess", "uploads/.gitkeep"):
            continue
        if relative.endswith(".zip"):
            continue
        archive.write(path, "news-blog-cms/" + relative)
        count += 1
print(f"{target} | {count} files | {target.stat().st_size} bytes")
