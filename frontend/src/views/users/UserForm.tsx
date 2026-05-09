"use client";
import { useState } from 'react';

import Alert from '@mui/material/Alert';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import CircularProgress from '@mui/material/CircularProgress';
import FormControl from '@mui/material/FormControl';
import InputLabel from '@mui/material/InputLabel';
import MenuItem from '@mui/material/MenuItem';
import Select from '@mui/material/Select';
import Stack from '@mui/material/Stack';
import TextField from '@mui/material/TextField';

import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas';

interface Props {
  mode?: 'create' | 'edit';
  user?: UserResource;
  onSuccess?: () => void;
  onCancel?: () => void;
}

export default function UserForm({ mode = 'create', user, onSuccess, onCancel }: Props) {
  const [name, setName] = useState(user?.attributes?.name ?? '');
  const [email, setEmail] = useState(user?.attributes?.email ?? '');
  const [password, setPassword] = useState('');
  const [passwordConfirm, setPasswordConfirm] = useState('');
  const [role, setRole] = useState(user?.attributes?.role ?? 'pacijent');
  const [errors, setErrors] = useState<Record<string, string[]>>({});
  const [formError, setFormError] = useState('');
  const [loading, setLoading] = useState(false);

  const fieldError = (field: string) => errors[field]?.[0];

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setErrors({});
    setFormError('');

    try {
      const payload: Record<string, string> = { name, email, role };
      if (mode === 'create') {
        payload.password = password;
        payload.password_confirmation = passwordConfirm;
      }

      const url = mode === 'create' ? '/api/users' : `/api/users/${user?.id}`;
      const method = mode === 'create' ? 'POST' : 'PATCH';

      const res = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });

      const json = await res.json();

      if (res.status === 422) {
        setErrors(json.errors ?? {});
        return;
      }
      if (!res.ok) {
        setFormError(json.message ?? `Server error ${res.status}`);
        return;
      }

      window.dispatchEvent(new CustomEvent('users:changed'));
      onSuccess?.();
    } finally {
      setLoading(false);
    }
  };

  return (
    <Box component='form' onSubmit={submit} noValidate>
      <Stack spacing={3} sx={{ pt: 1 }}>
        {formError && <Alert severity='error'>{formError}</Alert>}

        <TextField
          label='Name'
          value={name}
          onChange={e => setName(e.target.value)}
          error={!!fieldError('name')}
          helperText={fieldError('name')}
          fullWidth
          required
        />

        <TextField
          label='Email'
          type='email'
          value={email}
          onChange={e => setEmail(e.target.value)}
          error={!!fieldError('email')}
          helperText={fieldError('email')}
          fullWidth
          required
        />

        <FormControl fullWidth required error={!!fieldError('role')}>
          <InputLabel>Role</InputLabel>
          <Select value={role} label='Role' onChange={e => setRole(e.target.value)}>
            <MenuItem value='admin'>Admin</MenuItem>
            <MenuItem value='doktor'>Doktor</MenuItem>
            <MenuItem value='pacijent'>Pacijent</MenuItem>
          </Select>
        </FormControl>

        {mode === 'create' && (
          <>
            <TextField
              label='Password'
              type='password'
              value={password}
              onChange={e => setPassword(e.target.value)}
              error={!!fieldError('password')}
              helperText={fieldError('password')}
              fullWidth
              required
            />

            <TextField
              label='Confirm Password'
              type='password'
              value={passwordConfirm}
              onChange={e => setPasswordConfirm(e.target.value)}
              error={!!fieldError('password_confirmation')}
              helperText={fieldError('password_confirmation')}
              fullWidth
              required
            />
          </>
        )}

        <Box sx={{ display: 'flex', gap: 2, justifyContent: 'flex-end' }}>
          {onCancel && (
            <Button variant='outlined' onClick={onCancel} disabled={loading}>
              Cancel
            </Button>
          )}
          <Button type='submit' variant='contained' disabled={loading} startIcon={loading ? <CircularProgress size={16} /> : null}>
            {mode === 'create' ? 'Create User' : 'Save Changes'}
          </Button>
        </Box>
      </Stack>
    </Box>
  );
}
