'use client'

import React, { useEffect, useMemo, useState } from 'react'
import { useRouter } from 'next/navigation'
import { useReactTable, getCoreRowModel, flexRender, getFilteredRowModel } from '@tanstack/react-table'

import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import Table from '@mui/material/Table'
import TableHead from '@mui/material/TableHead'
import TableBody from '@mui/material/TableBody'
import TableRow from '@mui/material/TableRow'
import TableCell from '@mui/material/TableCell'
import TableContainer from '@mui/material/TableContainer'
import TablePagination from '@mui/material/TablePagination'
import TextField from '@mui/material/TextField'
import Button from '@mui/material/Button'
import Box from '@mui/material/Box'
import CircularProgress from '@mui/material/CircularProgress'
import Dialog from '@mui/material/Dialog'
import DialogTitle from '@mui/material/DialogTitle'
import DialogContent from '@mui/material/DialogContent'

import type { PatientResource } from '@/api/generated/nutriBaseAPI.schemas'
import PatientForm from './PatientForm'

export default function PatientList() {
  const [patients, setPatients] = useState<PatientResource[]>([]);
  const [search, setSearch] = useState('');
  const [pageIndex, setPageIndex] = useState(0);
  const [rowsPerPage, setRowsPerPage] = useState(10);
  const [loading, setLoading] = useState(false);
  const router = useRouter();
  const [formOpen, setFormOpen] = useState(false);

  useEffect(() => {
    const controller = new AbortController();
    const signal = controller.signal;
    let mounted = true;

    setLoading(true);

    const load = async () => {
      try {
        const res = await fetch(`/api/patients`, { signal });
        const json = await res.json();

        if (!res.ok) throw new Error(json?.message ?? `HTTP ${res.status}`);

        if (!mounted) return;
        // API forwards data shape; expected json.data.patients
        setPatients(json.data?.patients ?? []);
      } catch (err: any) {
        if (err?.name === 'AbortError') return;
        // fall back to empty
        if (mounted) setPatients([]);
      } finally {
        if (mounted) setLoading(false);
      }
    };

    load();

    const handler = () => { setLoading(true); load(); };

    window.addEventListener('patients:changed', handler);

    return () => {
      mounted = false;
      window.removeEventListener('patients:changed', handler);
      controller.abort();
    };
  }, [pageIndex, rowsPerPage]);

  const columns = useMemo(
    () => [
      { accessorKey: 'attributes.fullName', header: 'Full Name' },
      { accessorKey: 'attributes.dateOfBirth', header: 'Date of Birth' },
      { accessorKey: 'attributes.gender', header: 'Gender' },
      { accessorKey: 'attributes.phone', header: 'Phone' },
    ],
    []
  );

  const table = useReactTable({
    data: patients,
    columns,
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    state: { globalFilter: search },
    onGlobalFilterChange: setSearch
  });

  const visibleRows = table.getRowModel().rows.slice(pageIndex * rowsPerPage, (pageIndex + 1) * rowsPerPage);

  return (
    <Box>
      <Card>
        <CardHeader title='Patients' />
        <Box sx={{ display: 'flex', gap: 2, p: 2, alignItems: 'center' }}>
          <TextField
            size='small'
            placeholder='Search by name, phone or other'
            value={search}
            onChange={e => { setSearch(e.target.value); setPageIndex(0); }}
          />
          <Box sx={{ flex: 1 }} />
          <Button variant='contained' onClick={() => setFormOpen(true)}>
            Add New Patient
          </Button>
        </Box>

        {loading ? (
          <Box sx={{ display: 'flex', justifyContent: 'center', p: 4 }}>
            <CircularProgress />
          </Box>
        ) : patients.length === 0 ? (
          <Box sx={{ p: 4, textAlign: 'center' }}>No patients found.</Box>
        ) : (
          <TableContainer>
            <Table>
              <TableHead>
                {table.getHeaderGroups().map(headerGroup => (
                  <TableRow key={headerGroup.id}>
                    {headerGroup.headers.map(header => (
                      <TableCell key={header.id}>
                        {header.isPlaceholder ? null : (
                          <Box
                            onClick={header.column.getToggleSortingHandler()}
                            sx={{ display: 'inline-flex', alignItems: 'center', cursor: header.column.getCanSort() ? 'pointer' : 'default' }}
                          >
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
                  <TableRow
                    key={row.id}
                    hover
                    sx={{ cursor: 'pointer' }}
                    onClick={() => router.push(`/dashboard/patients/${row.original.id}`)}
                  >
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
          component='div'
          count={table.getRowModel().rows.length}
          page={pageIndex}
          onPageChange={(_, newPage) => setPageIndex(newPage)}
          rowsPerPage={rowsPerPage}
          onRowsPerPageChange={e => { setRowsPerPage(Number(e.target.value)); setPageIndex(0); }}
        />
      </Card>

      <Dialog open={formOpen} onClose={() => setFormOpen(false)} fullWidth maxWidth='sm'>
        <DialogTitle>Create Patient</DialogTitle>
        <DialogContent sx={{ pt: 2 }}>
          <PatientForm onSuccess={() => setFormOpen(false)} onCancel={() => setFormOpen(false)} />
        </DialogContent>
      </Dialog>
    </Box>
  )
}
