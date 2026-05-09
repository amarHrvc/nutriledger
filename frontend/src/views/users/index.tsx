"use client";
import React, { useState } from 'react';

import UserList from './UserList';
import PatientsView from '../PatientsView';
import { Button, Box } from '@mui/material';

export default function UsersView() {
  const [showPatients, setShowPatients] = useState(false);

  return (
    <div>
      <Box sx={{ display: 'flex', gap: 2, mb: 2 }}>
        <Button variant="contained" onClick={() => setShowPatients((s) => !s)}>
          {showPatients ? 'Hide Patients' : 'Show Patients'}
        </Button>
      </Box>
      {showPatients ? <PatientsView /> : <UserList />}
    </div>
  );
}
