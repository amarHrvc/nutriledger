import sys, json
from graphify.extract import collect_files, extract
from pathlib import Path

inc_path = Path(r'D:\_Learn\_PhpstormProjects\nutri-ledger\_graphify_corpus\graphify-out\.graphify_incremental.json')
if not inc_path.exists():
    print('No incremental file found', file=sys.stderr)
    sys.exit(1)

inc = json.loads(inc_path.read_text())
code_files = []
for f in inc.get('new_files', {}).get('code', []):
    p = Path(f)
    if p.is_dir():
        code_files.extend([str(x) for x in collect_files(p)])
    else:
        code_files.append(str(p))

out_ast = Path(r'D:\_Learn\_PhpstormProjects\nutri-ledger\_graphify_corpus\graphify-out\.graphify_ast.json')
if code_files:
    result = extract([Path(f) for f in code_files])
    out_ast.write_text(json.dumps(result, indent=2))
    print(f"AST: {len(result.get('nodes', []))} nodes, {len(result.get('edges', []))} edges")
else:
    out_ast.write_text(json.dumps({'nodes':[],'edges':[],'input_tokens':0,'output_tokens':0}))
    print('No code files - skipping AST extraction')
