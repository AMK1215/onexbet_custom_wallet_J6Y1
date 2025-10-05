import React from 'react';
import { Card } from 'react-bootstrap';
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

export default function NumberDisplay({ number }) {
  return (
    <div className="mb-4">
      <Card className="bg-light text-center p-4">
        <Card.Body>
          {number !== null ? (
            <img
              src={digitImages[number]}
              alt={number}
              style={{ width: '90px', height: '90px', objectFit: 'contain' }}
            />
          ) : (
            <span className="display-4 text-muted">?</span>
          )}
        </Card.Body>
      </Card>
    </div>
  );
}
