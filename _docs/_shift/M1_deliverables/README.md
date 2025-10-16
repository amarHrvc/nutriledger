# M1 Deliverables

## Contents

| File | Description |
|---|---|
| `01_user_stories.md` | 27 functional + 3 non-functional user stories |
| `02_product_roadmap.md` | Product roadmap and vision |
| `03_release_plan.md` | Release plan across M1–M3 milestones |
| `diagrams/` | Mermaid `.mmd` source files |

## Diagrams

### Files

| File | Type |
|---|---|
| `act_01_patient_registration.mmd` | Activity diagram |
| `act_02_visit_creation.mmd` | Activity diagram |
| `act_03_patient_profile_view.mmd` | Activity diagram |
| `act_04_patient_soft_delete_restore.mmd` | Activity diagram |
| `act_05_visit_list_access.mmd` | Activity diagram |
| `seq_01_create_patient.mmd` | Sequence diagram |
| `seq_02_view_patient.mmd` | Sequence diagram |
| `seq_03_update_patient.mmd` | Sequence diagram |
| `seq_04_create_visit.mmd` | Sequence diagram |
| `seq_05_list_visits.mmd` | Sequence diagram |
| `class_diagram.mmd` | Class diagram |

### Generating Images

Requires `@mermaid-js/mermaid-cli` and `puppeteer` (used headlessly to render diagrams):

```bash
npm install @mermaid-js/mermaid-cli puppeteer
```

**Single file:**

```bash
npx mmdc -i diagrams/class_diagram.mmd -o diagrams/class_diagram.png
```

**All files at once:**

```bash
for f in diagrams/*.mmd; do
  npx mmdc -i "$f" -o "${f%.mmd}.png"
done
```
