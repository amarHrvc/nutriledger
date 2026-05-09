# Quickstart: Admin User Management

**Branch**: `008-admin-user-management` | **Date**: 2026-05-01  
**Stack**: Next.js 14 App Router · MUI v7 · TanStack Table v8 · React Hook Form · Orval v8.7.0  
**Pattern**: BFF (Next.js route handler) → Orval → Laravel REST API  
**Reference corpus**: `_graphify_corpus/FE/views/apps/user/list/` (UserListTable, AddUserDrawer)

---

## Vuexy Component Conventions (follow throughout)

| Always use | Instead of |
|---|---|
| `CustomTextField` from `@core/components/mui/TextField` | raw MUI `TextField` |
| `CustomAvatar` from `@core/components/mui/Avatar` | raw MUI `Avatar` |
| `Chip variant='tonal'` | `Chip variant='outlined'` |
| `<i className='tabler-...' />` | MUI icon components |
| `OptionMenu` from `@core/components/option-menu` | custom dropdown menus |
| `tableStyles.table` from `@core/styles/table.module.css` | raw MUI `Table` |
| `toast.success / toast.error` (react-toastify, already mounted) | inline `<Typography color='error'>` |
| React Hook Form `Controller` | controlled `useState` form fields |
| `Drawer` (right-anchor) | separate form pages |

---

## Architecture Overview

```
page.tsx (thin server wrapper)
└── UsersPage / index.tsx (client, owns drawer open states)
    ├── UserListTable.tsx      ← TanStack Table, DebouncedInput, OptionMenu
    ├── AddEditUserDrawer.tsx  ← right-side Drawer, React Hook Form
    ├── UserDetailDrawer.tsx   ← right-side Drawer, user info + actions
    └── ConfirmationDialog.tsx ← MUI Dialog for destructive actions
```

BFF routes mirror this feature's 7 operations under `app/api/users/`.

---

## Prerequisites

- Branch `008-admin-user-management` checked out
- Dev server running (`npm run dev` in `frontend/`)
- Admin seed account available

---

## T001 — BFF Routes: List + Create

**Goal**: `GET /api/users?page=N` and `POST /api/users` as Next.js BFF routes.  
**Inputs**: `usersIndex`, `usersStore` from `src/api/generated/user/user.ts`  
**Output**: `src/app/api/users/route.ts`

```ts
// frontend/src/app/api/users/route.ts
import { cookies } from 'next/headers'
import { NextResponse } from 'next/server'
import { getUsersIndexUrl, usersStore } from '@/api/generated/user/user'
import type { StoreUserRequest } from '@/api/generated/nutriBaseAPI.schemas'

async function getToken() {
  const cookieStore = await cookies()
  return cookieStore.get('auth_token')?.value?.split('|')[1] ?? null
}

export async function GET(request: Request) {
  const token = await getToken()
  if (!token) return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })

  const { searchParams } = new URL(request.url)
  const page = searchParams.get('page') ?? '1'

  const res = await fetch(getUsersIndexUrl() + '?page=' + page, {
    method: 'GET',
    headers: { Authorization: 'Bearer ' + token, Accept: 'application/json' },
  })

  if (!res.ok) return NextResponse.json({ message: 'Failed to load users' }, { status: 502 })

  const body = await res.json()

  return NextResponse.json({
    users: (body.data as any) ?? [],
    meta: (body.meta as any) ?? {},
  })
}

export async function POST(request: Request) {
  const token = await getToken()
  if (!token) return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })

  const body: StoreUserRequest = await request.json()
  const result = await usersStore(body, {
    headers: { Authorization: 'Bearer ' + token, Accept: 'application/json' },
  })

  if (result.status === 422) return NextResponse.json(result.data, { status: 422 })
  if (result.status !== 201) return NextResponse.json({ message: 'Failed to create user' }, { status: result.status })

  return NextResponse.json((result.data as any).data, { status: 201 })
}
```

**Why `getUsersIndexUrl() + '?page='`**: Orval's `usersIndex(options?)` only accepts `RequestInit` — no typed query params. The URL helper is the single source of truth for the base URL; page is appended manually.

---

## T002 — BFF Routes: Show, Update, Soft-Delete, Restore, Force-Delete

