import React, { useContext, useState } from 'react';
// import { Modal, Spinner } from 'react-bootstrap'; // Removed react-bootstrap imports
import { EyeIcon, EyeOffIcon } from 'lucide-react';
import useLogin from "../hooks/useLogin"; // Original import restored
import BASE_URL from '../hooks/baseUrl'; // Original import restored
import { LanguageContext } from "../contexts/LanguageContext"; // Original import restored

const Login = ({ show, handleClose }) => {
  const { content, lan } = useContext(LanguageContext);
  const [pwType, setPwType] = useState('password');
  const togglePwType = () => {
    setPwType(pwType === 'text' ? 'password' : 'text');
  };

  const [user_name, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const { login, error, loading, errMsg } = useLogin();

  const handleLogin = async (e) => {
    e.preventDefault();
    let url = BASE_URL + "/login";
    let inputData = { user_name, password };
    const success = await login(url, inputData);
    if (success) {
      handleClose(); // Close modal on successful login
    }
  };

  // Render nothing if 'show' is false
  if (!show) {
    return null;
  }

  return (
    // Custom Modal Overlay (replaces react-bootstrap Modal)
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-[rgba(0,0,0,0.7)] bg-opacity-70 backdrop-blur-sm">
      {/* Modal Content */}
      <div className="bg-[#11121C] p-8 rounded-2xl w-full max-w-md text-white shadow-2xl border border-gray-700 relative">
        {/* Close Button */}
        <div className="text-right mb-2">
          <button
            onClick={handleClose}
            className="bg-transparent border-none text-xl text-gray-400 hover:text-white transition-colors duration-200"
          >
            &times;
          </button>
        </div>

        {/* Logo and Title */}
        <div className="text-center mb-6">
          {/* Assuming logo.png is in the public/images folder or accessible via a direct URL */}
          <img src={'1x2.png'} width={220} alt="logo" className=" mx-auto mb-3" />
          <h4 className="text-[#FFD700] font-bold text-2xl">{content?.auth?.login.toUpperCase()}</h4>
        </div>

        {/* Login Form */}
        <form onSubmit={handleLogin}>
          {/* Username Input */}
          <div className="flex mb-4 rounded-lg overflow-hidden border border-gray-600 focus-within:ring-2 focus-within:ring-teal-500">
            <input
              type="text"
              placeholder={content?.profile?.username}
              value={user_name}
              onChange={(e) => setUsername(e.target.value)}
              className="flex-1 p-3 bg-gray-700 text-white placeholder-gray-400 focus:outline-none"
            />
          </div>
          {error?.user_name && <span className="text-red-400 text-sm block mb-2">*{error.user_name}</span>}
          {!error?.user_name && errMsg && <span className="text-red-400 text-sm block mb-2">*{errMsg}</span>}

          {/* Password Input */}
          <div className="flex mb-4 rounded-lg overflow-hidden border border-gray-600 focus-within:ring-2 focus-within:ring-teal-500">
            <input
              type={pwType}
              placeholder={content?.auth?.password}
              value={password}
              onChange={e => setPassword(e.target.value)}
              className="flex-1 p-3 bg-gray-700 text-white placeholder-gray-400 focus:outline-none"
            />
            <div
              onClick={togglePwType}
              className="bg-gray-700 p-3 flex items-center justify-center cursor-pointer hover:bg-gray-600 transition-colors duration-200"
            >
              {pwType === 'text' ? <EyeIcon color="white" size={20} /> : <EyeOffIcon color="white" size={20} />}
            </div>
          </div>
          {error?.password && <span className='text-red-400 text-sm block mb-4'>*{error.password}</span>}

          {/* Submit Button */}
          <button
            type="submit"
            className={`w-full py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold text-xl transition-colors duration-200 shadow-md ${lan === "mm" ? "mm-font" : ""}`}
            disabled={loading}
          >
            {loading ? (
              <div className="flex items-center justify-center">
                {/* Custom Spinner for Tailwind (replaces react-bootstrap Spinner) */}
                <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                {content?.auth?.login.toUpperCase()}
              </div>
            ) : (
              content?.auth?.login.toUpperCase()
            )}
          </button>
        </form>

        {/* Optional: Close Button (if needed outside form) */}
        {/* <button type="button" onClick={handleClose} className="w-full py-2 mt-4 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-bold text-lg transition-colors duration-200">
          {lan === 'mm' ? 'ပိတ်မည်' : 'Close'}
        </button> */}
      </div>
    </div>
  );
};

export default Login;
