import React from 'react';
import { Button } from 'react-bootstrap';
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

export function BetSizeButtons({ betType, placeBet, loading }) {
  return (
    <div className="mb-3">
      <h4 className="text-center mb-3">Bet on Size</h4>
      {/* Digits 0-4 */}
      <div className="d-flex justify-content-center mb-2 gap-2">
        {[0, 1, 2, 3, 4].map(num => (
          <img
            key={num}
            src={digitImages[num]}
            alt={num}
            style={{ width: 40, height: 40, objectFit: 'contain' }}
          />
        ))}
      </div>
      {/* Digits 5-9 */}
      <div className="d-flex justify-content-center mb-3 gap-2">
        {[5, 6, 7, 8, 9].map(num => (
          <img
            key={num}
            src={digitImages[num]}
            alt={num}
            style={{ width: 40, height: 40, objectFit: 'contain' }}
          />
        ))}
      </div>
      {/* Big/Small Buttons */}
      <div className="d-flex justify-content-center gap-3">
        <Button
          variant={betType === 'big' ? 'warning' : 'outline-warning'}
          size="lg"
          className={betType === 'big' ? 'fw-bold shadow' : ''}
          onClick={() => placeBet('big')}
          disabled={loading}
        >
          Big (5-9)
        </Button>
        <Button
          variant={betType === 'small' ? 'primary' : 'outline-primary'}
          size="lg"
          className={betType === 'small' ? 'fw-bold shadow' : ''}
          onClick={() => placeBet('small')}
          disabled={loading}
        >
          Small (0-4)
        </Button>
      </div>
    </div>
  );
}

export function BetColorButtons({ betType, placeBet, loading }) {
  return (
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
  );
}