**Outputs**:
- `src/app/api/users/[id]/route.ts`
- `src/app/api/users/[id]/restore/route.ts`
- `src/app/api/users/[id]/force/route.ts`

```ts
// frontend/src/app/api/users/[id]/route.ts
import { cookies } from 'next/headers'
import { NextResponse } from 'next/server'
import { usersShow, usersUpdate, usersDestroy } from '@/api/generated/user/user'
import type { UpdateUserRequest } from '@/api/generated/nutriBaseAPI.schemas'

async function authHeaders() {
  const cookieStore = await cookies()
  const token = cookieStore.get('auth_token')?.value?.split('|')[1]
  return token ? { Authorization: 'Bearer ' + token, Accept: 'application/json' } : null
}

export async function GET(_req: Request, { params }: { params: { id: string } }) {
  const headers = await authHeaders()
  if (!headers) return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })

  const result = await usersShow(params.id, { headers })
  if (result.status !== 200) return NextResponse.json({ message: 'Failed to load user' }, { status: result.status })

  return NextResponse.json((result.data as any).data)
}

export async function PATCH(request: Request, { params }: { params: { id: string } }) {
  const headers = await authHeaders()
  if (!headers) return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })

  const body: UpdateUserRequest = await request.json()
  const result = await usersUpdate(params.id, body, { headers })

  if (result.status === 422) return NextResponse.json(result.data, { status: 422 })
  if (result.status !== 200) return NextResponse.json({ message: 'Failed to update user' }, { status: result.status })

  return NextResponse.json((result.data as any).data)
}

export async function DELETE(_req: Request, { params }: { params: { id: string } }) {
  const headers = await authHeaders()
  if (!headers) return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })

  const result = await usersDestroy(params.id, { headers })
  if (result.status !== 204) return NextResponse.json({ message: 'Failed to deactivate user' }, { status: result.status })

  return new NextResponse(null, { status: 204 })
}
```

```ts
// frontend/src/app/api/users/[id]/restore/route.ts
import { cookies } from 'next/headers'
import { NextResponse } from 'next/server'
import { usersRestore } from '@/api/generated/user/user'

export async function POST(_req: Request, { params }: { params: { id: string } }) {
  const cookieStore = await cookies()
  const token = cookieStore.get('auth_token')?.value?.split('|')[1]
  if (!token) return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })

  const result = await usersRestore(params.id, {
    headers: { Authorization: 'Bearer ' + token, Accept: 'application/json' },
  })

  if (result.status !== 200) return NextResponse.json({ message: 'Failed to restore user' }, { status: result.status })

  return NextResponse.json((result.data as any).data)
}
```

```ts
// frontend/src/app/api/users/[id]/force/route.ts
import { cookies } from 'next/headers'
import { NextResponse } from 'next/server'
import { usersForceDelete } from '@/api/generated/user/user'

export async function DELETE(_req: Request, { params }: { params: { id: string } }) {
  const cookieStore = await cookies()
  const token = cookieStore.get('auth_token')?.value?.split('|')[1]
  if (!token) return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })

  const result = await usersForceDelete(params.id, {
    headers: { Authorization: 'Bearer ' + token, Accept: 'application/json' },
  })

  if (result.status !== 204) return NextResponse.json({ message: 'Failed to permanently delete user' }, { status: result.status })

  return new NextResponse(null, { status: 204 })
}
```

---

## T003 — ConfirmationDialog

**Goal**: Reusable MUI Dialog for destructive action confirmations.  
**Why build**: `@components/dialogs/confirmation-dialog` exists in the Vuexy corpus but is NOT present in this project's starter kit.  
**Output**: `src/views/users/ConfirmationDialog.tsx`

