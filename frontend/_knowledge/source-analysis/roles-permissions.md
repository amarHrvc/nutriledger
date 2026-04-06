# Roles & Permissions Domain - Source Code Analysis

**Epic:** kb-joc.1.2  
**Task:** T013 - Read roles & permissions domain source  
**Date:** 2026-05-04  
**Status:** Complete

## Overview

The Roles & Permissions domain manages role-based access control (RBAC) and fine-grained permissions within the admin application. It provides interfaces for managing roles, assigning permissions to roles, and viewing user-role associations.

## Directory Structure

```
src/views/apps/
├── roles/
│   ├── index.tsx              # Roles page container
│   ├── RoleCards.tsx          # Role cards display with visual design
│   └── RolesTable.tsx         # Roles table with user associations
└── permissions/
    └── index.tsx              # Permissions page with table

src/types/apps/
└── permissionTypes.ts         # Permission type definitions
```

## Architecture Pattern

The Roles & Permissions domain follows a **RBAC (Role-Based Access Control)** pattern with:
- **Roles:** Named groups of permissions
- **Permissions:** Individual actions/resources that can be granted
- **Users:** Associated with one or more roles
- **Role Cards:** Visual representation of existing roles with user counts
- **Role Tables:** Detailed view of users and their assigned roles
- **Permissions Table:** Management of individual permissions

## Core Components Analysis

### 1. Roles Domain

#### Roles Page (index.tsx)

**Type:** Container Component  
**Purpose:** Orchestrates role display and management

**Structure:**
```typescript
Props: {
  userData?: UsersType[]
}

Layout:
- Typography header with description
- RoleCards component (role overview)
- Typography header for user-role associations
- RolesTable component (user-role listing)
```

**Description Text:**
- "A role provided access to predefined menus and features so that depending on assigned role an administrator can have access to what he need"
- Focus on administrative access management through roles

#### RoleCards.tsx (Lines 1-112)

**Type:** Client Component (`'use client'`)  
**Purpose:** Visual display of existing roles with action buttons

**Key Features:**
- **Card Grid:** Responsive grid layout (xs: 12, sm: 6, lg: 4)
- **Role Summary:** Shows total users and user avatars per role
- **Visual Design:** Includes illustration graphics
- **Action Options:** Edit role, copy role buttons
- **Add Role Card:** Special card for adding new roles

**Pre-defined Roles:**
```typescript
const cardData: CardDataType[] = [
  { totalUsers: 4, title: 'Administrator', avatars: ['1.png', '2.png', '3.png', '4.png'] },
  { totalUsers: 7, title: 'Editor', avatars: ['5.png', '6.png', '7.png'] },
  { totalUsers: 5, title: 'Users', avatars: ['4.png', '5.png', '6.png'] },
  { totalUsers: 6, title: 'Support', avatars: ['1.png', '2.png', '3.png'] },
  { totalUsers: 10, title: 'Restricted User', avatars: ['4.png', '5.png', '6.png'] }
]
```

**Role Types Supported:**
1. Administrator
2. Editor
3. Users
4. Support
5. Restricted User

**UI Components:**
- Card with illustration graphic
- AvatarGroup for showing users
- Typography for role name and user count
- Edit Role link (styled as primary colored text)
- Copy Role icon button
- Add Role button for new role creation

**Dialog Integration:**
- Uses `RoleDialog` for editing existing roles
- Uses `RoleDialog` for creating new roles
- Powered by `OpenDialogOnElementClick` HOC pattern

**Card Layout Details:**
```
Card
├── Grid (2 parts)
│   ├── Grid XS:5 (Image/Illustration)
│   └── Grid XS:7 (Content)
│       ├── Button "Add Role"
│       └── Typography description
└── CardContent
    ├── User count + AvatarGroup
    ├── Role title + "Edit Role" link
    └── Copy button icon
```

#### RolesTable.tsx (Lines 1-432)

**Type:** Client Component (`'use client'`)  
**Purpose:** Table display of users and their associated roles

**Key Features:**
- **TanStack Powered:** Same react-table v8 implementation as UserListTable
- **Fuzzy Search:** Full-text search with debouncing
- **Role Filter:** Dropdown filter for specific roles
- **Row Selection:** Multi-select with select-all checkbox
- **Sorting:** Column-based sorting
- **Pagination:** Configurable page size (10, 25, 50)

