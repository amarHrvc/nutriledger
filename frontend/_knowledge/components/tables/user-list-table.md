# UserListTable Component

**File:** `src/views/apps/user/list/UserListTable.tsx`  
**Reusable:** Yes (swap Redux slice for different domain)  
**Type:** Presentational / Container Hybrid  

---

## Purpose

Core data grid component for user list pages. Implements TanStack React Table v8 with:
- Multi-select row selection
- Global fuzzy search
- Column sorting (asc/desc)
- Role/plan/status filters
- Client-side pagination
- Inline delete action
- View/edit/download action menu

---

## Props

| Prop              | Type              | Required | Default | Purpose                                          |
|-------------------|-------------------|----------|---------|--------------------------------------------------|
| `tableData`       | `UsersType[]`     | Yes      | —       | Array of user objects to display in table        |

---

## Required Bundle

| File                                    | Purpose                            | Category      |
|-----------------------------------------|------------------------------------|---------------|
| `src/views/apps/user/list/index.tsx`    | Page wrapper (optional, can use directly) | View          |
| `src/views/apps/user/list/TableFilters.tsx` | Filter dropdowns UI             | Component     |
| `src/views/apps/user/list/AddUserDrawer.tsx` | Add user form                  | Component     |
| `src/types/apps/userTypes.ts`           | UsersType interface                | Type          |
| `src/@core/types/index.ts`              | ThemeColor, other core types       | Type          |
| `src/configs/i18n.ts`                   | Locale type for i18n               | Config        |

---

## State Management

### Component State

- `rowSelection` - selected rows
- `data` - current table data
- `globalFilter` - search input value
- `columnFilters` - role/plan/status filters
- `sorting` - sort state
- `pagination` - page index and size
- `addUserOpen` - drawer visibility

---

## Features

- **Row Selection:** Multi-select with "select all" header checkbox
- **Columns:** 8 columns (select, user, role, plan, billing, status, actions)
- **Sorting:** Clickable column headers with asc/desc indicators
- **Filtering:** Global fuzzy search + role/plan/status dropdowns
- **Pagination:** 10/25/50 rows per page, manual page navigation
- **Row Actions:** Delete inline, view link, options menu

---

## Dependencies

### External Libraries
- `@tanstack/react-table` ^8.x
- `@tanstack/match-sorter-utils` ^8.x
- `@mui/material` ^5.x
- `react-hook-form` ^7.x
- `next/link` and `next/navigation`

### Custom Components
- `CustomTextField`, `CustomAvatar`, `OptionMenu`, `TablePaginationComponent`
- `TableFilters`, `AddUserDrawer` (sibling components)

---

## Usage

\\\	ypescript
import UserListTable from '@/views/apps/user/list/UserListTable'

export default function Page() {
  return <UserListTable tableData={users} />
}
\\\

---

## Notes

- No Redux integration (state is local)
- TanStack Table v8 headless table
- Pagination is client-side only
- Suitable for 100-500 users max
- Auto-avatar generation on user creation