```tsx
// frontend/src/views/users/ConfirmationDialog.tsx
import Button from '@mui/material/Button'
import Dialog from '@mui/material/Dialog'
import DialogActions from '@mui/material/DialogActions'
import DialogContent from '@mui/material/DialogContent'
import DialogContentText from '@mui/material/DialogContentText'
import DialogTitle from '@mui/material/DialogTitle'
import type { ButtonProps } from '@mui/material/Button'

interface ConfirmationDialogProps {
  open: boolean
  title: string
  message: string
  confirmLabel?: string
  confirmColor?: ButtonProps['color']
  onConfirm: () => void
  onCancel: () => void
}

export default function ConfirmationDialog({
  open,
  title,
  message,
  confirmLabel = 'Confirm',
  confirmColor = 'primary',
  onConfirm,
  onCancel,
}: ConfirmationDialogProps) {
  return (
    <Dialog open={open} onClose={onCancel} maxWidth='xs' fullWidth>
      <DialogTitle>{title}</DialogTitle>
      <DialogContent>
        <DialogContentText>{message}</DialogContentText>
      </DialogContent>
      <DialogActions className='gap-2 pbe-5 pli-6'>
        <Button variant='tonal' color='secondary' onClick={onCancel}>
          Cancel
        </Button>
        <Button variant='contained' color={confirmColor} onClick={onConfirm}>
          {confirmLabel}
        </Button>
      </DialogActions>
    </Dialog>
  )
}
```

---

## T004 — UserListTable

**Goal**: Searchable, paginated table of all users using TanStack Table v8 and Vuexy table styles.  
**Pattern source**: `_graphify_corpus/FE/views/apps/user/list/UserListTable.tsx`  
**Key imports**: `CustomTextField`, `CustomAvatar`, `OptionMenu`, `tableStyles`, `classnames`  
**Output**: `src/views/users/UserListTable.tsx`

