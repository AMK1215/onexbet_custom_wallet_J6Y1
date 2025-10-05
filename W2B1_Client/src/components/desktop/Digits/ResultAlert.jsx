import React from 'react';
import { Alert } from 'react-bootstrap';

export default function ResultAlert({ resultMessage }) {
  if (!resultMessage) return null;
  return (
    <Alert variant={resultMessage.includes('Won') ? 'success' : resultMessage.includes('Lost') ? 'danger' : 'info'} className="text-center fw-bold">
      {resultMessage}
    </Alert>
  );
}
