# Users List Page Snapshot

**Route:** /apps/user/list  
**View:** src/views/apps/user/list/  
**Layout:** Dashboard (sidebar nav)  
**Auth:** Required  

---

## Page Structure

​​
UserList (index.tsx)
├── UserListCards          — stats/summary cards (optional, renders empty)
└── UserListTable          — main table with embedded controls
    ├── TableFilters       — role/plan/status filter dropdowns
    ├── DebouncedInput     — global search box
    ├── TanStack Table     — core data grid with sorting/filtering/pagination
    │   ├── Selection cols (checkbox)
    │   ├── User col       (avatar + fullName + username)
    │   ├── Role col       (icon + role name, colored)
    │   ├── Plan col       (plan name)
    │   ├── Billing col    (billing type)
    │   ├── Status col     (status chip, colored)
    │   └── Actions col    (delete, view, options menu)
    └── AddUserDrawer      — right-side form with React Hook Form
​​

---

## Components Used

| Component         | File                            | Purpose                                          | Props                      |
|-------------------|---------------------------------|--------------------------------------------------|----------------------------|
| **UserListTable** | UserListTable.tsx               | Main table grid with all features                | 	ableData: UsersType[]    |
| **TableFilters**  | TableFilters.tsx                | Filter UI: role, plan, status dropdowns          | setData, tableData        |
| **AddUserDrawer** | AddUserDrawer.tsx               | Add user form, React Hook Form + validation      | open, handleClose, userData, setData |
| **UserListCards** | UserListCards.tsx               | Summary stats (not fully implemented)            | none                       |

---

## Key Features

### Table (TanStack React Table v8)
- **Selection:** Multi-select with "select all" header checkbox
- **Columns:** 8 columns (select, user, role, plan, billing, status, actions)
- **Sorting:** Clickable column headers with asc/desc indicators
- **Filtering:** 
  - Global fuzzy search (DebouncedInput, 500ms debounce)
  - Role/plan/status dropdowns (TableFilters component)
- **Pagination:** 10/25/50 rows per page, manual page navigation
- **Row Actions:** Delete inline, view link, options menu (download/edit)

### Filters (TableFilters)
- **Role:** admin, author, editor, maintainer, subscriber
- **Plan:** basic, company, enterprise, team
- **Status:** pending, active, inactive
- **Update:** Live filter on state change, synced to table

### Add User (AddUserDrawer)
- **Validation:** fullName, username, email, role, plan, status (all required)
- **Optional:** company, country, contact
- **Auto-gen:** avatar (random 1–8), billing (inherit from table)
- **Submit:** Appends new user to table data, closes drawer

---

## State Management

**Redux:** None. All state is local to the components.

**Local State in UserListTable:**
- owSelection — selected rows
- data — current table data
- ilteredData — after filter/search
- globalFilter — search input value
- ddUserOpen — drawer visibility

**State in TableFilters:**
- ole, plan, status — filter selections (computed on change)

**State in AddUserDrawer:**
- ormData — optional fields (company, country, contact)
- React Hook Form — validated form fields

---

## Data Type

​​	ypescript
type UsersType = {
  id: number
  role: string                  // admin | author | editor | maintainer | subscriber
  email: string
  status: string                // active | pending | inactive
  avatar: string                // img path
  company: string
  country: string
  contact: string
  fullName: string
  username: string
  currentPlan: string           // basic | company | enterprise | team
  avatarColor?: ThemeColor      // optional
  billing: string               // "Auto Debit" etc
}
​​

---

## Styling

- **MUI Components:** Card, Grid, CardHeader, Button, Chip, Checkbox, IconButton, Drawer
- **Custom Components:** CustomTextField (MUI TextField wrapper), CustomAvatar
- **Table:** 	ableStyles.table (CSS module) — semantic <table> with <thead>, <tbody>
- **Utility Classes:** Flexbox (lex, items-center, gap-4), spacing (padding/margin)

---

## Dependencies

### Direct Imports
- @mui/material/* — Card, Grid, Button, Chip, etc.
- @tanstack/react-table — useReactTable, column helpers, filtering models
- @tanstack/match-sorter-utils — rankItem for fuzzy search
- eact-hook-form — useForm, Controller for AddUserDrawer form
- 
ext/link — Link component for view action
- 
ext/navigation — useParams for locale

### Custom Components
- CustomTextField — MUI TextField wrapper
- CustomAvatar — MUI Avatar wrapper
- OptionMenu — options dropdown menu
- TablePaginationComponent — pagination controls

### Utilities
- getInitials(fullName) — extract initials for avatar
- getLocalizedUrl(path, locale) — i18n-aware routing

### Types
- UsersType — from @/types/apps/userTypes
- ThemeColor — from @core/types
- Locale — from @configs/i18n

---

## Notes

- No Redux integration; designed for mock/static data demo
- TanStack Table v8 (headless, 100% custom rendering)
- Drawer state (add/edit) managed at component level, not global
- Filter logic is React-based; could be moved to API/server-side
- Pagination is client-side; page size changes reset to page 0
- No animations or transitions
- Fully responsive: flexbox layout adapts to mobile
