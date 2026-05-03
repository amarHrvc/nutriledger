"use client";

import React, { useEffect, useMemo, useState } from 'react';

import { useReactTable, getCoreRowModel, flexRender, getFilteredRowModel } from '@tanstack/react-table'

import Card from '@mui/material/Card';
import CardHeader from '@mui/material/CardHeader';
import Table from '@mui/material/Table';
import TableHead from '@mui/material/TableHead';
import TableBody from '@mui/material/TableBody';
import TableRow from '@mui/material/TableRow';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TablePagination from '@mui/material/TablePagination';
import TextField from '@mui/material/TextField';
import Button from '@mui/material/Button';
import Box from '@mui/material/Box';
import CircularProgress from '@mui/material/CircularProgress';

import UserStatusChip from './shared/UserStatusChip';

interface User { id: string; attributes?: { name?: string; email?: string; role?: string }; active?: boolean }

export default function UserList() {
  const [users, setUsers] = useState<User[]>([]);
  const [search, setSearch] = useState('');
  const [pageIndex, setPageIndex] = useState(0); // zero-based for MUI TablePagination
  const [rowsPerPage, setRowsPerPage] = useState(10);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const controller = new AbortController();
    const signal = controller.signal;
    let mounted = true;

    const apiPage = pageIndex + 1; // backend is 1-based

    console.log('[search] :::', search)

    setLoading(true);

    const load = async () => {
      try {
        const res = await fetch(`/api/users?page=${apiPage}&search=${encodeURIComponent(search)}`, { signal });
        const json = await res.json();

        if (!mounted) return;
        setUsers(json.data.users ?? []);
        console.log('[UserList] setUsers ->', json.data ?? []);
      } catch (err: any) {
        if (err?.name === 'AbortError') return; // expected on cancel
        console.error('UserList load error', err);
        if (mounted) setUsers([]);
      } finally {
        if (mounted) setLoading(false);
      }
    };

    load();

    const handler = () => { setLoading(true); load(); };

    window.addEventListener('users:changed', handler);

    return () => {
      mounted = false;
      window.removeEventListener('users:changed', handler);
      controller.abort();
    };
  }, [pageIndex, rowsPerPage]);

  const columns = useMemo(
    () => [
      { accessorKey: 'attributes.name', header: 'Name' },
      { accessorKey: 'attributes.email', header: 'Email' },
      { accessorKey: 'attributes.role', header: 'Role' },
      {
        accessorKey: 'attributes.deletedAt',
        header: 'Status',
        cell: (info: any) => <UserStatusChip active={!!info.getValue()} />
      }
    ],
    []
  );

  const table = useReactTable({
    data: users,
    columns,
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    state: {
      globalFilter: search
    },
    onGlobalFilterChange: setSearch

  });

  const visibleRows = table.getRowModel().rows.slice(pageIndex * rowsPerPage, (pageIndex + 1) * rowsPerPage);
  return (
    <Card>
      <CardHeader title="Users" />
      <Box sx={{ display: 'flex', gap: 2, p: 2, alignItems: 'center' }}>
        <TextField
          size="small"
          placeholder="Search by name or email"
          value={search}
          onChange={e => {

            console.log('[search] ::: ', search)

            setSearch(e.target.value);
            setPageIndex(0); }}
        />
        <Box sx={{ flex: 1 }} />
        <Button variant="contained">Add New User</Button>
      </Box>

      {loading ? (
        <Box sx={{ display: 'flex', justifyContent: 'center', p: 4 }}><CircularProgress /></Box>
      ) : users.length === 0 ? (
        <Box sx={{ p: 4, textAlign: 'center' }}>No users found.</Box>
      ) : (
        <TableContainer>
          <Table>
            <TableHead>
              {table.getHeaderGroups().map(headerGroup => (
                <TableRow key={headerGroup.id}>
                  {headerGroup.headers.map(header => (
                    <TableCell key={header.id}>
                      {header.isPlaceholder ? null : (
                        <Box onClick={header.column.getToggleSortingHandler()} sx={{ display: 'inline-flex', alignItems: 'center', cursor: header.column.getCanSort() ? 'pointer' : 'default' }}>
                          {flexRender(header.column.columnDef.header, header.getContext())}
                          {{ asc: ' ↑', desc: ' ↓' }[header.column.getIsSorted() as 'asc' | 'desc'] ?? null}
                        </Box>
                      )}
                    </TableCell>
                  ))}
                </TableRow>
              ))}
            </TableHead>
            <TableBody>
              {visibleRows.map(row => (
                <TableRow key={row.id} hover>
                  {row.getVisibleCells().map(cell => (
                    <TableCell key={cell.id}>{flexRender(cell.column.columnDef.cell, cell.getContext())}</TableCell>
                  ))}
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </TableContainer>
      )}

      <TablePagination
        component="div"
        count={table.getRowModel().rows.length}
        page={pageIndex}
        onPageChange={(_, newPage) => setPageIndex(newPage)}
        rowsPerPage={rowsPerPage}
        onRowsPerPageChange={e => { setRowsPerPage(Number(e.target.value)); setPageIndex(0); }}
      />
    </Card>
  );
}
