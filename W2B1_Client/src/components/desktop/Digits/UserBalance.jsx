import React from 'react';

export default function UserBalance({ balance }) {
  return (
    <div className="mb-3 text-end">
      <span className="badge bg-warning text-white fs-5">
        Balance: {balance ?? '...'}
      </span>
    </div>
  );
}
