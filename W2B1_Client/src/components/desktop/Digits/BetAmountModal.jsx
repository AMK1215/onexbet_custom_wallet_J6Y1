import React, { useState, useEffect } from 'react';
import { Button, Card, Modal } from 'react-bootstrap';

export default function BetAmountModal({ show, onHide, onConfirm, initialBet = 1, initialCoinValue = 1, multiplier = 20 }) {
  const [bet, setBet] = useState(initialBet);
  const [coinValue, setCoinValue] = useState(initialCoinValue);

  useEffect(() => {
    setBet(initialBet);
    setCoinValue(initialCoinValue);
  }, [initialBet, initialCoinValue, show]);

  const totalBet = bet * coinValue * multiplier;

  const handleBetMax = () => {
    // Example: set bet and coinValue to some max values (customize as needed)
    setBet(10);
    setCoinValue(10);
  };

  const handleConfirm = () => {
    onConfirm && onConfirm({ bet, coinValue, totalBet });
    onHide && onHide();
  };

  return (
    <Modal show={show} onHide={onHide} centered>
      <Card className="p-3 text-center">
        <Card.Title className="mb-3">BET MULTIPLIER {multiplier}x</Card.Title>
        <div className="mb-3">
          <div className="d-flex align-items-center justify-content-between mb-2">
            <Button variant="outline-dark" onClick={() => setBet(b => Math.max(1, b - 1))}>-</Button>
            <div style={{ minWidth: 60, fontWeight: 'bold', fontSize: '1.2rem' }}>{bet}</div>
            <Button variant="outline-dark" onClick={() => setBet(b => b + 1)}>+</Button>
          </div>
          <div style={{ fontSize: 12, color: '#888' }}>BET</div>
        </div>
        <div className="mb-3">
          <div className="d-flex align-items-center justify-content-between mb-2">
            <Button variant="outline-dark" onClick={() => setCoinValue(c => Math.max(1, c - 1))}>-</Button>
            <div style={{ minWidth: 60, fontWeight: 'bold', fontSize: '1.2rem' }}>MMK{coinValue.toFixed(2)}</div>
            <Button variant="outline-dark" onClick={() => setCoinValue(c => c + 1)}>+</Button>
          </div>
          <div style={{ fontSize: 12, color: '#888' }}>COIN VALUE</div>
        </div>
        <div className="mb-3">
          <div className="d-flex align-items-center justify-content-between mb-2">
            <Button variant="outline-dark" disabled>-</Button>
            <div style={{ minWidth: 60, fontWeight: 'bold', fontSize: '1.2rem' }}>MMK{totalBet.toFixed(2)}</div>
            <Button variant="outline-dark" disabled>+</Button>
          </div>
          <div style={{ fontSize: 12, color: '#888' }}>TOTAL BET</div>
        </div>
        <Button variant="success" className="w-100 mb-2" onClick={handleBetMax}>BET MAX</Button>
        <Button variant="primary" className="w-100" onClick={handleConfirm}>Confirm</Button>
      </Card>
    </Modal>
  );
} 