"use client";
import React, { useEffect, useState } from 'react';
import { Card, CardContent, Typography, List, ListItem, ListItemText, CircularProgress, Alert } from '@mui/material';

export default function VisitsView() {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [visits, setVisits] = useState<any[] | null>(null);

  useEffect(() => {
    let mounted = true;
    (async () => {
      try {
        const res = await fetch('http://localhost:8000/api/visits', { method: 'GET' });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const json = await res.json();
        if (mounted) setVisits(Array.isArray(json) ? json : json?.data ?? json ?? []);
      } catch (e) {
        if (mounted) setError(String(e));
      } finally {
        if (mounted) setLoading(false);
      }
    })();
    return () => {
      mounted = false;
    };
  }, []);

  return (
    <Card>
      <CardContent>
        <Typography variant="h6" gutterBottom>
          Visits
        </Typography>
        {loading && <CircularProgress />}
        {error && <Alert severity="error">{error}</Alert>}
        {!loading && !error && (
          <List>
            {visits && visits.length > 0 ? (
              visits.map((v: any, i: number) => (
                <ListItem key={v.id ?? i} divider>
                  <ListItemText
                    primary={v?.title ?? (v?.type ?? 'Visit') + (v?.date ? ` — ${new Date(v.date).toLocaleString()}` : '')}
                    secondary={
                      <>
                        {v?.patient?.name ? <span>Patient: {v.patient.name}</span> : null}
                        {v?.doctor?.name ? <span> — Doctor: {v.doctor.name}</span> : null}
                        {v?.notes ? <div>{v.notes}</div> : null}
                      </>
                    }
                  />
                </ListItem>
              ))
            ) : (
              <ListItem>
                <ListItemText primary="No visits found or the backend endpoint is unavailable." />
              </ListItem>
            )}
          </List>
        )}
      </CardContent>
    </Card>
  );
}
