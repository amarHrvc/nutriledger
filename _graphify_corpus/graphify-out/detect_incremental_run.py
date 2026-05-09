import sys, json
from pathlib import Path

try:
    from graphify.detect import detect_incremental
except Exception as e:
    print('ERROR_IMPORT:' + str(e), file=sys.stderr)
    sys.exit(2)

INPUT_PATH = Path(r"D:\_Learn\_PhpstormProjects\nutri-ledger\_graphify_corpus")
OUT_DIR = INPUT_PATH / 'graphify-out'
OUT_DIR.mkdir(parents=True, exist_ok=True)

result = detect_incremental(INPUT_PATH)

# write full JSON to graphify-out
Path(OUT_DIR / '.graphify_incremental.json').write_text(json.dumps(result, indent=2))
# also print compact JSON to stdout
print(json.dumps(result))
