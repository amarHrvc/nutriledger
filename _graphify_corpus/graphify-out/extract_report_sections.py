from pathlib import Path
p = Path(r'D:\_Learn\_PhpstormProjects\nutri-ledger\_graphify_corpus\graphify-out\GRAPH_REPORT.md')
text = p.read_text(encoding='utf-8')

def get_section(start, end):
    if start not in text:
        return ''
    s = text.split(start,1)[1]
    if end in s:
        return s.split(end,1)[0].strip()
    return s.strip()

god = get_section('## God Nodes', '## Surprising Connections')
surprises = get_section('## Surprising Connections', '## Suggested Questions')
questions = get_section('## Suggested Questions', '## Communities')

print('=== GOD NODES ===\n')
print(god)
print('\n=== SURPRISING CONNECTIONS ===\n')
print(surprises)
print('\n=== SUGGESTED QUESTIONS ===\n')
print(questions)
