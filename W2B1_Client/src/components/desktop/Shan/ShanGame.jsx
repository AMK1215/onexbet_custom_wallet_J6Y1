import React, { useState, useEffect } from 'react';
import { Button, Spinner, Alert, Card } from 'react-bootstrap';
import shanImg from './assets/img/shan.jpg';

export default function ShanGame({ operator_code = "a3h1" }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(null);
  const [fetchingUser, setFetchingUser] = useState(true);

  useEffect(() => {
    // Fetch user info on mount
    setFetchingUser(true);
    fetch('https://gamestar77.online/api/user', {
      headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('token'),
        'Accept': 'application/json'
      }
    })
      .then(res => res.json())
      .then(data => {
        setUser(data.data);
        setFetchingUser(false);
      })
      .catch(() => setFetchingUser(false));
  }, []);

  const handleLaunchGame = async () => {
    setLoading(true);
    setError(null);
    setSuccess(null);
    try {
      const response = await fetch('https://gamestar77.online/api/shan-launch-game', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ' + localStorage.getItem('token'),
          'Accept': 'application/json'
        },
        body: JSON.stringify({ member_account: user?.member_account || user?.user_name, operator_code })
      });
      const data = await response.json();
      if (data.status === 'success' && data.launch_game_url) {
        setSuccess('Game launched successfully!');
        window.open(data.launch_game_url, '_blank');
      } else if (data.status === 'fail' && data.errors && data.errors.operator_code) {
        setError(data.errors.operator_code.join(' '));
      } else {
        setError(data.message || data.error_detail || 'Failed to launch game.');
      }
    } catch (err) {
      setError('Network or server error.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="flex flex-col items-center justify-center min-h-[400px] py-8">
      <div className="relative w-full max-w-md mx-auto bg-gradient-to-br from-yellow-400/70 via-white/10 to-yellow-600/70 p-1 rounded-3xl shadow-2xl">
        <div className="w-full h-full rounded-3xl bg-[#181A29] p-6">
          {/* Spinner overlay during launch */}
          {loading && (
            <div className="absolute inset-0 bg-white/70 z-20 flex items-center justify-center rounded-3xl">
              <div className="w-16 h-16 border-4 border-yellow-400 border-t-transparent rounded-full animate-spin" />
            </div>
          )}
          <div className="flex flex-col items-center mb-4">
            <img src={shanImg} alt="Shan Game" className="w-full max-w-[220px] rounded-2xl object-cover shadow-lg mb-2" />
            <h2 className="text-2xl font-extrabold text-yellow-400 mb-2 tracking-wide drop-shadow text-center">Shan Game</h2>
          </div>
          {fetchingUser ? (
            <div className="flex flex-col items-center my-4">
              <div className="w-8 h-8 border-4 border-yellow-400 border-t-transparent rounded-full animate-spin mb-2" />
              <div className="text-gray-300">Loading user info...</div>
            </div>
          ) : user ? (
            <>
              <div className="mb-4 grid grid-cols-1 gap-2 text-center text-sm text-gray-200">
                <div><span className="font-semibold text-yellow-300">Account:</span> <span className="font-mono">{user.member_account || user.user_name}</span></div>
                <div><span className="font-semibold text-yellow-300">Balance:</span> <span className="font-mono">{user.balance ? parseFloat(user.balance).toLocaleString(undefined, { minimumFractionDigits: 2 }) : '0.00'}</span></div>
                <div><span className="font-semibold text-yellow-300">Product Code:</span> <span className="font-mono">{operator_code}</span></div>
              </div>
              {error && <div className="mb-2 px-3 py-2 bg-red-100 text-red-700 rounded-lg text-center text-sm font-semibold shadow">{error}</div>}
              {success && <div className="mb-2 px-3 py-2 bg-green-100 text-green-700 rounded-lg text-center text-sm font-semibold shadow">{success}</div>}
              <button
                onClick={handleLaunchGame}
                disabled={loading}
                className="w-full mb-2 px-4 py-3 rounded-full bg-gradient-to-r from-orange-400 to-yellow-500 text-white font-bold text-lg shadow hover:scale-105 transition focus:outline-none focus:ring-2 focus:ring-yellow-400 disabled:opacity-60 disabled:cursor-not-allowed"
              >
                {loading ? <span className="inline-block w-5 h-5 border-2 border-yellow-400 border-t-transparent rounded-full animate-spin align-middle" /> : 'Launch Shan Game'}
              </button>
            </>
          ) : (
            <div className="mb-2 px-3 py-2 bg-red-100 text-red-700 rounded-lg text-center text-sm font-semibold shadow">Failed to load user info. Please log in again.</div>
          )}
        </div>
      </div>
    </div>
  );
}
