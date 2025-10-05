import { useContext, useState } from 'react';
import { IoMdClose } from "react-icons/io";
import logo from '../../assets/img/logo.png';
import { useNavigate } from 'react-router-dom';
import LanguageDropdown from '../LanguageDropdown';
import { AlignJustifyIcon } from 'lucide-react';
import { LanguageContext } from '../../contexts/LanguageContext';
import { AuthContext } from '../../contexts/AuthContext';
import Register from '../../pages/Register';
import Login from '../../pages/Login';
import useFetch from '../../hooks/useFetch';
import BASE_URL from '../../hooks/baseUrl';
import useLogout from '../../hooks/useLogout';
import home from '../../assets/img/home.svg';
import money from '../../assets/img/money.png';
import promotion from '../../assets/img/promotion.svg';
import profile from '../../assets/img/profile.svg';
import fb from '../../assets/img/fb.png';
import tele from '../../assets/img/telew.svg';
import viber from '../../assets/img/viberw.svg';
import { FaSignOutAlt, FaExchangeAlt, FaGamepad, FaMoneyBillWave } from 'react-icons/fa';
 
function Navbar() {
  const { content } = useContext(LanguageContext);
  const { user } = useContext(AuthContext);
  const [showRegister, setShowRegister] = useState(false);
  const [showLogin, setShowLogin] = useState(false);
  const navigate = useNavigate();
  const navLinks = [
    { img: home, name: content?.nav?.home, link: '/' },
    { img: profile, name: content?.profile?.my_profile, link: '/information?tab=profile' },
    { img: money, name: content?.wallet?.wallet, link: '/information?tab=transfer' },
    { img: promotion, name: content?.nav?.promotion, link: '/promotion' },
  ];
  const [show, setShow] = useState(false);
  const [showExchangeModal, setShowExchangeModal] = useState(false);

  const { data } = useFetch(BASE_URL + "/contact");
  const contacts = data?.map((contact) => ({
    ...contact,
    image: contact.name === "Viber"
      ? viber
      : contact.name === "Telegram"
        ? tele
        : contact.name === "Facebook"
          ? fb
          : null,
  }));

  const { logout, loading } = useLogout();
  const handleLogout = async (e) => {
    e.preventDefault();
    await logout();
  }

  return (
    <>
      <nav className="w-full z-30 sticky top-0 bg-[#181A29] shadow-lg px-2 py-2 flex items-center justify-between">
        {/* Logo */}
        <div className="flex items-center gap-2 cursor-pointer" onClick={() => navigate('/')}> 
          <img src={logo} alt="Logo" className="h-8 w-auto object-contain" />
        </div>
        {/* Balances & Exchange */}
        {user && (
          <div className="flex items-center gap-2 bg-[#23243a] rounded-xl px-3 py-1 shadow-inner">
            <div className="flex items-center gap-1">
              <FaMoneyBillWave className="text-yellow-400 mr-1" />
              <span className="text-xs text-gray-400 font-semibold">M:</span>
              <span className="text-white font-bold">{user?.main_balance}</span>
            </div>
            <button
              className="mx-2 p-2 rounded-full bg-gradient-to-r from-yellow-400 to-orange-400 text-white shadow hover:scale-110 transition"
              onClick={() => setShowExchangeModal(true)}
              aria-label="Exchange between balances"
            >
              <FaExchangeAlt />
            </button>
            <div className="flex items-center gap-1">
              <FaGamepad className="text-yellow-400 mr-1" />
              <span className="text-xs text-gray-400 font-semibold">G:</span>
              <span className="text-white font-bold">{user?.balance}</span>
            </div>
          </div>
        )}
        <div className="flex items-center gap-2">
            {!user && (
              <>
                <button
                  onClick={() => setShowLogin(true)}
                className="px-3 py-1 rounded-lg bg-[#23243a] text-white font-semibold hover:bg-yellow-400 hover:text-black transition text-xs"
                  aria-label="Login"
                >
                  Login
                </button>
                <Login show={showLogin} handleClose={() => setShowLogin(false)} />
                <button
                  onClick={() => setShowRegister(true)}
                className="px-3 py-1 rounded-lg bg-yellow-400 text-black font-semibold hover:bg-yellow-500 transition text-xs"
                  aria-label="Join Now"
                >
                  Join Now
                </button>
                <Register show={showRegister} onClose={() => setShowRegister(false)} />
              </>
            )}
            <LanguageDropdown />
            <button
              onClick={() => setShow(true)}
            className="p-2 rounded-lg bg-[#23243a] text-white hover:bg-yellow-400 hover:text-black transition"
              aria-label="Open menu"
            >
              <AlignJustifyIcon size={22} />
            </button>
        </div>
      </nav>
      {/* Mobile Drawer/Sidebar */}
      {show && (
        <div className="fixed inset-0 z-40 flex">
          {/* Overlay */}
          <div className="fixed inset-0 bg-black/60" onClick={() => setShow(false)} />
          {/* Drawer */}
          <div className="relative ml-auto w-72 max-w-full h-full bg-[#1e1e1e] text-white rounded-l-2xl shadow-2xl flex flex-col">
            <div className="flex items-center justify-between px-4 py-3 border-b border-gray-700">
              <span className="text-lg font-bold">Menu</span>
              <button
                onClick={() => setShow(false)}
                className="p-1 rounded hover:bg-gray-700 transition"
                aria-label="Close menu"
              >
                <IoMdClose size={26} />
              </button>
            </div>
            <div className="flex-1 overflow-y-auto px-4 py-3">
            {navLinks.map((item, index) => (
                <button
                key={index}
                onClick={() => {
                  navigate(item.link);
                  setShow(false);
                }}
                  className="flex items-center w-full gap-3 py-2 px-2 rounded-lg hover:bg-gray-800 transition mb-1"
              >
                <img
                  src={item.img}
                  alt={`${item.name} icon`}
                    className="w-6 h-6 object-contain"
                  />
                  <span className="text-base font-medium">{item.name}</span>
                </button>
              ))}
              <hr className="my-3 border-gray-700" />
            {user && (
                <button
                  onClick={handleLogout}
                  className="flex items-center gap-2 py-2 px-2 rounded-lg hover:bg-red-600 transition w-full text-left text-red-300 font-semibold"
                >
                  <span><FaSignOutAlt /></span>
                  <span>Logout</span>
                </button>
              )}
            </div>
              </div>
        </div>
      )}
    </>
  );
}

export default Navbar;