"use client";
import React, { useState } from 'react';
import UserStatusChip from './shared/UserStatusChip';
import ConfirmDialog from './shared/ConfirmDialog';

interface User {
  id: string;
  name?: string;
  email?: string;
  role?: string;
  active?: boolean;
  created_at?: string;
  deleted_at?: string | null;
}

export default function UserDetail({ user }: { user: User }) {
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [confirmAction, setConfirmAction] = useState<null | 'deactivate' | 'force' | 'restore'>(null);

  const onConfirm = async () => {
    if (!confirmAction) return;
    try {
      if (confirmAction === 'deactivate') {
        await fetch(`/api/users/${user.id}`, { method: 'DELETE' });
      } else if (confirmAction === 'restore') {
        await fetch(`/api/users/${user.id}/restore`, { method: 'POST' });
      } else if (confirmAction === 'force') {
        await fetch(`/api/users/${user.id}/force`, { method: 'DELETE' });
      }
      window.dispatchEvent(new CustomEvent('users:changed'));
    } finally {
      setConfirmOpen(false);
      setConfirmAction(null);
    }
  };

  return (
    <div>
      <h2>{user.name}</h2>
      <p>{user.email}</p>
      <p>Role: {user.role}</p>
      <p>Status: <UserStatusChip active={!!user.active} /></p>
      <p>Created: {user.created_at}</p>
      {user.deleted_at && <p>Deactivated: {user.deleted_at}</p>}

      <div>
        {!user.active && (
          <button onClick={() => { setConfirmAction('restore'); setConfirmOpen(true); }}>Restore</button>
        )}

        {user.active && (
          <button onClick={() => { setConfirmAction('deactivate'); setConfirmOpen(true); }}>Deactivate</button>
        )}

        {!user.active && (
          <button onClick={() => { setConfirmAction('force'); setConfirmOpen(true); }}>Permanently Delete</button>
        )}
      </div>

      <ConfirmDialog open={confirmOpen} title="Confirm Action" message={`Are you sure you want to ${confirmAction}?`} onConfirm={onConfirm} onCancel={() => { setConfirmOpen(false); setConfirmAction(null); }} />
    </div>
  );
}
