"use client";
import React, { useState } from 'react';

interface Props {
  mode?: 'create' | 'edit';
  user?: any;
  onSuccess?: () => void;
}

export default function UserForm({ mode = 'create', user = {}, onSuccess }: Props) {
  const [name, setName] = useState(user.name ?? '');
  const [email, setEmail] = useState(user.email ?? '');
  const [password, setPassword] = useState('');
  const [passwordConfirm, setPasswordConfirm] = useState('');
  const [role, setRole] = useState(user.role ?? 'pacijent');
  const [errors, setErrors] = useState<Record<string,string>>({});
  const [loading, setLoading] = useState(false);

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setErrors({});
    try {
      const payload: any = { name, email, role };
      if (mode === 'create') payload.password = password;
      if (mode === 'create') payload.password_confirmation = passwordConfirm;

      const url = mode === 'create' ? '/api/users' : `/api/users/${user.id}`;
      const method = mode === 'create' ? 'POST' : 'PATCH';

      const res = await fetch(url, { method, headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
      if (res.status === 422) {
        const json = await res.json();
        // assume json.data or json.errors
        setErrors(json.data?.errors ?? json.errors ?? { form: 'Validation failed' });
        return;
      }
      if (!res.ok) {
        setErrors({ form: `Server error ${res.status}` });
        return;
      }
      // success
      window.dispatchEvent(new CustomEvent('users:changed'));
      onSuccess?.();
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={submit} aria-live="polite">
      <div>
        <label>Name</label>
        <input value={name} onChange={e => setName(e.target.value)} />
        {errors.name && <div role="alert">{errors.name}</div>}
      </div>
      <div>
        <label>Email</label>
        <input value={email} onChange={e => setEmail(e.target.value)} />
        {errors.email && <div role="alert">{errors.email}</div>}
      </div>
      <div>
        <label>Role</label>
        <select value={role} onChange={e => setRole(e.target.value)}>
          <option value="admin">admin</option>
          <option value="doktor">doktor</option>
          <option value="pacijent">pacijent</option>
        </select>
      </div>

      {mode === 'create' && (
        <>
          <div>
            <label>Password</label>
            <input type="password" value={password} onChange={e => setPassword(e.target.value)} />
            {errors.password && <div role="alert">{errors.password}</div>}
          </div>
          <div>
            <label>Confirm Password</label>
            <input type="password" value={passwordConfirm} onChange={e => setPasswordConfirm(e.target.value)} />
            {errors.password_confirmation && <div role="alert">{errors.password_confirmation}</div>}
          </div>
        </>
      )}

      {errors.form && <div role="alert">{errors.form}</div>}

      <div>
        <button type="submit" disabled={loading}>{loading ? 'Saving...' : mode === 'create' ? 'Create' : 'Save'}</button>
      </div>
    </form>
  );
}