```tsx
// frontend/src/views/users/UserListTable.tsx
'use client'

import { useEffect, useState, useMemo } from 'react'

import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import Button from '@mui/material/Button'
import Chip from '@mui/material/Chip'
import Typography from '@mui/material/Typography'
import TablePagination from '@mui/material/TablePagination'
import MenuItem from '@mui/material/MenuItem'
import type { TextFieldProps } from '@mui/material/TextField'

import classnames from 'classnames'
import { rankItem } from '@tanstack/match-sorter-utils'
import {
  createColumnHelper,
  flexRender,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useReactTable,
} from '@tanstack/react-table'
import type { ColumnDef, FilterFn } from '@tanstack/react-table'
import type { RankingInfo } from '@tanstack/match-sorter-utils'
import { toast } from 'react-toastify'

import CustomTextField from '@core/components/mui/TextField'
import CustomAvatar from '@core/components/mui/Avatar'
import OptionMenu from '@core/components/option-menu'
import tableStyles from '@core/styles/table.module.css'
import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'

declare module '@tanstack/table-core' {
  interface FilterFns { fuzzy: FilterFn<unknown> }
  interface FilterMeta { itemRank: RankingInfo }
}

const fuzzyFilter: FilterFn<any> = (row, columnId, value, addMeta) => {
  const itemRank = rankItem(row.getValue(columnId), value)
  addMeta({ itemRank })
  return itemRank.passed
}

const DebouncedInput = ({
  value: initialValue,
  onChange,
  debounce = 400,
  ...props
}: { value: string; onChange: (v: string) => void; debounce?: number } & Omit<TextFieldProps, 'onChange'>) => {
  const [value, setValue] = useState(initialValue)

  useEffect(() => { setValue(initialValue) }, [initialValue])

  useEffect(() => {
    const timeout = setTimeout(() => onChange(value), debounce)
    return () => clearTimeout(timeout)
  }, [value])

  return <CustomTextField {...props} value={value} onChange={e => setValue(e.target.value)} />
}

const ROLE_LABELS: Record<string, string> = {
  admin: 'Administrator',
  doktor: 'Doctor',
  pacijent: 'Patient',
}

const columnHelper = createColumnHelper<UserResource>()

interface UserListTableProps {
  onViewUser: (user: UserResource) => void
  onEditUser: (user: UserResource) => void
  onAddUser: () => void
  refreshKey: number
}

export default function UserListTable({ onViewUser, onEditUser, onAddUser, refreshKey }: UserListTableProps) {
  const [users, setUsers] = useState<UserResource[]>([])
  const [globalFilter, setGlobalFilter] = useState('')
  const [isLoading, setIsLoading] = useState(true)

  const loadUsers = () => {
    setIsLoading(true)
    fetch('/api/users')
      .then(res => { if (!res.ok) throw new Error(res.statusText); return res.json() })
      .then(data => setUsers(data.users ?? []))
      .catch(() => toast.error('Failed to load users'))
      .finally(() => setIsLoading(false))
  }

  useEffect(() => { loadUsers() }, [refreshKey])

  const handleDeactivate = async (user: UserResource) => {
    const res = await fetch('/api/users/' + user.id, { method: 'DELETE' })
    if (res.ok) { toast.success(user.attributes.name + ' deactivated'); loadUsers() }
    else toast.error('Failed to deactivate user')
  }

  const handleRestore = async (user: UserResource) => {
    const res = await fetch('/api/users/' + user.id + '/restore', { method: 'POST' })
    if (res.ok) { toast.success(user.attributes.name + ' restored'); loadUsers() }
    else toast.error('Failed to restore user')
  }

  const columns = useMemo<ColumnDef<UserResource, any>[]>(() => [
    columnHelper.accessor('attributes.name', {
      header: 'User',
      cell: ({ row }) => {
        const { name, email } = row.original.attributes
        const initials = name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2)
        return (
          <div className='flex items-center gap-3'>
            <CustomAvatar size={34}>{initials}</CustomAvatar>
            <div className='flex flex-col'>
              <Typography color='text.primary' className='font-medium'>{name}</Typography>
              <Typography variant='body2' color='text.secondary'>{email}</Typography>
            </div>
          </div>
        )
      }
    }),
    columnHelper.accessor('attributes.role', {
      header: 'Role',
      cell: ({ row }) => (
        <Typography color='text.primary' className='capitalize'>
          {ROLE_LABELS[row.original.attributes.role] ?? row.original.attributes.role}
        </Typography>
      )
    }),
    columnHelper.accessor('attributes.isDeleted', {
      header: 'Status',
      cell: ({ row }) => (
        <Chip
          variant='tonal'
          label={row.original.attributes.isDeleted ? 'Deactivated' : 'Active'}
          color={row.original.attributes.isDeleted ? 'error' : 'success'}
          size='small'
        />
      )
    }),
    columnHelper.display({
      id: 'actions',
      header: 'Actions',
      cell: ({ row }) => {
        const user = row.original
        const isDeleted = user.attributes.isDeleted

        return (
          <div className='flex items-center'>
            <OptionMenu
              iconButtonProps={{ size: 'medium' }}
              iconClassName='text-textSecondary'
              options={[
                {
                  text: 'View',
                  icon: 'tabler-eye',
                  menuItemProps: {
                    className: 'flex items-center gap-2 text-textSecondary',
                    onClick: () => onViewUser(user)
                  }
                },
                {
                  text: 'Edit',
                  icon: 'tabler-edit',
                  menuItemProps: {
                    className: 'flex items-center gap-2 text-textSecondary',
                    onClick: () => onEditUser(user)
                  }
                },
                ...(!isDeleted ? [{
                  text: 'Deactivate',
                  icon: 'tabler-user-off',
                  menuItemProps: {
                    className: 'flex items-center gap-2 text-warning',
                    onClick: () => handleDeactivate(user)
                  }
                }] : [{
                  text: 'Restore',
                  icon: 'tabler-user-check',
                  menuItemProps: {
                    className: 'flex items-center gap-2 text-primary',
                    onClick: () => handleRestore(user)
                  }
                }])
              ]}
            />
          </div>
        )
      },
      enableSorting: false
    })
  ], [users])

  const table = useReactTable({
    data: users,
    columns,
    filterFns: { fuzzy: fuzzyFilter },
    state: { globalFilter },
    globalFilterFn: fuzzyFilter,
    onGlobalFilterChange: setGlobalFilter,
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    initialState: { pagination: { pageSize: 15 } },
  })

  return (
    <Card>
      <CardHeader
        title='Users'
        action={
          <Button variant='contained' startIcon={<i className='tabler-plus' />} onClick={onAddUser}>
            Add User
          </Button>
        }
      />
      <div className='flex justify-between items-center p-6 border-bs gap-4 flex-wrap'>
        <CustomTextField
          select
          value={table.getState().pagination.pageSize}
          onChange={e => table.setPageSize(Number(e.target.value))}
          className='is-[70px]'
        >
          <MenuItem value='10'>10</MenuItem>
          <MenuItem value='15'>15</MenuItem>
          <MenuItem value='25'>25</MenuItem>
        </CustomTextField>
        <DebouncedInput
          value={globalFilter}
          onChange={value => setGlobalFilter(value)}
          placeholder='Search users…'
          className='max-sm:is-full'
        />
      </div>

      <div className='overflow-x-auto'>
        <table className={tableStyles.table}>
          <thead>
            {table.getHeaderGroups().map(headerGroup => (
              <tr key={headerGroup.id}>
                {headerGroup.headers.map(header => (
                  <th key={header.id}>
                    {header.isPlaceholder ? null : (
                      <div
                        className={classnames({
                          'flex items-center': header.column.getIsSorted(),
                          'cursor-pointer select-none': header.column.getCanSort()
                        })}
                        onClick={header.column.getToggleSortingHandler()}
                      >
                        {flexRender(header.column.columnDef.header, header.getContext())}
                        {{ asc: <i className='tabler-chevron-up text-xl' />, desc: <i className='tabler-chevron-down text-xl' /> }[header.column.getIsSorted() as string] ?? null}
                      </div>
                    )}
                  </th>
                ))}
              </tr>
            ))}
          </thead>
          <tbody>
            {isLoading ? (
              <tr><td colSpan={4} className='text-center py-8'>Loading…</td></tr>
            ) : table.getFilteredRowModel().rows.length === 0 ? (
              <tr><td colSpan={4} className='text-center py-8'>No users found.</td></tr>
            ) : table.getRowModel().rows.map(row => (
              <tr key={row.id}>
                {row.getVisibleCells().map(cell => (
                  <td key={cell.id}>{flexRender(cell.column.columnDef.cell, cell.getContext())}</td>
                ))}
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <TablePagination
        component='div'
        count={table.getFilteredRowModel().rows.length}
        rowsPerPage={table.getState().pagination.pageSize}
        page={table.getState().pagination.pageIndex}
        onPageChange={(_, page) => table.setPageIndex(page)}
        onRowsPerPageChange={e => table.setPageSize(Number(e.target.value))}
        rowsPerPageOptions={[10, 15, 25]}
      />
    </Card>
  )
}
```

