import React from 'react';
import { Button, Spinner } from 'react-bootstrap';

export default function RollButton({ onRoll, betType, loading }) {
  return (
    <div className="mb-3">
      <Button
        variant="primary"
        size="lg"
        className="w-100 fw-bold"
        onClick={onRoll}
        disabled={!betType || loading}
      >
        {loading ? (
          <>
            <Spinner as="span" animation="border" size="sm" role="status" aria-hidden="true" className="me-2" />
            Rolling...
          </>
        ) : (
          'Roll the Dice!'
        )}
      </Button>
    </div>
  );
}
