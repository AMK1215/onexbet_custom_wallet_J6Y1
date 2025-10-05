// import React, { useContext, useState } from 'react'
// import { Link } from 'react-router-dom'
// import { EyeIcon, EyeOffIcon, PhoneCallIcon } from 'lucide-react'
// import { LanguageContext } from '../contexts/LanguageContext'
// import useRegister from "../hooks/useRegister";
// import BASE_URL from '../hooks/baseUrl';
// import { Spinner } from 'react-bootstrap';
// import logo from "../assets/img/logo.png"




// const RegisterPage = () => {
//   const { content, lan } = useContext(LanguageContext);
//   const [pwType, setPwType] = useState('password');
//   const [cpwType, setCpwType] = useState('password');
//   const togglePwType = () => {
//     setPwType(pwType === 'text' ? 'password' : 'text');
//   }
//   const toggleCPwType = () => {
//     setCpwType(cpwType === 'text' ? 'password' : 'text');
//   }

//   const [name, setName] = useState("");
//   const [phone, setPhone] = useState("");
//   const [password, setPassword] = useState("");
//   const [password_confirmation, setPasswordConfirmation] = useState("");
//   const [referral_code, setReferralCode] = useState("");
//   const { register, error, errMsg, loading } = useRegister();
//   const handleRegister = async (e) => {
//     e.preventDefault();
//     let url = BASE_URL + "/register";
//     let inputData = { name, phone, password, password_confirmation, referral_code };
//     await register(url, inputData);
//   }


//   return (
//     <div className='row justify-content-center px-4 mt-3'>
//       <div className="col-lg-3 col-md-5 col-sm-6 col-9">
//         <div>
//           <img src={logo} width={300} alt="" />
//           <h1 className={`fw-semibold text-warning2 text-center mb-4`}>{content?.auth?.register.toUpperCase()}</h1>
//         </div>
//         <form onSubmit={handleRegister}>
//           <div className="my-3">
//             <input type="text"
//               placeholder={content?.auth?.name}
//               className='w-full py-2 rounded-5 px-4'
//               onChange={(e) => setName(e.target.value)}
//               value={name}
//             />
//             {error && error.name && <span className='text-danger'>*{error.name}</span>}
//           </div>
//           <div className="my-3">
//             <div className="d-flex bg-white rounded-5 w-full">
//               <div className='rounded-start-5 d-flex align-items-center justify-content-center px-3' style={{ background: '#457C8F' }}>
//                 <PhoneCallIcon />
//               </div>
//               <input type={'text'}
//                 placeholder={content?.auth?.phone}
//                 className='rounded-end-5 w-full px-4 py-2'
//                 onChange={(e) => setPhone(e.target.value)}
//                 value={phone}
//               />
//             </div>
//             {error && error.phone && <span className='text-danger'>*{error.phone}</span>}
//           </div>
//           <div className="my-3">
//             <div className="d-flex bg-white rounded-5 w-full">
//               <input type={pwType}
//                 placeholder={content?.auth?.password}
//                 className='rounded-start-5 w-full px-4 py-2'
//                 onChange={(e) => setPassword(e.target.value)}
//                 value={password}
//               />
//               <div onClick={togglePwType} className='rounded-end-5 bg-secondary d-flex align-items-center justify-content-center px-3'>
//                 {pwType === 'text' ?
//                   <EyeIcon className='text-white ' /> :
//                   <EyeOffIcon className='text-white ' />
//                 }
//               </div>
//             </div>
//             {error && error.password && <span className='text-danger'>*{error.password}</span>}
//           </div>
//           <div className="my-3">
//             <div className="d-flex bg-white rounded-5 w-full">
//               <input
//                 type={cpwType}
//                 placeholder={content?.auth?.confirm_password}
//                 className='rounded-start-5 w-full px-4 py-2'
//                 onChange={(e) => setPasswordConfirmation(e.target.value)}
//                 value={password_confirmation}
//               />
//               <div onClick={toggleCPwType} className='rounded-end-5 bg-secondary d-flex align-items-center justify-content-center px-3'>
//                 {cpwType === 'text' ?
//                   <EyeIcon className='text-white ' /> :
//                   <EyeOffIcon className='text-white ' />
//                 }
//               </div>
//             </div>
//             {error && error.password_confirmation && <span className='text-danger'>*{error.password_confirmation}</span>}
//           </div>
//           <div className="my-3">
//             <input type="text"
//               placeholder={content?.auth?.ref_code}
//               className='w-full py-2 rounded-5 px-4'
//               onChange={(e) => setReferralCode(e.target.value)}
//               value={referral_code}
//             />
//             {error && error.referral_code && <span className='text-danger'>*{error.referral_code}</span>}
//           </div>
//           <button
//             type='submit'
//             className={`mb-3 py-1 rounded-4 bg-red button-bottom text-white w-100 ${lan === "mm" ? "mm-font" : ""}`}>
//             {loading && <Spinner size='sm' className='me-2' />}
//             {content?.auth?.register.toUpperCase()}
//           </button>
//           <Link to={'/login'}
//             className={`mb-3 py-1 rounded-4 bg-black2 button-bottom w-100 d-block text-center ${lan === "mm" ? "mm-font" : ""}`}>
//             {content?.auth?.login.toUpperCase()}
//           </Link>
//         </form>
//       </div>
//     </div>
//   )
// }

// export default RegisterPage


