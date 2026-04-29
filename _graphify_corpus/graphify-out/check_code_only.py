import json
from pathlib import Path

p = Path(r"D:\_Learn\_PhpstormProjects\nutri-ledger\_graphify_corpus\graphify-out\.graphify_incremental.json")
if not p.exists():
    print('MISSING')
    raise SystemExit(1)

result = json.loads(p.read_text())
new_files = result.get('new_files', {})
all_changed = [f for files in new_files.values() for f in files]
code_exts = {'.py','.ts','.js','.go','.rs','.java','.cpp','.c','.rb','.swift','.kt','.cs','.scala','.php','.cc','.cxx','.hpp','.h','.kts','.lua','.toc'}
from pathlib import Path as P
code_only = all(P(f).suffix.lower() in code_exts for f in all_changed)
print('code_only:', code_only)
print('new_total:', result.get('new_total', 0))
print('counts:', {k: len(v) for k, v in result.get('new_files', {}).items()})
