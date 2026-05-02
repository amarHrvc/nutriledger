"use client";
import React, { useEffect, useState } from 'react';

interface User { id: string; name?: string; email?: string; role?: string; active?: boolean }

export default function UserList() {
  const [users, setUsers] = useState<User[]>([]);
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    let mounted = true;
    setLoading(true);
    fetch(`/api/users?page=${page}&search=${encodeURIComponent(search)}`)
      .then(r => r.json())
      .then((json) => {
        if (!mounted) return;
        setUsers(json.data ?? []);
      })
      .catch(() => setUsers([]))
      .finally(() => mounted && setLoading(false));
    return () => { mounted = false; };
  }, [search, page]);

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
            <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>
          </thead>
          <tbody>
            {users.map(u => (
              <tr key={u.id}>
                <td>{u.name}</td>
                <td>{u.email}</td>
                <td>{u.role}</td>
                <td>{u.active ? 'Active' : 'Deactivated'}</td>
              </tr>
            ))}
          </tbody>
        </table>
      )}

      <div>
        <button onClick={() => setPage(p => Math.max(1, p - 1))} disabled={page === 1}>Prev</button>
        <span>Page {page}</span>
        <button onClick={() => setPage(p => p + 1)}>Next</button>
      </div>
    </div>
  );
}
