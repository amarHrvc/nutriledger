"use client";
import React, { useEffect, useMemo, useState } from 'react';
import { useReactTable, getCoreRowModel, flexRender } from '@tanstack/react-table';
import UserStatusChip from './shared/UserStatusChip';

interface User { id: string; name?: string; email?: string; role?: string; active?: boolean }

export default function UserList() {
  const [users, setUsers] = useState<User[]>([]);
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const controller = new AbortController();
    const signal = controller.signal;
    let mounted = true;
    setLoading(true);

    const load = async () => {
      try {
        const res = await fetch(`/api/users?page=${page}&search=${encodeURIComponent(search)}`, { signal });
        const json = await res.json();
        console.log('[UserList - json] snapshot', JSON.stringify(json.data));
        if (!mounted) return;
        setUsers(json.data ?? []);
        console.log('[UserList] setUsers ->', json.data ?? []);
      } catch (err: any) {
        if (err.name === 'AbortError') return; // expected on cancel
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
  }, [search, page]);

  const columns = useMemo(() => [
    { accessorKey: 'name', header: 'Name' },
    { accessorKey: 'email', header: 'Email' },
    { accessorKey: 'role', header: 'Role' },
    {
      accessorKey: 'active',
      header: 'Status',
      cell: (info: any) => <UserStatusChip active={!!info.getValue()} />,
    },
  ], []);

  const table = useReactTable({ data: users, columns, getCoreRowModel: getCoreRowModel() });

  return (
    <div>
      <div>
        <input aria-label="Search users" placeholder="Search by name or email" value={search} onChange={e => { setSearch(e.target.value); setPage(1); }} />
      </div>

      {loading && <div>Loading...</div>}

      {!loading && users.length === 0 && <div>No users found.</div>}

      {!loading && users.length > 0 && (
        <table>
          <thead>
            {table.getHeaderGroups().map(headerGroup => (
              <tr key={headerGroup.id}>
                {headerGroup.headers.map(header => (
                  <th key={header.id}>{header.isPlaceholder ? null : flexRender(header.column.columnDef.header, header.getContext())}</th>
                ))}
              </tr>
            ))}
          </thead>
          <tbody>
            {table.getRowModel().rows.map(row => (
              <tr key={row.id}>
                {row.getVisibleCells().map(cell => (
                  <td key={cell.id}>{flexRender(cell.column.columnDef.cell, cell.getContext())}</td>
                ))}
              </tr>
            ))}
          </tbody>
        </table>
      )}

      <div style={{ marginTop: 8 }}>
        <button onClick={() => setPage(p => Math.max(1, p - 1))} disabled={page === 1}>Prev</button>
        <span style={{ margin: '0 8px' }}>Page {page}</span>
        <button onClick={() => setPage(p => p + 1)}>Next</button>
      </div>
    </div>
  );
}
