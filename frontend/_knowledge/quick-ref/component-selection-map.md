# Component Selection Navigation Map

## Quick Decision Guide

Choose your component based on your UI need. Use this map to find the right quick-ref and implementation patterns.

---

## 1. I Need a List

**Quick-Ref**: See `user-crud-quick-ref.md` for data grid and list patterns, or `dashboard-quick-ref.md` for widget lists.

**Table Types**:
| Type | Use Case | Component | Pattern |
|------|----------|-----------|---------|
| **Data Table** | Sortable, filterable tabular data | `<Table>` + hooks | Pagination + sorting |
| **Simple List** | Basic list of items | `<ul>` semantic HTML | Map items with key |
| **Action List** | List with per-item actions | Custom + buttons | onClick handlers |
| **Selectable List** | Multiple/single selection | Checkbox/radio + state | useOptimistic for UX |
| **Virtual List** | 1000+ items performance | `react-window` | Virtualization pattern |

---

## 2. I Need a Form

**Quick-Ref**: See `forms-quick-ref.md` for validation and submission patterns.

**React Hook Form Pattern**:
```typescript
import { useForm } from 'react-hook-form'
import { useFormStatus } from 'react-dom'

interface FormData { name: string; email: string }

export function MyForm() {
  const { register, handleSubmit, formState: { errors } } = useForm<FormData>()
  
  const onSubmit = async (data: FormData) => {
    await submitToServer(data)
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)}>
      <input {...register('name', { required: true })} />
      {errors.name && <span>Required</span>}
      <SubmitButton />
    </form>
  )
}

function SubmitButton() {
  const { pending } = useFormStatus()
  return <button disabled={pending}>{pending ? 'Saving...' : 'Submit'}</button>
}
```

---

## 3. I Need a Chart

**Quick-Ref**: See `dashboard-quick-ref.md` for chart setup and integration.

**Chart Types**:
| Type | Use Case | Library | Component |
|------|----------|---------|-----------|
| **Line Chart** | Time-series, trends | Recharts | `<LineChart>` |
| **Bar Chart** | Categorical comparison | Recharts | `<BarChart>` |
| **Pie Chart** | Percentage breakdown | Recharts | `<PieChart>` |
| **Area Chart** | Cumulative trends | Recharts | `<AreaChart>` |
| **Combo Chart** | Multiple metrics | Recharts | `<ComposedChart>` |

---

## 4. I Need a Card/Widget

**Quick-Ref**: See `dashboard-quick-ref.md` for widget patterns and layouts.

**Widget Catalog**:
| Widget | Purpose | Pattern | Container |
|--------|---------|---------|-----------|
| **Stats Card** | Display KPI metrics | Icon + number | `<div className="card">` |
| **Chart Card** | Embedded chart | Card header + chart | Recharts inside card |
| **List Card** | Grouped list items | Title + table/list | Scrollable container |
| **Form Card** | Inline form editing | Card + form inputs | Actions at bottom |
| **Profile Card** | User/entity summary | Avatar + details | Fixed width layout |

---

## 5. I Need a Dialog/Drawer

**Quick-Ref**: See component naming patterns below.

**Component Naming Patterns**:
- **Dialog Modal**: `<DialogName>Modal` or `<DialogNameDialog>`
- **Drawer Sidebar**: `<DrawerName>Drawer` or `<DrawerNameSidebar>`
- **Popover/Tooltip**: `<TriggerName>Popover` or `<TooltipName>Tooltip`
- **Confirmation**: `<ConfirmActionDialog>` (follows dialog pattern)

---

## Component Selection Reference Table

| Need | Component | Quick-Ref | Implementation File |
|------|-----------|-----------|---------------------|
| Display tabular data | `<Table>` | user-crud-quick-ref.md | `components/tables/` |
| Simple list display | Semantic `<ul>` | user-crud-quick-ref.md | Component file |
| Form submission | `<form>` + RHF | forms-quick-ref.md | `components/forms/` |
| Line/area trends | `<LineChart>` | dashboard-quick-ref.md | `components/charts/` |
| Bar comparisons | `<BarChart>` | dashboard-quick-ref.md | `components/charts/` |
| Pie breakdown | `<PieChart>` | dashboard-quick-ref.md | `components/charts/` |
| KPI metric | Stats Card | dashboard-quick-ref.md | `components/widgets/` |
| Modal prompt | Dialog Component | component naming patterns | `components/dialogs/` |
| Side panel | Drawer Component | component naming patterns | `components/drawers/` |
| User summary | Profile Card | dashboard-quick-ref.md | `components/widgets/` |

---

## Quick Tips

- ✅ Check the **Quick-Ref** file before implementing
- ✅ Look for existing patterns in `components/` catalog
- ✅ Use TypeScript types for all props
- ✅ Test accessibility (keyboard nav, ARIA labels)
- ✅ Reference examples in the implementation files
