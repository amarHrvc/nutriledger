# Roles & Permissions Tables

**File:** `src/views/apps/roles-permissions/` (Conceptual, not yet implemented)  
**Reusable:** Yes (when implemented)  
**Type:** Admin / Configuration Tables  

---

## Purpose

These tables would manage:
- **Roles Table**: CRUD for user roles (Admin, Editor, Viewer, etc.)
- **Permissions Table**: CRUD for system permissions (Create, Read, Update, Delete, etc.)
- **Role-Permission Mapping**: Assignment matrix

---

## Proposed Structure

### RolesTable Props

`	ypescript
interface RolesTableProps {
  tableData: RoleType[]
  onRoleChange?: (role: RoleType) => void
}

type RoleType = {
  id: number
  name: string
  description: string
  permissions: number[]
  usersCount: number
  createdDate: string
}
`

### PermissionsTable Props

`	ypescript
interface PermissionsTableProps {
  tableData: PermissionType[]
}

type PermissionType = {
  id: number
  name: string
  module: string
  description: string
  rolesCount: number
}
`

---

## Features (When Implemented)

- Multi-select row selection
- Fuzzy search (role name, permission name)
- Column sorting
- Inline edit/delete actions
- Drawer for adding new roles/permissions
- Permission assignment matrix (role to permissions)

---

## Status

**Not yet implemented in current Vuexy template.** This is a placeholder for future admin/settings feature expansion.

---

## Reusability

When implemented, will follow UserListTable/ProductListTable patterns with TanStack React Table v8.
