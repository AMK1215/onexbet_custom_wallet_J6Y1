import React, { useEffect, useState } from 'react';
import BASE_URL from '../../hooks/baseUrl';
import { Spinner, Table, Alert } from 'react-bootstrap';

const ExchangeTranLog = () => {
  const [logs, setLogs] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchLogs = async () => {
      setLoading(true);
      setError(null);
      try {
        const res = await fetch(`${BASE_URL}/exchange-transactions-log`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Accept': 'application/json',
          },
        });
        const data = await res.json();
        if (res.ok && data.status === 'Request was successful.' && data.data?.data) {
          setLogs(data.data.data);
        } else {
          setError(data.message || 'Failed to fetch logs');
        }
      } catch (e) {
        setError('Network or server error');
      }
      setLoading(false);
    };
    fetchLogs();
  }, []);

  return (
    <div className="container my-4">
      <h3>Exchange Transaction Log</h3>
      {loading && <Spinner animation="border" />}
      {error && <Alert variant="danger">{error}</Alert>}
      {!loading && !error && (
        <Table striped bordered hover responsive>
          <thead>
            <tr>
              <th>#</th>
              <th>Amount</th>
              <th>Type</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            {logs.length === 0 && (
              <tr><td colSpan="4" className="text-center">No transactions found.</td></tr>
            )}
            {logs.map((log, idx) => (
              <tr key={log.id}>
                <td>{idx + 1}</td>
                <td>{log.amount}</td>
                <td>{log.type === 'mainBalanceToGaming' ? 'Main → Game' : 'Game → Main'}</td>
                <td>{new Date(log.created_at).toLocaleString()}</td>
              </tr>
            ))}
          </tbody>
        </Table>
      )}
    </div>
  );
};

export default ExchangeTranLog; 