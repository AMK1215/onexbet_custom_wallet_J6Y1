import React, { useState, useContext } from 'react';
import { TbPlus, TbRefresh } from "react-icons/tb";
import bag from '../assets/img/moneyBag.png';
import shoppingBag from '../assets/img/shoppingBag.png';
import { Link, useLocation } from 'react-router-dom';
import { OverlayTrigger, Tooltip, Spinner } from 'react-bootstrap';
import axios from 'axios';
import '../assets/css/userWallet.css';
import { AuthContext } from '../contexts/AuthContext';

const UserWallet = () => {
  const location = useLocation();
  const except = ['/2d/bet', '/3d/bet'];
  const [loading, setLoading] = useState(false);
  const { user, updateProfile } = useContext(AuthContext);

  // Get token from localStorage (adjust key as needed)
  const token = localStorage.getItem('token');

  // Refresh wallet balances and update context
  const handleRefresh = async () => {
    setLoading(true);
    try {
      const res = await axios.get('/api/user', {
        headers: {
          Authorization: token ? `Bearer ${token}` : undefined,
        },
        params: {
          type: 'main'
        }
      });
      if (res.data && res.data.data) {
        updateProfile({ ...user, ...res.data.data });
      }
    } catch (err) {
      // Optionally handle error
    }
    setLoading(false);
  };

  // Skeleton loader
  const Skeleton = () => (
    <div className="placeholder-glow w-100">
      <span className="placeholder col-6"></span>
    </div>
  );

  // Modern gradient and color theme
  const cardGradient = {
    background: 'linear-gradient(90deg, #20e3b2 0%, #4f8cff 60%, #a259ff 100%)',
    color: 'white',
    borderRadius: '1.2rem',
    boxShadow: '0 4px 24px 0 rgba(80, 80, 200, 0.10)'
  };

  const iconColor = "#20e3b2"; // Teal for plus
  const refreshColor = "#a259ff"; // Purple for refresh

  return (
    <div className="userWalletContainer my-3">
      <div className="card shadow border-0" style={cardGradient}>
        <div className="card-body p-3">
          <div className="d-flex justify-content-between align-items-center mb-2">
            <div className="d-flex align-items-center gap-2">
              <img src={shoppingBag} alt="Main Wallet" style={{ width: 28, height: 28 }} />
              <span className="fw-bold" style={{ color: "#f8f9fa" }}>ကျွန်ုပ်ပိုက်ဆံအိတ်</span>
            </div>
            <div className="d-flex align-items-center gap-2">
              <h5 style={{fontSize:'18px'}} className='fw-bold mt-2'>
                {loading ? <Spinner animation="border" size="sm" /> : (user?.main_balance !== undefined && user?.main_balance !== null ? Number(user.main_balance).toLocaleString() : 0)} Ks
              </h5>
              <OverlayTrigger placement="top" overlay={<Tooltip>ပိုက်ဆံထည့်မည်</Tooltip>}>
                <Link to="/wallet/internal-transfer" className="btn btn-light btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center" style={{ width: 32, height: 32 }}>
                  <TbPlus size={18} color={iconColor} />
                </Link>
              </OverlayTrigger>
              <OverlayTrigger placement="top" overlay={<Tooltip>Refresh</Tooltip>}>
                <button className="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center" style={{ width: 32, height: 32 }} onClick={handleRefresh} disabled={loading}>
                  <TbRefresh size={18} color={refreshColor} />
                </button>
              </OverlayTrigger>
            </div>
          </div>
          {!except.includes(location.pathname) && (
            <div className="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-light">
              <div className="d-flex align-items-center gap-2">
                <img src={bag} alt="Game Wallet" style={{ width: 24, height: 24 }} />
                <span className="fw-bold" style={{ color: "#f8f9fa" }}>ဂိမ်းပိုက်ဆံအိတ်</span>
              </div>
              <div className="d-flex align-items-center gap-2">
                <h5 style={{fontSize:'18px'}} className='fw-bold mt-2'>
                  {loading ? <Spinner animation="border" size="sm" /> : (user?.balance !== undefined && user?.balance !== null ? Number(user.balance).toLocaleString() : 0)} Ks
                </h5>
                <OverlayTrigger placement="top" overlay={<Tooltip>ပိုက်ဆံထည့်မည်</Tooltip>}>
                  <Link to="/wallet/internal-transfer" className="btn btn-light btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center" style={{ width: 28, height: 28 }}>
                    <TbPlus size={16} color={iconColor} />
                  </Link>
                </OverlayTrigger>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default UserWallet;
