import React, { useContext, useState } from 'react'
import SmallSpinner from "./SmallSpinner"
import { LanguageContext } from '../../contexts/LanguageContext';
import useFormSubmit from "../../hooks/useFormSubmit";
import BASE_URL from '../../hooks/baseUrl';
import { Spinner } from 'react-bootstrap';

const ChangePassword = () => {
  const { content } = useContext(LanguageContext);
  const [current_password, setCurrentPassword] = useState("");
  const [password, setPassword] = useState("");
  const [password_confirmation, setPasswordConfirmation] = useState("");

  const {inputSubmit, error, loading, errMsg} = useFormSubmit();
  const changePassword = async (e) => {
    e.preventDefault();
    let url = BASE_URL + "/change-password";
    let inputData = {current_password, password, password_confirmation};
    let method = "POST";
    let redirect = "/information?tab=profile";
    let msg = "Password changed successfully";
    await inputSubmit(url, inputData, method, redirect, msg);
  }

  return (
    <div>
      <form className="bg-white/10 backdrop-blur-sm mb-5 px-3 mt-4 py-4 rounded-2xl border border-white/20" onSubmit={changePassword}>
        <div className="flex justify-between items-center mb-3">
          <h5 className="font-bold text-white">{content?.profile?.change_password}</h5>
          {errMsg && (
            <div className="bg-red-500 text-white text-xs px-2 py-1 rounded ml-2">{errMsg}</div>
          )}
        </div>
        <div className="grid grid-cols-12 mb-2 items-center">
          <div className="col-span-5 mt-2 text-gray-300 font-medium">{content?.profile?.old_password}</div>
          <div className="col-span-7">
            <input type="password"
              className="w-full bg-transparent border-b border-gray-500 text-white focus:outline-none py-1"
              onChange={e => setCurrentPassword(e.target.value)}
              value={current_password}
            />
            {error && error.current_password && (<span className='text-red-400 text-xs'>*{error.current_password}</span>)}
          </div>
        </div>
        <div className="grid grid-cols-12 mb-2 items-center">
          <div className="col-span-5 mt-2 text-gray-300 font-medium">{content?.profile?.new_password}</div>
          <div className="col-span-7">
            <input type="password"
              className="w-full bg-transparent border-b border-gray-500 text-white focus:outline-none py-1"
              onChange={e => setPassword(e.target.value)}
              value={password}
            />
            {error && error.password && (<span className='text-red-400 text-xs'>*{error.password}</span>)}
          </div>
        </div>
        <div className="grid grid-cols-12 mb-2 items-center">
          <div className="col-span-5 mt-2 text-gray-300 font-medium">{content?.profile?.confirm_password}</div>
          <div className="col-span-7">
            <input type="password"
              className="w-full bg-transparent border-b border-gray-500 text-white focus:outline-none py-1"
              onChange={e => setPasswordConfirmation(e.target.value)}
              value={password_confirmation}
            />
            {error && error.password_confirmation && (<span className='text-red-400 text-xs'>*{error.password_confirmation}</span>)}
          </div>
        </div>
        <div className="text-right mt-3">
          <button className="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors" type="submit">
            {loading && <SmallSpinner className='inline-block mr-1' />}
            {content?.profile?.change_password}
          </button>
        </div>
      </form>
    </div>
  )
}

export default ChangePassword
