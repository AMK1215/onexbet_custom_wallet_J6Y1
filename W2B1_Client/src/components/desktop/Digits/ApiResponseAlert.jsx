import React from 'react';
import { Alert } from 'react-bootstrap';

export default function ApiResponseAlert({ apiResponseMessage }) {
  if (!apiResponseMessage) return null;
  return (
    <Alert variant={apiResponseMessage.includes('Error') ? 'danger' : 'secondary'} className="text-center">
      {apiResponseMessage}
    </Alert>
  );
}
