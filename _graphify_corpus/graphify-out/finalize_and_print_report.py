import json
from pathlib import Path
from datetime import datetime, timezone
import sys

OUT_DIR = Path(r'D:\_Learn\_PhpstormProjects\nutri-ledger\_graphify_corpus\graphify-out')
# Load detect (prefer incremental if present)
inc = OUT_DIR / '.graphify_incremental.json'
detect_file = OUT_DIR / '.graphify_detect.json'
if inc.exists():
    detect = json.loads(inc.read_text(encoding='utf-8'))
elif detect_file.exists():
    detect = json.loads(detect_file.read_text(encoding='utf-8'))
else:
    detect = {}

# Save manifest if possible
try:
    from graphify.detect import save_manifest
    files_to_save = detect.get('files') if isinstance(detect, dict) else None
    if files_to_save:
        save_manifest(files_to_save)
        print('Manifest saved for update')
except Exception as e:
    print('WARN: save_manifest failed: ' + str(e), file=sys.stderr)

# Update cumulative cost tracker
extract_path = OUT_DIR / '.graphify_extract.json'
if extract_path.exists():
    extract = json.loads(extract_path.read_text(encoding='utf-8'))
else:
    extract = {'input_tokens': 0, 'output_tokens': 0}
input_tok = extract.get('input_tokens', 0)
output_tok = extract.get('output_tokens', 0)

cost_path = OUT_DIR / 'cost.json'
if cost_path.exists():
    cost = json.loads(cost_path.read_text(encoding='utf-8'))
else:
    cost = {'runs': [], 'total_input_tokens': 0, 'total_output_tokens': 0}

cost['runs'].append({
    'date': datetime.now(timezone.utc).isoformat(),
    'input_tokens': input_tok,
    'output_tokens': output_tok,
    'files': detect.get('new_total', detect.get('total_files', 0)) if isinstance(detect, dict) else 0,
})
cost['total_input_tokens'] += input_tok
cost['total_output_tokens'] += output_tok
cost_path.write_text(json.dumps(cost, indent=2), encoding='utf-8')

print(f"This run: {input_tok:,} input tokens, {output_tok:,} output tokens")
print(f"All time: {cost['total_input_tokens']:,} input, {cost['total_output_tokens']:,} output ({len(cost['runs'])} runs)")

# Clean up temp files (best effort)
for fname in ['.graphify_detect.json', '.graphify_extract.json', '.graphify_ast.json', '.graphify_semantic.json', '.graphify_analysis.json', '.graphify_labels.json']:
    p = OUT_DIR / fname
    try:
        if p.exists():
            p.unlink()
    except Exception as e:
        print(f'WARN: could not remove {p}: {e}', file=sys.stderr)

# Remove .needs_update if present
needs = OUT_DIR / '.needs_update'
try:
    if needs.exists():
        needs.unlink()
except Exception as e:
    print('WARN: could not remove .needs_update: ' + str(e), file=sys.stderr)

# Read report and extract sections
report_path = OUT_DIR / 'GRAPH_REPORT.md'
if not report_path.exists():
    print('ERROR: GRAPH_REPORT.md not found', file=sys.stderr)
    sys.exit(1)

report_text = report_path.read_text(encoding='utf-8')

def extract_section(text, start_marker, end_marker):
    if start_marker not in text:
        return ''
    s = text.split(start_marker, 1)[1]
    if end_marker in s:
        return s.split(end_marker, 1)[0].strip()
    return s.strip()

god = extract_section(report_text, '## God Nodes', '## Surprising Connections')
surprises = extract_section(report_text, '## Surprising Connections', '## Suggested Questions')
questions = extract_section(report_text, '## Suggested Questions', '## Communities')

print('\n=== GOD NODES ===\n')
print(god)
print('\n=== SURPRISING CONNECTIONS ===\n')
print(surprises)
print('\n=== SUGGESTED QUESTIONS ===\n')
print(questions)

print()
print('Graph complete. Outputs in ' + str(OUT_DIR) + '\\')
print()
print('  graph.html            - interactive graph, open in browser')
print('  GRAPH_REPORT.md       - audit report')
print('  graph.json            - raw graph data')
if (OUT_DIR / 'obsidian').exists():
    print('  obsidian/             - Obsidian vault (only if --obsidian was given)')