---

## T005 — UserDetailDrawer

**Goal**: Right-side Drawer showing full user info and action buttons.  
**Pattern**: `_graphify_corpus/FE/views/apps/user/view/user-left-overview/UserDetails.tsx`  
**Output**: `src/views/users/UserDetailDrawer.tsx`

```tsx
// frontend/src/views/users/UserDetailDrawer.tsx
'use client'

import { useState } from 'react'

import Button from '@mui/material/Button'
import Chip from '@mui/material/Chip'
import Divider from '@mui/material/Divider'
import Drawer from '@mui/material/Drawer'
import IconButton from '@mui/material/IconButton'
import Typography from '@mui/material/Typography'
import { toast } from 'react-toastify'

import CustomAvatar from '@core/components/mui/Avatar'
import ConfirmationDialog from './ConfirmationDialog'
import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'

const ROLE_LABELS: Record<string, string> = {
  admin: 'Administrator',
  doktor: 'Doctor',
  pacijent: 'Patient',
}

interface UserDetailDrawerProps {
  open: boolean
  user: UserResource | null
  onClose: () => void
  onEdit: (user: UserResource) => void
  onActionComplete: () => void
}

type PendingAction = 'deactivate' | 'restore' | 'force-delete' | null

export default function UserDetailDrawer({ open, user, onClose, onEdit, onActionComplete }: UserDetailDrawerProps) {
  const [pendingAction, setPendingAction] = useState<PendingAction>(null)

  if (!user) return null

  const { attributes: a } = user
  const initials = a.name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2)

  const handleConfirm = async () => {
    let res: Response

    if (pendingAction === 'deactivate') res = await fetch('/api/users/' + user.id, { method: 'DELETE' })
    else if (pendingAction === 'restore') res = await fetch('/api/users/' + user.id + '/restore', { method: 'POST' })
    else if (pendingAction === 'force-delete') res = await fetch('/api/users/' + user.id + '/force', { method: 'DELETE' })
    else return

    setPendingAction(null)

    if (res.ok) {
      toast.success(pendingAction === 'deactivate' ? 'User deactivated' : pendingAction === 'restore' ? 'User restored' : 'User permanently deleted')
      onClose()
      onActionComplete()
    } else {
      toast.error('Action failed')
    }
  }

  const confirmConfig: Record<NonNullable<PendingAction>, { title: string; message: string; label: string; color: 'error' | 'warning' | 'primary' }> = {
    deactivate: { title: 'Deactivate User', message: `Deactivate ${a.name}? They will lose access until restored.`, label: 'Deactivate', color: 'warning' },
    restore: { title: 'Restore User', message: `Restore ${a.name}? They will regain system access.`, label: 'Restore', color: 'primary' },
    'force-delete': { title: 'Permanently Delete', message: `This will permanently delete ${a.name}. This cannot be undone.`, label: 'Delete Permanently', color: 'error' },
  }

  const DetailRow = ({ label, value }: { label: string; value: string | null | undefined }) => (
    <div className='flex items-center flex-wrap gap-x-1.5'>
      <Typography className='font-medium' color='text.primary'>{label}:</Typography>
      <Typography color={value ? 'text.primary' : 'text.disabled'} fontStyle={value ? 'normal' : 'italic'}>
        {value ?? 'Not provided'}
      </Typography>
    </div>
  )

  return (
    <>
      <Drawer
        open={open}
        anchor='right'
        variant='temporary'
        onClose={onClose}
        ModalProps={{ keepMounted: true }}
        sx={{ '& .MuiDrawer-paper': { width: { xs: 300, sm: 400 } } }}
      >
        <div className='flex items-center justify-between plb-5 pli-6'>
          <Typography variant='h5'>User Details</Typography>
          <IconButton size='small' onClick={onClose}>
            <i className='tabler-x text-2xl text-textPrimary' />
          </IconButton>
        </div>
        <Divider />

        <div className='flex flex-col items-center gap-4 pbs-8 pli-6'>
          <CustomAvatar size={80}>{initials}</CustomAvatar>
          <div className='flex flex-col items-center gap-1 text-center'>
            <Typography variant='h5'>{a.name}</Typography>
            <Typography variant='body2' color='text.secondary'>{a.email}</Typography>
          </div>
          <div className='flex gap-2'>
            <Chip label={ROLE_LABELS[a.role] ?? a.role} color='primary' size='small' variant='tonal' />
            <Chip label={a.isDeleted ? 'Deactivated' : 'Active'} color={a.isDeleted ? 'error' : 'success'} size='small' variant='tonal' />
          </div>
        </div>

        <Divider className='mlb-4' />

        <div className='flex flex-col gap-2 pli-6'>
          <Typography variant='h6'>Details</Typography>
          <Divider className='mlb-2' />
          <DetailRow label='Created' value={a.createdAt} />
          <DetailRow label='Updated' value={a.updatedAt} />
          {a.isDeleted && <DetailRow label='Deactivated At' value={a.deletedAt} />}
          {user.relationships.patient && (
            <DetailRow label='Patient Record' value='Linked' />
          )}
        </div>

        <div className='flex flex-col gap-2 p-6 mbs-auto'>
          <Button variant='contained' fullWidth startIcon={<i className='tabler-edit' />} onClick={() => { onClose(); onEdit(user) }}>
            Edit
          </Button>
          {!a.isDeleted && (
            <Button variant='tonal' color='warning' fullWidth startIcon={<i className='tabler-user-off' />} onClick={() => setPendingAction('deactivate')}>
              Deactivate
            </Button>
          )}
          {a.isDeleted && (
            <>
              <Button variant='tonal' color='primary' fullWidth startIcon={<i className='tabler-user-check' />} onClick={() => setPendingAction('restore')}>
                Restore
              </Button>
              <Button variant='tonal' color='error' fullWidth startIcon={<i className='tabler-trash' />} onClick={() => setPendingAction('force-delete')}>
                Delete Permanently
              </Button>
            </>
          )}
        </div>
      </Drawer>

      {pendingAction && (
        <ConfirmationDialog
          open
          title={confirmConfig[pendingAction].title}
          message={confirmConfig[pendingAction].message}
          confirmLabel={confirmConfig[pendingAction].label}
          confirmColor={confirmConfig[pendingAction].color}
          onConfirm={handleConfirm}
          onCancel={() => setPendingAction(null)}
        />
      )}
    </>
  )
}
```

