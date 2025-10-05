import React from 'react';
import { Table, Badge } from 'react-bootstrap';

export default function BetHistory({ history }) {
  if (!history || history.length === 0) return null;
  return (
    <div className="mt-4">
      <h5>Bet History</h5>
      <div className="table-responsive">
        <Table striped bordered hover>
          <thead>
            <tr>
              <th>Date</th>
              <th>Bet Type</th>
              <th>Number</th>
              <th>Outcome</th>
            </tr>
          </thead>
          <tbody>
            {history.map((h, idx) => (
              <tr key={idx}>
                <td>{new Date(h.timestamp).toLocaleString()}</td>
                <td>{h.betType}</td>
                <td>{h.rolledNumber}</td>
                <td>
                  <Badge bg={h.outcome === 'win' ? 'success' : 'danger'}>
                    {h.outcome}
                  </Badge>
                </td>
              </tr>
            ))}
          </tbody>
        </Table>
      </div>
    </div>
  );
}
