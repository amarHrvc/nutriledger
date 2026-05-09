import React from 'react';

interface Props {
  deletedAt: string | null | undefined;
}


export default function UserStatusChip({ deletedAt }: Props) {

  const label = deletedAt ? 'Deactivated' : 'Active' ;
  const style = { padding: '0.25rem 0.5rem', borderRadius: '0.25rem', background: !deletedAt ? '#d1fae5' : '#fee2e2' };

  return <span style={style}>{label}</span>;
}