---

## T006 — AddEditUserDrawer

**Goal**: Right-side Drawer for creating and editing users. React Hook Form + Controller + CustomTextField.  
**Pattern**: `_graphify_corpus/FE/views/apps/user/list/AddUserDrawer.tsx`  
**Output**: `src/views/users/AddEditUserDrawer.tsx`

```tsx
// frontend/src/views/users/AddEditUserDrawer.tsx
'use client'

import Button from '@mui/material/Button'
import Divider from '@mui/material/Divider'
import Drawer from '@mui/material/Drawer'
import IconButton from '@mui/material/IconButton'
import MenuItem from '@mui/material/MenuItem'
import Typography from '@mui/material/Typography'
import { useForm, Controller } from 'react-hook-form'
import { toast } from 'react-toastify'

import CustomTextField from '@core/components/mui/TextField'
import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'

interface FormValues {
  name: string
  email: string
  password: string
  password_confirmation: string
  role: string
}

interface AddEditUserDrawerProps {
  open: boolean
  user?: UserResource | null     // null/undefined = create mode
  onClose: () => void
  onSaved: () => void
}

export default function AddEditUserDrawer({ open, user, onClose, onSaved }: AddEditUserDrawerProps) {
  const isEdit = !!user

  const { control, handleSubmit, reset, setError, formState: { errors, isSubmitting } } = useForm<FormValues>({
    defaultValues: {
      name: user?.attributes.name ?? '',
      email: user?.attributes.email ?? '',
      password: '',
      password_confirmation: '',
      role: user?.attributes.role ?? 'doktor',
    }
  })

  // Reset form when user prop changes (switching between create/edit)
  const handleClose = () => { reset(); onClose() }

  const onSubmit = async (data: FormValues) => {
    const payload: Record<string, string> = { name: data.name, email: data.email, role: data.role }
    if (!isEdit) { payload.password = data.password; payload.password_confirmation = data.password_confirmation }
    else if (data.password) { payload.password = data.password }

    const res = await fetch(isEdit ? '/api/users/' + user!.id : '/api/users', {
      method: isEdit ? 'PATCH' : 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    })

    const body = await res.json()

    if (res.status === 422) {
      const fieldErrors: Record<string, string[]> = body.errors ?? {}
      Object.entries(fieldErrors).forEach(([field, messages]) => {
        setError(field as keyof FormValues, { message: messages[0] })
      })
      return
    }

    if (!res.ok) { toast.error(body.message ?? 'Something went wrong'); return }

    toast.success(isEdit ? 'User updated' : 'User created')
    handleClose()
    onSaved()
  }

  return (
    <Drawer
      open={open}
      anchor='right'
      variant='temporary'
      onClose={handleClose}
      ModalProps={{ keepMounted: true }}
      sx={{ '& .MuiDrawer-paper': { width: { xs: 300, sm: 400 } } }}
    >
      <div className='flex items-center justify-between plb-5 pli-6'>
        <Typography variant='h5'>{isEdit ? 'Edit User' : 'Add New User'}</Typography>
        <IconButton size='small' onClick={handleClose}>
          <i className='tabler-x text-2xl text-textPrimary' />
        </IconButton>
      </div>
      <Divider />

      <form onSubmit={handleSubmit(onSubmit)} className='flex flex-col gap-6 p-6'>
        <Controller
          name='name'
          control={control}
          rules={{ required: 'Name is required' }}
          render={({ field }) => (
            <CustomTextField
              {...field}
              fullWidth
              label='Full Name'
              placeholder='Ana Kovač'
              error={!!errors.name}
              helperText={errors.name?.message}
            />
          )}
        />
        <Controller
          name='email'
          control={control}
          rules={{ required: 'Email is required' }}
          render={({ field }) => (
            <CustomTextField
              {...field}
              fullWidth
              type='email'
              label='Email'
              placeholder='ana@nutribase.com'
              error={!!errors.email}
              helperText={errors.email?.message}
            />
          )}
        />
        <Controller
          name='role'
          control={control}
          rules={{ required: 'Role is required' }}
          render={({ field }) => (
            <CustomTextField
              {...field}
              select
              fullWidth
              label='Role'
              error={!!errors.role}
              helperText={errors.role?.message}
            >
              <MenuItem value='admin'>Administrator</MenuItem>
              <MenuItem value='doktor'>Doctor</MenuItem>
              <MenuItem value='pacijent'>Patient</MenuItem>
            </CustomTextField>
          )}
        />
        <Controller
          name='password'
          control={control}
          rules={!isEdit ? { required: 'Password is required', minLength: { value: 8, message: 'Minimum 8 characters' } } : {}}
          render={({ field }) => (
            <CustomTextField
              {...field}
              fullWidth
              type='password'
              label={isEdit ? 'New Password (leave blank to keep)' : 'Password'}
              error={!!errors.password}
              helperText={errors.password?.message}
            />
          )}
        />
        {!isEdit && (
          <Controller
            name='password_confirmation'
            control={control}
            rules={{ required: 'Please confirm password' }}
            render={({ field }) => (
              <CustomTextField
                {...field}
                fullWidth
                type='password'
                label='Confirm Password'
                error={!!errors.password_confirmation}
                helperText={errors.password_confirmation?.message}
              />
            )}
          />
        )}

        <div className='flex items-center gap-4'>
          <Button variant='contained' type='submit' disabled={isSubmitting}>
            {isSubmitting ? 'Saving…' : isEdit ? 'Save Changes' : 'Create User'}
          </Button>
          <Button variant='tonal' color='error' type='reset' onClick={handleClose}>
            Cancel
          </Button>
        </div>
      </form>
    </Drawer>
  )
}
```

