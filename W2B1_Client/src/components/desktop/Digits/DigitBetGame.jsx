import React, { useState, useEffect, useRef, useCallback } from 'react';
import { Container, Row, Col, Card, Button, Spinner, Alert, Table, Badge, Collapse } from 'react-bootstrap';
import UserBalance from './UserBalance';
import NumberDisplay from './NumberDisplay';
import { BetSizeButtons, BetColorButtons } from './BetButtons';
import RollButton from './RollButton';
import ResultAlert from './ResultAlert';
import ApiResponseAlert from './ApiResponseAlert';
import BetHistory from './BetHistory';
import BetAmountModal from './BetAmountModal';
import bgImg from './bg.jfif';

// Import digit images
import digit0 from './assets/digit_img/0_Red-Purple.png';
import digit1 from './assets/digit_img/1_Green.png';
import digit2 from './assets/digit_img/2_Red.png';
import digit3 from './assets/digit_img/3_Green.png';
import digit4 from './assets/digit_img/4_Red.png';
import digit5 from './assets/digit_img/5_Green-Purple.png';
import digit6 from './assets/digit_img/6_Red.png';
import digit7 from './assets/digit_img/7_Green.png';
import digit8 from './assets/digit_img/8_Red.png';
import digit9 from './assets/digit_img/9_Green.png';

const digitImages = [
  digit0, digit1, digit2, digit3, digit4,
  digit5, digit6, digit7, digit8, digit9
];

// Custom hook for user
function useUser() {
  const [user, setUser] = useState(null);
  useEffect(() => {
    fetch('https://gamestar77.online/api/user', {
      headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('token'),
        'Accept': 'application/json'
      }
    })
      .then(res => res.json())
      .then(data => setUser(data.data));
  }, []);
  return [user, setUser];
}

function BetHistoryTable({ history }) {
  const [openRow, setOpenRow] = React.useState(null);

  return (
    <div className="table-responsive">
      <Table striped bordered hover className="align-middle text-center">
        <thead className="table-dark">
          <tr>
            <th>Date</th>
            <th>Bet Type</th>
            <th>Digit</th>
            <th>Bet Amount</th>
            <th>Multiplier</th>
            <th>Rolled</th>
            <th>Outcome</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>
          {history.map((h, idx) => (
            <React.Fragment key={idx}>
              <tr>
                <td>{new Date(h.timestamp).toLocaleString()}</td>
                <td className="text-capitalize">{h.betType}</td>
                <td>{h.digit !== null && h.digit !== undefined ? h.digit : '-'}</td>
                <td className="text-end">{parseFloat(h.bet_amount).toFixed(2)}</td>
                <td className="text-end">{parseFloat(h.multiplier).toFixed(2)}</td>
                <td>{h.rolledNumber}</td>
                <td>
                  <Badge bg={h.outcome === 'win' ? 'success' : 'danger'}>{h.outcome}</Badge>
                </td>
                <td>
                  <Button
                    size="sm"
                    variant="outline-secondary"
                    onClick={() => setOpenRow(openRow === idx ? null : idx)}
                    aria-controls={`collapse-row-${idx}`}
                    aria-expanded={openRow === idx}
                  >
                    {openRow === idx ? 'Hide' : 'Show'}
                  </Button>
                </td>
              </tr>
              <tr>
                <td colSpan={8} style={{ padding: 0, background: '#f9f9f9' }}>
                  <Collapse in={openRow === idx}>
                    <div id={`collapse-row-${idx}`} className="p-2 text-start">
                      <div><strong>Win Amount:</strong> {parseFloat(h.win_amount).toFixed(2)}</div>
                      <div>
                        <strong>Profit:</strong>{' '}
                        <span style={{ color: parseFloat(h.profit) > 0 ? 'green' : (parseFloat(h.profit) < 0 ? 'red' : 'inherit') }}>
                          {parseFloat(h.profit).toFixed(2)}
                        </span>
                      </div>
                    </div>
                  </Collapse>
                </td>
              </tr>
            </React.Fragment>
          ))}
        </tbody>
      </Table>
    </div>
  );
}