**State Management:**
```typescript
- role: UsersType['role']      // Role filter value
- rowSelection: Record<>       // Selected rows
- data: UsersType[]           // Current data
- filteredData: UsersType[]   // Display data
- globalFilter: string        // Search query
```

**Columns (6 total):**
1. **Select** - Checkbox for row selection
2. **User** - Full name + username with avatar (skin='light')
3. **Role** - Icon-coded role with color mapping
4. **Plan** - Subscription plan
5. **Billing** - Billing method
6. **Status** - Chip-based status
7. **Actions** - Delete, view, and menu options

**Role Icon Mapping (userRoleObj):**
```typescript
{
  admin: { icon: 'tabler-crown', color: 'primary' },
  author: { icon: 'tabler-device-desktop', color: 'error' },
  editor: { icon: 'tabler-edit', color: 'warning' },
  maintainer: { icon: 'tabler-chart-pie', color: 'info' },
  subscriber: { icon: 'tabler-user', color: 'success' }
}
```

**Status Mapping (userStatusObj):**
```typescript
{
  active: 'success',
  pending: 'warning',
  inactive: 'secondary'
}
```

**Filtering Logic:**
- Role filter updates via `useEffect`
- Filters combine with AND logic
- Empty role value = show all roles

**TanStack Configuration:**
```typescript
- enableRowSelection: true
- globalFilterFn: fuzzyFilter
- initialState: { pagination: { pageSize: 10 } }
- Row models: Core, Filtered, Sorted, Paginated
- Faceted models: Unique values, Min/Max
```

**Dependencies:**
- MUI: Card, CardContent, MenuItem, Chip, Typography, Checkbox, IconButton, TablePagination
- React Table: Full suite of utilities
- Custom: CustomAvatar, OptionMenu, CustomTextField, TablePaginationComponent

### 2. Permissions Domain

#### Permissions Page (index.tsx) (Lines 1-332)

**Type:** Client Component (`'use client'`)  
**Purpose:** Manage individual permissions with dialog-based editing

**Key Features:**
- **TanStack Table:** Advanced table with filtering and pagination
- **Permission Search:** Debounced fuzzy search
- **Pagination:** Page size selector (5, 7, 9)
- **Dialog Editing:** Modal dialog for add/edit operations
- **Dynamic Action Types:** Supports both single and multi-value assignments

**State Management:**
```typescript
- open: boolean                      // Dialog visibility
- rowSelection: Record<>             // Selected rows
- editValue: string                  // Current editing permission name
- data: PermissionRowType[]          // Current data
- globalFilter: string               // Search query
```

**Columns (4 total):**
1. **Name** - Permission name
2. **Assigned To** - Single or multiple role assignments (chips)
3. **Created Date** - Creation timestamp
4. **Actions** - Edit button + menu button

**Color Mapping for Assigned Roles (colors obj):**
```typescript
{
  support: 'info',
  users: 'success',
  manager: 'warning',
  administrator: 'primary',
  'restricted-user': 'error'
}
```

**Assigned To Logic:**
- Can be single string: `"admin"` → Single chip
- Can be array: `["admin", "editor"]` → Multiple chips
- Chips display in tonal variant with capitalization

**Dialog Handling:**
- `handleEditPermission(name)` - Opens dialog with permission name
- `handleAddPermission()` - Clears edit value and opens dialog
- Uses `OpenDialogOnElementClick` for dynamic button-to-dialog binding

**Button Configuration:**
```typescript
const buttonProps: ButtonProps = {
  variant: 'contained',
  children: 'Add Permission',
  onClick: () => handleAddPermission(),
  className: 'max-sm:is-full',
  startIcon: <i className='tabler-plus' />
}
```

**TanStack Configuration:**
```typescript
- initialState: { pagination: { pageSize: 9 } }
- enableRowSelection: true
- globalFilterFn: fuzzyFilter
- Row models: Core, Filtered, Sorted, Paginated
- Faceted models: Unique values, Min/Max
```

**Dependencies:**
- MUI: Card, CardContent, Button, Typography, Chip, TablePagination, IconButton, MenuItem
- React Table: Full suite of utilities
- Custom: PermissionDialog, OpenDialogOnElementClick, CustomTextField, TablePaginationComponent

