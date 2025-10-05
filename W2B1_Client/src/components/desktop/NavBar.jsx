import { useContext, useEffect, useState } from "react";
//import "../../tailwind_css.css";
import Register from '../../pages/Register';
import Login from '../../pages/Login';
import "../../assets/css/navbar.css";
import { IoMdClose } from "react-icons/io";
import home from "../../assets/img/home.svg";
import logo from "../../assets/img/black_logo.png";
import money from "../../assets/img/money.png";
import register from "../../assets/img/register.svg";
import promotion from "../../assets/img/promotion.svg";
import profile from "../../assets/img/profile.svg";
import { Link, useNavigate } from "react-router-dom";
import LanguageDropdown from "../LanguageDropdown";
import tele from "../../assets/img/telew.svg";
import viber from "../../assets/img/viberw.svg";
import { AlignJustifyIcon } from "lucide-react";
import { LanguageContext } from "../../contexts/LanguageContext";
import { AuthContext } from "../../contexts/AuthContext";
import useFetch from "../../hooks/useFetch";
import BASE_URL from "../../hooks/baseUrl";
import fb from "../../assets/img/fb.png";
import useLogout from "../../hooks/useLogout";
import AdsVideo from "../../pages/AdsVideo";
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { FaExchangeAlt, FaGamepad, FaHome, FaUser, FaWallet, FaGift, FaPhone, FaVideo, FaSignOutAlt, FaMoneyBillWave } from 'react-icons/fa';
import SidebarLg from './SidebarLg';