export default function DigitBetGame() {
  const [user, setUser] = useUser();
  const [randomNumber, setRandomNumber] = useState(null);
  const [betType, setBetType] = useState(null);
  const [selectedDigit, setSelectedDigit] = useState(null);
  const [resultMessage, setResultMessage] = useState('');
  const [loading, setLoading] = useState(false);
  const [apiResponseMessage, setApiResponseMessage] = useState('');
  const [history, setHistory] = useState([]);
  const [betAmount, setBetAmount] = useState(1);
  const [betMultiplier, setBetMultiplier] = useState(1);
  const [showBetModal, setShowBetModal] = useState(false);
  const [countdown, setCountdown] = useState(null);
  const timerRef = useRef(null);
  const handleRollRef = useRef();

  useEffect(() => {
    fetch('https://gamestar77.online/api/digitbet/history', {
      headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('token'),
        'Accept': 'application/json'
      }
    })
      .then(res => res.json())
      .then(data => setHistory(data.data || []));
  }, []);

  const generateNumber = () => Math.floor(Math.random() * 10);

  // Define checkBet before handleRoll
  const checkBet = (number) => {
    if (selectedDigit !== null) {
      const win = (number === selectedDigit);
      setResultMessage(
        win
          ? `You Won! The number was ${number}.`
          : `You Lost! The number was ${number}.`
      );
      return win ? 'win' : 'lose';
    }
    let win = false;
    let category = (number >= 0 && number <= 4) ? 'Small' : 'Big';

    switch (betType) {
      case 'small': win = (number >= 0 && number <= 4); break;
      case 'big': win = (number >= 5 && number <= 9); break;
      default: break;
    }

    if (win) setResultMessage(`You Won! The number was ${number}. It's ${category}.`);
    else setResultMessage(`You Lost! The number was ${number}. It's ${category}.`);
    return win ? 'win' : 'lose';
  };

  // Define sendToBackend before handleRoll
  const sendToBackend = async (bet, resultNumber, winStatus, digit = null) => {
    setLoading(true);
    try {
      // Simplified payload matching the backend API exactly
      const payload = {
        bet_type: digit !== null ? 'digit' : betType,
        digit: digit,
        rolled_number: resultNumber,
        outcome: winStatus,
        bet_amount: betAmount,
        multiplier: betMultiplier
      };

      const response = await fetch('https://gamestar77.online/api/digitbet', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ' + localStorage.getItem('token'),
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      const data = await response.json();
      
      if (data.success) {
        setApiResponseMessage(`✅ ${data.message}`);
        // Update user balance from response
        if (data.data && data.data.balance) {
          setUser(prev => ({ ...prev, balance: data.data.balance }));
        }
        // Update history from response
        if (data.data && data.data.history) {
          setHistory(data.data.history);
        }
      } else {
        setApiResponseMessage(`❌ ${data.message}`);
        // If there's an error, refresh history separately
        fetch('https://gamestar77.online/api/digitbet/history', {
          headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token'),
            'Accept': 'application/json'
          }
        })
          .then(res => res.json())
          .then(historyData => {
            if (historyData.success) {
              setHistory(historyData.data || []);
            }
          });
      }
    } catch (error) {
      setApiResponseMessage(`❌ Backend Error: ${error.message || 'Could not connect to backend.'}`);
    } finally {
      setLoading(false);
    }
  };

  const handleRoll = useCallback(async () => {
    setResultMessage('');
    setApiResponseMessage('');
    setRandomNumber(null);

    if (selectedDigit === null && !betType) {
      setResultMessage('Please place a bet first!');
      return;
    }

    const newNumber = generateNumber();
    setRandomNumber(newNumber);

    let winStatus;
    if (selectedDigit !== null) {
      winStatus = (newNumber === selectedDigit) ? 'win' : 'lose';
      setResultMessage(
        winStatus === 'win'
          ? `You Won! The number was ${newNumber}.`
          : `You Lost! The number was ${newNumber}.`
      );
      await sendToBackend('digit', newNumber, winStatus, selectedDigit);
    } else {
      winStatus = checkBet(newNumber);
      await sendToBackend(betType, newNumber, winStatus);
    }

    // Reset selection for next round
    setSelectedDigit(null);
    setBetType(null);
  }, [selectedDigit, betType, generateNumber, sendToBackend, checkBet]);

  useEffect(() => {
    handleRollRef.current = handleRoll;
  }, [handleRoll]);

  // Start countdown logic
  const startCountdown = () => {
    setCountdown(90);
    if (timerRef.current) clearInterval(timerRef.current);
    timerRef.current = setInterval(() => {
      setCountdown(prev => {
        if (prev === 1) {
          clearInterval(timerRef.current);
          setCountdown(null);
          if (handleRollRef.current) handleRollRef.current();
          return null;
        }
        return prev - 1;
      });
    }, 1000);
  };

  // Cleanup timer on unmount
  useEffect(() => {
    return () => {
      if (timerRef.current) clearInterval(timerRef.current);
    };
  }, []);

  const placeBet = (type) => {
    setBetType(type);
    setSelectedDigit(null);
    setResultMessage('');
    setApiResponseMessage('');
    setRandomNumber(null);
    startCountdown();
  };

  const selectDigit = (num) => {
    setSelectedDigit(num);
    setBetType(null);
    setResultMessage('');
    setApiResponseMessage('');
    setRandomNumber(null);
    startCountdown();
  };

  return (
    <Container className="d-flex align-items-center justify-content-center min-vh-100" style={{ background: 'linear-gradient(135deg, #7b2ff2 0%, #f357a8 100%)' }}>
      <Row className="w-100 justify-content-center">
        <Col xs={12} md={8} lg={6}>
          <Card className="shadow-lg p-4 rounded-4">
            <Card.Body>
              <UserBalance balance={user?.balance} />
              <h1 className="text-center mb-4 fw-bold">Digit Bet Game</h1>
              <div className="mb-4">
                <Card
                  className="text-center p-4"
                  style={{
                    backgroundImage: `url(${bgImg})`,
                    backgroundSize: 'cover',
                    backgroundPosition: 'center',
                    minHeight: 180,
                    borderRadius: 12
                  }}
                >
                  <Card.Body>
                    {randomNumber !== null ? (
                      <img
                        src={digitImages[randomNumber]}
                        alt={randomNumber}
                        style={{ width: '90px', height: '90px', objectFit: 'contain' }}
                      />
                    ) : (
                      <span className="display-4 text-white">?</span>
                    )}
                  </Card.Body>
                </Card>
              </div>
              <h4 className="text-center mb-3">Select Bet Digit</h4>
              <div className="d-flex justify-content-center mb-2 gap-2">
                {[0, 1, 2, 3, 4].map(num => (
                  <img
                    key={num}
                    src={digitImages[num]}
                    alt={num}
                    style={{
                      width: 40,
                      height: 40,
                      objectFit: 'contain',
                      border: selectedDigit === num ? '3px solid #007bff' : '2px solid transparent',
                      borderRadius: '50%',
                      cursor: 'pointer',
                      boxShadow: selectedDigit === num ? '0 0 8px #007bff' : undefined
                    }}
                    onClick={() => selectDigit(num)}
                  />
                ))}
              </div>
              <div className="d-flex justify-content-center mb-3 gap-2">
                {[5, 6, 7, 8, 9].map(num => (
                  <img
                    key={num}
                    src={digitImages[num]}
                    alt={num}
                    style={{
                      width: 40,
                      height: 40,
                      objectFit: 'contain',
                      border: selectedDigit === num ? '3px solid #007bff' : '2px solid transparent',
                      borderRadius: '50%',
                      cursor: 'pointer',
                      boxShadow: selectedDigit === num ? '0 0 8px #007bff' : undefined
                    }}
                    onClick={() => selectDigit(num)}
                  />
                ))}
              </div>
              <div className="d-flex justify-content-center gap-3 mb-3">
                <Button
                  variant={betType === 'big' ? 'warning' : 'outline-warning'}
                  size="lg"
                  className={betType === 'big' ? 'fw-bold shadow' : ''}
                  onClick={() => placeBet('big')}
                  disabled={loading || selectedDigit !== null}
                >
                  Big (5-9)
                </Button>
                <Button
                  variant={betType === 'small' ? 'primary' : 'outline-primary'}
                  size="lg"
                  className={betType === 'small' ? 'fw-bold shadow' : ''}
                  onClick={() => placeBet('small')}
                  disabled={loading || selectedDigit !== null}
                >
                  Small (0-4)
                </Button>
              </div>
              {/* Show result message or countdown timer in the result area */}
              {countdown !== null ? (
                <Alert variant="warning" className="text-center fw-bold" style={{ fontSize: '1.5rem' }}>
                  Rolling in {countdown}...
                </Alert>
              ) : (
                resultMessage && (
                  <Alert variant={resultMessage.includes('Won') ? 'success' : resultMessage.includes('Lost') ? 'danger' : 'info'} className="text-center fw-bold">
                    {resultMessage}
                  </Alert>
                )
              )}
              <div className="mb-3">
                <div className="mb-2 d-flex justify-content-between align-items-center">
                  <label htmlFor="betAmount" className="form-label fw-bold mb-0">Bet Amount</label>
                  <span className="fw-bold text-primary" style={{ fontSize: '1rem' }}>
                    Balance: {user?.balance ? parseFloat(user.balance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00'}
                  </span>
                </div>
                <input
                  type="number"
                  className="form-control"
                  id="betAmount"
                  value={betAmount}
                  onChange={e => setBetAmount(Number(e.target.value))}
                  style={{ maxWidth: 200, margin: '0 auto', background: '#f8f9fa', cursor: 'pointer' }}
                  onClick={() => setShowBetModal(true)}
                  placeholder="Set bet amount via modal"
                />
              </div>
              <div className="d-flex justify-content-center gap-2">
                {[1, 5, 10, 20, 50, 100].map(mult => (
                  <Button
                    key={mult}
                    variant={betMultiplier === mult ? 'primary' : 'outline-primary'}
                    size="sm"
                    onClick={() => setBetMultiplier(mult)}
                  >
                    x{mult}
                  </Button>
                ))}
              </div>
              <div className="text-center fw-bold my-2" style={{fontSize: '1.1rem'}}>
                {betAmount > 0 && betMultiplier > 0 && (
                  <>
                    {betAmount} x {betMultiplier} = <span className="text-primary">{betAmount * betMultiplier}</span>
                  </>
                )}
              </div>
              <div className="mb-3">
                <Button
                  variant="primary"
                  size="lg"
                  className="w-100 fw-bold"
                  onClick={handleRoll}
                  disabled={(!betType && selectedDigit === null) || loading || countdown !== null}
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
              {apiResponseMessage && (
                <Alert variant={apiResponseMessage.includes('Error') ? 'danger' : 'secondary'} className="text-center">
                  {apiResponseMessage}
                </Alert>
              )}
              {history.length > 0 && (
                <div className="mt-4">
                  <h5>Bet History</h5>
                  <BetHistoryTable history={history} />
                </div>
              )}
              <div className="mt-4 text-center text-muted">
                <div>
                  <strong>Current Bet:</strong> {selectedDigit !== null ? `Digit ${selectedDigit}` : betType ? betType.charAt(0).toUpperCase() + betType.slice(1) : 'None'}
                </div>
                <div>
                  <small>Select a digit or a bet (Small/Big) and then click <b>"Roll the Dice!"</b></small>
                </div>
              </div>
            </Card.Body>
          </Card>
        </Col>
      </Row>
      <BetAmountModal
        show={showBetModal}
        onHide={() => setShowBetModal(false)}
        onConfirm={({ bet, coinValue }) => {
          setBetAmount(bet);
          setBetMultiplier(coinValue);
        }}
        initialBet={betAmount}
        initialCoinValue={betMultiplier}
        multiplier={20}
      />
    </Container>
  );
}
