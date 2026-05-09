import sys, json
from graphify.build import build_from_json
from graphify.cluster import cluster, score_all
from graphify.analyze import god_nodes, surprising_connections, suggest_questions
from graphify.report import generate
from graphify.export import to_json
from pathlib import Path

extraction = json.loads(Path(r'D:\_Learn\_PhpstormProjects\nutri-ledger\_graphify_corpus\graphify-out\.graphify_extract.json').read_text())
detection  = json.loads(Path(r'D:\_Learn\_PhpstormProjects\nutri-ledger\_graphify_corpus\graphify-out\.graphify_incremental.json').read_text())

G = build_from_json(extraction)
communities = cluster(G)
cohesion = score_all(G, communities)
tokens = {'input': extraction.get('input_tokens', 0), 'output': extraction.get('output_tokens', 0)}
gods = god_nodes(G)
surprises = surprising_connections(G, communities)
labels = {cid: 'Community ' + str(cid) for cid in communities}
questions = suggest_questions(G, communities, labels)

report = generate(G, communities, cohesion, labels, gods, surprises, detection, tokens, r'D:\_Learn\_PhpstormProjects\nutri-ledger\_graphify_corpus', suggested_questions=questions)
Path(r'D:\_Learn\_PhpstormProjects\nutri-ledger\_graphify_corpus\graphify-out\GRAPH_REPORT.md').write_text(report, encoding='utf-8')
to_json(G, communities, r'D:\_Learn\_PhpstormProjects\nutri-ledger\_graphify_corpus\graphify-out\graph.json')

analysis = {
    'communities': {str(k): v for k, v in communities.items()},
    'cohesion': {str(k): v for k, v in cohesion.items()},
    'gods': gods,
    'surprises': surprises,
    'questions': questions,
}
Path(r'D:\_Learn\_PhpstormProjects\nutri-ledger\_graphify_corpus\graphify-out\.graphify_analysis.json').write_text(json.dumps(analysis, indent=2), encoding='utf-8')
if G.number_of_nodes() == 0:
    print('ERROR: Graph is empty - extraction produced no nodes.')
    raise SystemExit(1)
print(f'Graph: {G.number_of_nodes()} nodes, {G.number_of_edges()} edges, {len(communities)} communities')
