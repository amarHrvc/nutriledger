import React from 'react';

interface Props {
  active: boolean;
}


export default function UserStatusChip({ active }: Props) {

  const label = !active ? 'Active' : 'Deactivated';
  const style = { padding: '0.25rem 0.5rem', borderRadius: '0.25rem', background: !active ? '#d1fae5' : '#fee2e2' };
  return <span style={style}>{label}</span>;
}