## Type Definitions

### PermissionRowType (`src/types/apps/permissionTypes.ts`)

```typescript
type PermissionRowType = {
  id: number
  name: string                    // Permission identifier
  createdDate: string            // ISO or formatted date string
  assignedTo: string | string[]  // Single role or array of roles
}
```

### UsersType (Reference)

Used in RolesTable for user data. See user-domain.md for full details.

## RBAC Architecture Patterns

### 1. Role Hierarchy
- Administrator (top-level access)
- Editor (content management)
- Users (standard user access)
- Support (support staff)
- Restricted User (limited access)

### 2. Permission Model
- Permissions are atomic actions/resources
- Assigned to roles (single or multiple roles)
- Color-coded by assignment type
- Trackable by creation date

### 3. User-Role Association
- Users have roles (from UsersType.role)
- Roles have permissions (from Permissions table)
- Displayed in RolesTable for visibility

## Dialog Integration Pattern

Both components use a common pattern:
```typescript
<OpenDialogOnElementClick
  element={Component}
  elementProps={props}
  dialog={DialogComponent}
  dialogProps={dialogProps}
/>
```

This enables:
- Click-to-open dialog pattern
- Conditional dialog data passing
- Clean separation of concerns
- Reusable dialog for multiple operations

## Customization Points

### Adding Roles
1. Add entry to `cardData` array in RoleCards
2. Update RolesTable if additional role properties needed
3. Update role mappings (icon, color)

### Adding Permissions
1. Add row to permission data source
2. Update color mapping if new assigned roles
3. Dialog handles the form logic

### Updating Color Schemes
- `colors` object in Permissions for role colors
- `userRoleObj` in RolesTable for role icons/colors
- `userStatusObj` in RolesTable for status colors

## State Management Patterns

### Role Cards
- **No State:** Pure presentation based on hardcoded data
- **Static Data:** cardData array defines roles

### Roles Table
- **Reactive Filtering:** useEffect watches role filter state
- **Multi-filter:** Combines search + role filter
- **Data Mutations:** setData() for delete operations

### Permissions Table
- **Dialog State:** Edit value passed to dialog
- **Search + Pagination:** Independent state management
- **Flexible Assignment:** Handles string or array values

## Performance Considerations

1. **useMemo for Columns:** Both tables memoize column definitions
2. **DebouncedInput:** 500ms debounce on searches
3. **TanStack Optimizations:** Row models computed by library
4. **Static Data:** RoleCards uses hardcoded data (no fetching)

## Integration Points

### Navigation
- RoleCards and RolesTable on same page
- Links to user detail page from table
- Dialog-based editing for add/edit operations

### Data Flow
```
Roles Page
├── RoleCards (static display)
└── RolesTable (filtered user-role view)

Permissions Page
└── Permissions Table (with dialog editing)
    ├── DebouncedInput (search)
    ├── Page size selector
    └── PermissionDialog (add/edit form)
```

## Accessibility Notes

- **Native Checkboxes:** Used for row selection
- **Semantic Tables:** Proper table structure
- **Color + Text:** Role names included with color codes
- **Icon + Text:** Icons paired with text labels
- **Button Variants:** Clear action buttons

## Known Patterns

### Reusable Components Pattern
- DebouncedInput component used in both domains
- fuzzyFilter function used consistently
- TablePaginationComponent standardized

### Dialog-on-Click Pattern
- OpenDialogOnElementClick wrapper component
- Decouples dialog trigger from dialog logic
- Supports both add and edit modes

### Chip-based Status Display
- Role/status information in chips
- Color-coded for quick visual scanning
- Tonal variant for secondary information

## Summary

The Roles & Permissions domain implements:
- **RBAC System:** Hierarchical role structure with 5 predefined roles
- **Permission Management:** Individual permissions linked to roles (1:1 or 1:many)
- **User-Role Mapping:** Display of user-to-role associations via RolesTable
- **Advanced Tables:** TanStack-powered tables with search, filter, sort, and pagination
- **Dialog Integration:** Modal-based editing for roles and permissions
- **Visual Design:** Role cards with avatars and statistics
- **Color Coding:** Semantic colors for roles and status values

The architecture emphasizes clarity in access control with visual and tabular representations of role hierarchies and permissions.