---

## T007 — Page Entry Point + View Orchestrator

**Goal**: Wire list table + drawers together. Add page entry.  
**Outputs**: `src/views/users/index.tsx`, `src/app/(dashboard)/dashboard/users/page.tsx`

```tsx
// frontend/src/views/users/index.tsx
'use client'

import { useState } from 'react'

import UserListTable from './UserListTable'
import UserDetailDrawer from './UserDetailDrawer'
import AddEditUserDrawer from './AddEditUserDrawer'
import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'

export default function UsersPage() {
  const [refreshKey, setRefreshKey] = useState(0)
  const [detailUser, setDetailUser] = useState<UserResource | null>(null)
  const [editUser, setEditUser] = useState<UserResource | null | undefined>(undefined) // undefined = closed, null = create
  const [detailOpen, setDetailOpen] = useState(false)
  const [editOpen, setEditOpen] = useState(false)

  const refresh = () => setRefreshKey(k => k + 1)

  const openDetail = (user: UserResource) => { setDetailUser(user); setDetailOpen(true) }
  const openEdit = (user?: UserResource) => { setEditUser(user ?? null); setEditOpen(true) }

  return (
    <>
      <UserListTable
        refreshKey={refreshKey}
        onViewUser={openDetail}
        onEditUser={openEdit}
        onAddUser={() => openEdit()}
      />

      <UserDetailDrawer
        open={detailOpen}
        user={detailUser}
        onClose={() => setDetailOpen(false)}
        onEdit={user => { setDetailOpen(false); openEdit(user) }}
        onActionComplete={refresh}
      />

      <AddEditUserDrawer
        open={editOpen}
        user={editUser}
        onClose={() => setEditOpen(false)}
        onSaved={refresh}
      />
    </>
  )
}
```

```tsx
// frontend/src/app/(dashboard)/dashboard/users/page.tsx
import UsersPage from '@views/users'

export default function Page() {
  return <UsersPage />
}
```

---

## T008 — Navigation

**File**: `src/components/layout/vertical/VerticalMenu.tsx`

Add the Users link inside the admin-only section:

```tsx
{user?.role === 'admin' && (
  <MenuItem href='/dashboard/users' icon='tabler-users'>
    Users
  </MenuItem>
)}
```

---

## Completion Checklist

| Task | Deliverable | Done? |
|------|------------|-------|
| T001 | `app/api/users/route.ts` | ☐ |
| T002 | `app/api/users/[id]/` + restore + force | ☐ |
| T003 | `views/users/ConfirmationDialog.tsx` | ☐ |
| T004 | `views/users/UserListTable.tsx` | ☐ |
| T005 | `views/users/UserDetailDrawer.tsx` | ☐ |
| T006 | `views/users/AddEditUserDrawer.tsx` | ☐ |
| T007 | `views/users/index.tsx` + `dashboard/users/page.tsx` | ☐ |
| T008 | Sidebar navigation | ☐ |

**Estimated effort**: 3–5 hours. Tiers are independent — T1 (BFF) can be tested with curl before any UI exists.