function NavBar() {
  const { content } = useContext(LanguageContext);
  const { user, updateProfile } = useContext(AuthContext);
  const [showRegister, setShowRegister] = useState(false);
  const [showLogin, setShowLogin] = useState(false);
  const navigate = useNavigate();
  
  const navLinks = [
    { icon: FaHome, name: content?.nav?.home, link: "/?type=all" },
    { icon: FaUser, name: content?.profile?.my_profile, link: "/information?tab=profile" },
    { icon: FaWallet, name: content?.wallet?.wallet, link: "/information?tab=transfer" },
    { icon: FaGift, name: content?.nav?.promotion, link: "/promotion" },
    { icon: FaPhone, name: content?.nav?.contact, link: "/contact" },
    { icon: FaVideo, name: content?.nav?.ads_video, link: "/ads-video" },
  ];

  const [show, setShow] = useState(false);
  const [showExchangeModal, setShowExchangeModal] = useState(false);
  const [exchangeType, setExchangeType] = useState('mainToGame');
  const [exchangeAmount, setExchangeAmount] = useState('');
  const [exchangeLoading, setExchangeLoading] = useState(false);
  const [isScrolled, setIsScrolled] = useState(false);
  const [showSidebar, setShowSidebar] = useState(false);

  const { data } = useFetch(BASE_URL + "/contact");
  const { logout, loading } = useLogout();

  // Handle scroll effect for navbar
  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 10);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const handleLogout = async (e) => {
    e.preventDefault();
    await logout();
  };

  const handleOpenExchangeModal = () => {
    setShowExchangeModal(true);
    setExchangeAmount('');
  };

  const handleCloseExchangeModal = () => {
    setShowExchangeModal(false);
    setExchangeAmount('');
    setExchangeType('mainToGame');
  };

  const handleExchange = async () => {
    if (!exchangeAmount || exchangeAmount <= 0) {
      toast.error('Please enter a valid amount');
      return;
    }
    
    setExchangeLoading(true);
    const url = exchangeType === 'mainToGame' 
      ? `${BASE_URL}/exchange-main-to-game`
      : `${BASE_URL}/exchange-game-to-main`;

    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
        },
        body: JSON.stringify({ amount: Number(exchangeAmount) }),
      });
      
      const data = await res.json();
      
      if (res.ok && data.status === 'Request was successful.') {
        // Fetch latest user profile
        const profileRes = await fetch(`${BASE_URL}/user`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
          },
        });
        const profileData = await profileRes.json();
        if (profileData && profileData.data) {
          updateProfile(profileData.data);
        }
        toast.success(data.message || 'Exchange successful!');
        handleCloseExchangeModal();
      } else {
        toast.error(data.message || 'Exchange failed!');
      }
    } catch (e) {
      toast.error('Network error. Please try again.');
    }
    setExchangeLoading(false);
  };

  return (
    <>
      {/* Unified Responsive NavBar with Tailwind */}
      <nav className={`w-full z-30 sticky top-0 bg-[#181A29] shadow-lg transition-all duration-300 ${isScrolled ? 'shadow-2xl' : ''}`}>
        <div className="max-w-6xl mx-auto flex items-center justify-between px-2 sm:px-6 py-2 gap-2">
          {/* Logo */}
          <div className="flex items-center gap-2 cursor-pointer flex-shrink-0" onClick={() => navigate('/')}> 
            <img src={"1x.png"} alt="Logo" className="h-9 md:h-10 w-auto object-contain" />
          </div>

          {/* Balances & Exchange - Always Centered */}
          {user && (
            <div className="flex items-center gap-2 bg-[#23243a] rounded-xl px-3 md:px-4 py-1 md:py-2 shadow-inner flex-1 justify-center mx-2 min-w-0 overflow-x-auto">
              {/*<div className="flex items-center gap-1">*/}
              {/*  <FaMoneyBillWave className="text-yellow-400 mr-1" />*/}
              {/*  <span className="text-xs text-gray-400 font-semibold">M:</span>*/}
              {/*  <span className="text-lg md:text-xl text-white font-extrabold">{user?.main_balance}</span>*/}
              {/*</div>*/}
              {/*<button */}
              {/*  className="mx-2 p-2 rounded-full bg-gradient-to-r from-yellow-400 to-orange-400 text-white shadow hover:scale-110 transition"*/}
              {/*  onClick={handleOpenExchangeModal}*/}
              {/*  aria-label="Exchange between balances"*/}
              {/*>*/}
              {/*  <FaExchangeAlt />*/}
              {/*</button>*/}
              <div className="flex items-center gap-1">
                <FaGamepad className="text-yellow-400 mr-1" />
                {/*<span className="text-xs text-gray-400 font-semibold">G:</span>*/}
                <span className="text-lg md:text-xl text-white font-extrabold">{user?.balance}</span>
              </div>
            </div>
          )}

          {/* Right Section - Auth/User Controls and Hamburger */}
          <div className="flex items-center gap-2 flex-shrink-0">
            {!user ? (
              <>
                <button 
                  onClick={() => setShowLogin(true)} 
                  className="px-3 py-1 rounded-lg bg-[#23243a] text-white font-semibold hover:bg-yellow-400 hover:text-black transition text-xs md:text-base"
                >
                  {content?.auth?.login || "Login"}
                </button>
                <button
                  onClick={() => setShowRegister(true)} 
                  className="px-3 py-1 rounded-lg bg-yellow-400 text-black font-semibold hover:bg-yellow-500 transition text-xs md:text-base"
                >
                  {content?.auth?.register || "Join Now"}
            </button>
            </>
            ) : null}
            <LanguageDropdown />
            <button
              onClick={() => setShowSidebar(true)}
              className="p-2 rounded-lg bg-[#23243a] text-white hover:bg-yellow-400 hover:text-black transition"
              aria-label="Open sidebar menu"
            >
              <AlignJustifyIcon size={24} />
            </button>
          </div>
        </div>
      </nav>
      {/* Sidebar for all devices */}
      <SidebarLg show={showSidebar} onClose={() => setShowSidebar(false)} />
      <Login show={showLogin} handleClose={() => setShowLogin(false)} />
      <Register show={showRegister} onClose={() => setShowRegister(false)} />
      <ToastContainer position="top-center" autoClose={2000} />

      {/* Exchange Modal */}
      {showExchangeModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 animate-fadeIn">
          <div className="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm relative flex flex-col items-center">
            <button
              className="absolute top-3 right-3 text-gray-400 hover:text-black focus:outline-none"
              onClick={handleCloseExchangeModal}
              aria-label="Close"
            >
              <IoMdClose size={24} />
            </button>
            <div className="flex flex-col items-center mb-4">
              <FaExchangeAlt className="text-yellow-400 text-3xl mb-1" />
              <h3 className="text-xl font-bold text-gray-800">Exchange Balance</h3>
            </div>
            <div className="flex w-full justify-between mb-4">
              <div className="flex flex-col items-center flex-1">
                <span className="text-xs text-gray-500">Main</span>
                <span className="font-bold text-lg text-yellow-500">{user?.main_balance}</span>
              </div>
              <div className="flex flex-col items-center flex-1">
                <span className="text-xs text-gray-500">Game</span>
                <span className="font-bold text-lg text-blue-500">{user?.balance}</span>
              </div>
            </div>
            <div className="flex w-full justify-center gap-2 mb-4">
              <button
                className={`flex-1 py-2 rounded-lg font-semibold border transition text-sm ${exchangeType === 'mainToGame' ? 'bg-yellow-400 text-black border-yellow-400 shadow' : 'bg-gray-100 text-gray-600 border-gray-200'}`}
                onClick={() => setExchangeType('mainToGame')}
                type="button"
                aria-label="Main to Game"
              >
                Main → Game
              </button>
              <button
                className={`flex-1 py-2 rounded-lg font-semibold border transition text-sm ${exchangeType === 'gameToMain' ? 'bg-blue-500 text-white border-blue-500 shadow' : 'bg-gray-100 text-gray-600 border-gray-200'}`}
                onClick={() => setExchangeType('gameToMain')}
                type="button"
                aria-label="Game to Main"
              >
                Game → Main
              </button>
            </div>
            <div className="w-full mb-4">
              <label className="block text-sm font-medium text-gray-700 mb-1">Amount</label>
              <input
                type="number"
                min="1"
                inputMode="numeric"
                className="w-full border-2 border-gray-200 rounded-lg px-3 py-2 text-lg focus:outline-none focus:border-yellow-400 transition"
                value={exchangeAmount}
                onChange={e => setExchangeAmount(e.target.value)}
                placeholder="Enter amount"
                autoFocus
              />
            </div>
            {/* Error message (if any) */}
            {/* You can add error state and show here if needed */}
            <div className="flex w-full gap-2 mt-2">
              <button
                className="flex-1 bg-gray-200 text-gray-700 rounded-lg py-2 font-semibold hover:bg-gray-300 transition"
                onClick={handleCloseExchangeModal}
                type="button"
              >
                Cancel
              </button>
              <button
                className={`flex-1 rounded-lg py-2 font-semibold flex items-center justify-center transition ${(!exchangeAmount || exchangeLoading) ? 'bg-yellow-200 text-gray-400 cursor-not-allowed' : 'bg-yellow-400 text-black hover:bg-yellow-500'}`}
                onClick={handleExchange}
                type="button"
                disabled={!exchangeAmount || exchangeLoading}
                aria-disabled={!exchangeAmount || exchangeLoading}
              >
                {exchangeLoading && <span className="loader mr-2"></span>}
                Exchange
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  );
}

export default NavBar;
