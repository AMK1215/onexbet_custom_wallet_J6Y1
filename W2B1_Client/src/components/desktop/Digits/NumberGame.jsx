import React, { useState } from 'react';
import { Button, Spinner, Card, Alert, Container, Row, Col } from 'react-bootstrap';

function DigitBetGame() {
  const [randomNumber, setRandomNumber] = useState(null);
  const [betType, setBetType] = useState(null); // 'small', 'big', 'green', 'yellow', 'red'
  const [resultMessage, setResultMessage] = useState('');
  const [gameStarted, setGameStarted] = useState(false); // To show initial state
  const [loading, setLoading] = useState(false); // New state for loading indicator
  const [apiResponseMessage, setApiResponseMessage] = useState(''); // New state for API response

  // Function to generate a random number (0-9)
  const generateNumber = () => {
    return Math.floor(Math.random() * 10); // Generates a number from 0 to 9
  };

  // Function to handle rolling the number, checking the bet, and sending to backend
  const handleRoll = async () => {
    setGameStarted(true); // Game has started
    setResultMessage(''); // Clear previous result
    setApiResponseMessage(''); // Clear previous API response
    setRandomNumber(null); // Clear previous number

    if (!betType) {
      setResultMessage('Please place a bet first!');
      return;
    }

    const newNumber = generateNumber();
    setRandomNumber(newNumber);
    const winStatus = checkBet(newNumber); // Check bet locally
    await sendToBackend(betType, newNumber, winStatus); // Send data to simulated backend
  };

  // Function to set the bet type
  const placeBet = (type) => {
    setBetType(type);
    setResultMessage(''); // Clear previous result
    setApiResponseMessage(''); // Clear previous API response
    setRandomNumber(null); // Clear previous number
  };

  // Function to check if the bet wins (local logic for display)
  const checkBet = (number) => {
    let win = false;
    let category = '';

    // Determine category for display
    if (number >= 0 && number <= 4) {
      category = 'Small';
    } else {
      category = 'Big';
    }

    let colorCategory = '';
    if (number >= 0 && number <= 3) {
      colorCategory = 'Green';
    } else if (number >= 4 && number <= 6) {
      colorCategory = 'Yellow';
    } else {
      colorCategory = 'Red';
    }

    switch (betType) {
      case 'small':
        win = (number >= 0 && number <= 4);
        break;
      case 'big':
        win = (number >= 5 && number <= 9);
        break;
      case 'green':
        win = (number >= 0 && number <= 3);
        break;
      case 'yellow':
        win = (number >= 4 && number <= 6);
        break;
      case 'red':
        win = (number >= 7 && number <= 9);
        break;
      default:
        break;
    }

    if (win) {
      setResultMessage(`You Won! The number was ${number}. It's ${category} and ${colorCategory}.`);
    } else {
      setResultMessage(`You Lost! The number was ${number}. It's ${category} and ${colorCategory}.`);
    }
    return win ? 'win' : 'lose'; // Return win status for backend
  };

  // Simulate sending data to a Laravel backend API
  const sendToBackend = async (bet, resultNumber, winStatus) => {
    setLoading(true); // Start loading

    // In a real application, replace this with your actual Laravel API endpoint
    const apiUrl = 'https://your-laravel-backend.com/api/place-bet'; // Placeholder URL

    // Simulate API call with a delay
    try {
      // Simulate network request
      await new Promise(resolve => setTimeout(resolve, 1500)); // Simulate 1.5 seconds delay

      // Simulate a successful response
      const mockResponse = {
        success: true,
        message: 'Bet recorded successfully!',
        data: {
          betType: bet,
          rolledNumber: resultNumber,
          outcome: winStatus,
          timestamp: new Date().toISOString()
        }
      };

      // In a real app, you'd do:
      /*
      const response = await fetch(apiUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
          // Add authorization headers if needed, e.g., 'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
          bet_type: bet,
          rolled_number: resultNumber,
          outcome: winStatus
        })
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to record bet on backend.');
      }

      const data = await response.json();
      setApiResponseMessage(`Backend Response: ${data.message}`);
      */

      setApiResponseMessage(`Backend Response (Simulated): ${mockResponse.message}`);

    } catch (error) {
      console.error('Error sending data to backend:', error);
      setApiResponseMessage(`Backend Error (Simulated): ${error.message || 'Could not connect to backend.'}`);
    } finally {
      setLoading(false); // End loading
    }
  };

  // Helper function to get color class based on number
  const getNumberColorClass = (num) => {
    if (num >= 0 && num <= 3) return 'text-success';
    if (num >= 4 && num <= 6) return 'text-warning';
    if (num >= 7 && num <= 9) return 'text-danger';
    return 'text-dark';
  };

  return (
    <Container className="d-flex align-items-center justify-content-center min-vh-100" style={{ background: 'linear-gradient(135deg, #7b2ff2 0%, #f357a8 100%)' }}>
      <Row className="w-100 justify-content-center">
        <Col xs={12} md={8} lg={6}>
          <Card className="shadow-lg p-4 rounded-4">
            <Card.Body>
              <h1 className="text-center mb-4 fw-bold">Digit Bet Game</h1>
              <div className="mb-4">
                <Card className="bg-light text-center p-4">
                  <Card.Body>
                    {randomNumber !== null ? (
                      <span className={`display-1 fw-bold ${getNumberColorClass(randomNumber)}`}>{randomNumber}</span>
                    ) : (
                      <span className="display-4 text-muted">?</span>
                    )}
                  </Card.Body>
                </Card>
              </div>
              <div className="mb-3">
                <h4 className="text-center mb-3">Bet on Size</h4>
                <div className="d-flex justify-content-center gap-3">
                  <Button
                    variant={betType === 'small' ? 'primary' : 'outline-primary'}
                    size="lg"
                    className={betType === 'small' ? 'fw-bold shadow' : ''}
                    onClick={() => placeBet('small')}
                    disabled={loading}
                  >
                    Small (0-4)
                  </Button>
                  <Button
                    variant={betType === 'big' ? 'warning' : 'outline-warning'}
                    size="lg"
                    className={betType === 'big' ? 'fw-bold shadow' : ''}
                    onClick={() => placeBet('big')}
                    disabled={loading}
                  >
                    Big (5-9)
                  </Button>
                </div>
              </div>
              <div className="mb-3">
                <h4 className="text-center mb-3">Bet on Color</h4>
                <div className="d-flex justify-content-center gap-3 flex-wrap">
                  <Button
                    variant={betType === 'green' ? 'success' : 'outline-success'}
                    size="lg"
                    className={betType === 'green' ? 'fw-bold shadow' : ''}
                    onClick={() => placeBet('green')}
                    disabled={loading}
                  >
                    Green (0-3)
                  </Button>
                  <Button
                    variant={betType === 'yellow' ? 'warning' : 'outline-warning'}
                    size="lg"
                    className={betType === 'yellow' ? 'fw-bold shadow' : ''}
                    onClick={() => placeBet('yellow')}
                    disabled={loading}
                  >
                    Yellow (4-6)
                  </Button>
                  <Button
                    variant={betType === 'red' ? 'danger' : 'outline-danger'}
                    size="lg"
                    className={betType === 'red' ? 'fw-bold shadow' : ''}
                    onClick={() => placeBet('red')}
                    disabled={loading}
                  >
                    Red (7-9)
                  </Button>
                </div>
              </div>
              <div className="mb-3">
                <Button
                  variant="primary"
                  size="lg"
                  className="w-100 fw-bold"
                  onClick={handleRoll}
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
              {resultMessage && (
                <Alert variant={resultMessage.includes('Won') ? 'success' : resultMessage.includes('Lost') ? 'danger' : 'info'} className="text-center fw-bold">
                  {resultMessage}
                </Alert>
              )}
              {apiResponseMessage && (
                <Alert variant={apiResponseMessage.includes('Error') ? 'danger' : 'secondary'} className="text-center">
                  {apiResponseMessage}
                </Alert>
              )}
              <div className="mt-4 text-center text-muted">
                <div>
                  <strong>Current Bet:</strong> {betType ? betType.charAt(0).toUpperCase() + betType.slice(1) : 'None'}
                </div>
                <div>
                  <small>Select a bet (Small/Big or Color) and then click <b>"Roll the Dice!"</b></small>
                </div>
              </div>
            </Card.Body>
          </Card>
        </Col>
      </Row>
    </Container>
  );
}

export default DigitBetGame;
