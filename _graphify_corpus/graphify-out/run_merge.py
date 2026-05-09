import sys, json
from pathlib import Path

ast_path = Path(r'D:\_Learn\_PhpstormProjects\nutri-ledger\_graphify_corpus\graphify-out\.graphify_ast.json')
sem_path = Path(r'D:\_Learn\_PhpstormProjects\nutri-ledger\_graphify_corpus\graphify-out\.graphify_semantic.json')

ast = json.loads(ast_path.read_text()) if ast_path.exists() else {'nodes': [], 'edges': []}
sem = json.loads(sem_path.read_text()) if sem_path.exists() else {'nodes': [], 'edges': [], 'hyperedges': []}

seen = {n['id'] for n in ast.get('nodes', [])}
merged_nodes = list(ast.get('nodes', []))
for n in sem.get('nodes', []):
    if n['id'] not in seen:
        merged_nodes.append(n)
        seen.add(n['id'])

merged_edges = ast.get('edges', []) + sem.get('edges', [])
merged_hyperedges = sem.get('hyperedges', [])
merged = {
    'nodes': merged_nodes,
    'edges': merged_edges,
    'hyperedges': merged_hyperedges,
    'input_tokens': sem.get('input_tokens', 0),
    'output_tokens': sem.get('output_tokens', 0),
}
out_path = Path(r'D:\_Learn\_PhpstormProjects\nutri-ledger\_graphify_corpus\graphify-out\.graphify_extract.json')
out_path.write_text(json.dumps(merged, indent=2))

print(f"Merged: {len(merged_nodes)} nodes, {len(merged_edges)} edges ({len(ast.get('nodes',[]))} AST + {len(sem.get('nodes',[]))} semantic)")