import React, { useContext, useState } from 'react';
import { Link } from 'react-router-dom';
import { EyeIcon, EyeOffIcon, PhoneCallIcon } from 'lucide-react';
import { LanguageContext } from '../contexts/LanguageContext';
import useRegister from "../hooks/useRegister";
import BASE_URL from '../hooks/baseUrl';
import { Spinner } from 'react-bootstrap';
// import logo from "./images/logo-P-jbhPrl.png";

const Register = ({ show, onClose }) => {
  const { content, lan } = useContext(LanguageContext);
  const [pwType, setPwType] = useState('password');
  const [cpwType, setCpwType] = useState('password');

  const [name, setName] = useState("");
  const [phone, setPhone] = useState("");
  const [password, setPassword] = useState("");
  const [password_confirmation, setPasswordConfirmation] = useState("");
  const [referral_code, setReferralCode] = useState("");
  const { register, error, loading } = useRegister();

  const handleRegister = async (e) => {
    e.preventDefault();
    let url = BASE_URL + "/register";
    let inputData = { name, phone, password, password_confirmation, referral_code };
    await register(url, inputData);
  };

  if (!show) return null;

  return (
    <div style={{
      position: 'fixed',
      top: 0, left: 0, right: 0, bottom: 0,
      backgroundColor: 'rgba(0,0,0,0.7)',
      display: 'flex',
      justifyContent: 'center',
      alignItems: 'center',
      zIndex: 9999
    }}>
      <div className="border border-gray-700 relative" style={{

        backgroundColor: '#11121C',
        borderRadius: '20px',
        padding: '30px',
        width: '100%',
        maxWidth: '400px',
        color: 'white',
        // boxShadow: '0 0 20px rgba(255, 215, 0, 0.3)'
      }}>
        <div className='text-end mb-2'>
          <button onClick={onClose} style={{
            background: 'transparent',
            border: 'none',
            fontSize: '1.2rem',
            color: '#bbb'
          }}>&times;</button>
        </div>
        <div className='text-center mb-3'>
          <img src={'1x2.png'} width={150} alt="Logo" className="mx-auto mb-3" />
          <h2 style={{ color: '#FFD700', fontWeight: 'bold' }}>{content?.auth?.register.toUpperCase()}</h2>
        </div>

        <form onSubmit={handleRegister}>
          <input type="text" placeholder={content?.auth?.name} value={name} onChange={e => setName(e.target.value)}
            style={{ width: '100%', padding: '10px', marginBottom: '10px', borderRadius: '10px' }} />
          {error?.name && <span className='text-danger'>*{error.name}</span>}

          <div style={{ display: 'flex', marginBottom: '10px', borderRadius: '10px', overflow: 'hidden' }}>
            <div style={{ background: '#457C8F', padding: '10px', display: 'flex', alignItems: 'center' }}>
              <PhoneCallIcon color='white' size={20} />
            </div>
            <input type="text" placeholder={content?.auth?.phone} value={phone} onChange={e => setPhone(e.target.value)}
              style={{ flex: 1, padding: '10px', border: 'none' }} />
          </div>
          {error?.phone && <span className='text-danger'>*{error.phone}</span>}

          <div style={{ display: 'flex', marginBottom: '10px', borderRadius: '10px', overflow: 'hidden' }}>
            <input type={pwType} placeholder={content?.auth?.password} value={password}
              onChange={e => setPassword(e.target.value)}
              style={{ flex: 1, padding: '10px', border: 'none' }} />
            <div onClick={() => setPwType(pwType === 'text' ? 'password' : 'text')}
              style={{ background: '#333', padding: '10px', cursor: 'pointer' }}>
              {pwType === 'text' ? <EyeIcon color="white" size={20} /> : <EyeOffIcon color="white" size={20} />}
            </div>
          </div>
          {error?.password && <span className='text-danger'>*{error.password}</span>}

          <div style={{ display: 'flex', marginBottom: '10px', borderRadius: '10px', overflow: 'hidden' }}>
            <input type={cpwType} placeholder={content?.auth?.confirm_password} value={password_confirmation}
              onChange={e => setPasswordConfirmation(e.target.value)}
              style={{ flex: 1, padding: '10px', border: 'none' }} />
            <div onClick={() => setCpwType(cpwType === 'text' ? 'password' : 'text')}
              style={{ background: '#333', padding: '10px', cursor: 'pointer' }}>
              {cpwType === 'text' ? <EyeIcon color="white" size={20} /> : <EyeOffIcon color="white" size={20} />}
            </div>
          </div>
          {error?.password_confirmation && <span className='text-danger'>*{error.password_confirmation}</span>}

          <input type="text" placeholder={content?.auth?.ref_code} value={referral_code}
            onChange={e => setReferralCode(e.target.value)}
            style={{ width: '100%', padding: '10px', marginBottom: '10px', borderRadius: '10px' }} />
          {error?.referral_code && <span className='text-danger'>*{error.referral_code}</span>}

          <button type='submit' disabled={loading}
            style={{
              width: '100%',
              padding: '10px',
              backgroundColor: '#FF0000',
              color: 'white',
              borderRadius: '10px',
              fontWeight: 'bold',
              marginBottom: '10px'
            }}>
            {loading && <Spinner size='sm' className='me-2' />}
            {content?.auth?.register.toUpperCase()}
          </button>
{/* 
          <Link to={'/login'} style={{
            display: 'block',
            textAlign: 'center',
            padding: '10px',
            borderRadius: '10px',
            backgroundColor: '#111',
            color: 'white',
            textDecoration: 'none'
          }}>
            {content?.auth?.login.toUpperCase()}
          </Link> */}
        </form>
      </div>
    </div>
  );
};

export default Register;

