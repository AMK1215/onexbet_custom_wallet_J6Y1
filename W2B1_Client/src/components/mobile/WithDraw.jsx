import React, { useContext, useState } from 'react'
import { ToastContainer, toast } from 'react-toastify';
import { Spinner } from 'react-bootstrap';
import { AuthContext } from '../../contexts/AuthContext';
import useFetch from '../../hooks/useFetch';
import BASE_URL from '../../hooks/baseUrl';
import useFormSubmit from '../../hooks/useFormSubmit';
import { LanguageContext } from '../../contexts/LanguageContext';

const WithDraw = () => {
  const { user } = useContext(AuthContext);
  const { content } = useContext(LanguageContext);
  const { data: banks } = useFetch(BASE_URL + "/banks");
  const [payment, setPayment] = useState('');
  const [account_name, setAccountName] = useState('');
  const [account_number, setAccountNumber] = useState('');
  const [amount, setAmount] = useState('');
  const [password, setPassword] = useState('');

  const { inputSubmit, error, loading, errMsg } = useFormSubmit();
  const withdraw = async (e) => {
    e.preventDefault();
    let url = BASE_URL + "/withdrawfinicial";
    let inputData = { payment_type_id: payment, account_name, account_number, amount, password };
    let method = "POST";
    let redirect = "/information?tab=logs&type=withdraw";
    let msg = "Withdraw Success";
    await inputSubmit(url, inputData, method, redirect, msg);
  }

  return (
    <div>
      <ToastContainer />
      <div className="bg-white/10 backdrop-blur-sm px-3 py-4 rounded-2xl border border-white/20 w-full max-w-md mx-auto">
        <form onSubmit={withdraw}>
          <div className="grid grid-cols-12 mb-2 items-center">
            <div className="col-span-5 mt-2 text-gray-300 font-medium">{content?.wallet?.balance} : </div>
            <div className="col-span-7">
              <input type="text"
                className="w-full bg-transparent border-b border-gray-500 text-white focus:outline-none py-1"
                disabled
                value={user && Number(user.balance).toLocaleString()} />
            </div>
          </div>
          <div className="grid grid-cols-12 mb-2 items-center">
            <div className="col-span-5 mt-2 text-gray-300 font-medium">{content?.wallet?.choose_bank} :</div>
            <div className="col-span-7">
              <select className="w-full bg-gray-800 text-white border-b border-gray-500 rounded-lg py-1 px-2 focus:outline-none" onChange={e => setPayment(e.target.value)} value={payment}>
                <option value="">{content?.wallet?.choose_bank}</option>
                {banks && banks.map((item, index) => (
                  <option key={index} value={item.payment_type_id}>{item.payment_type}</option>
                ))}
              </select>
              {error && error.payment_type_id && <span className="text-red-400 text-xs block mt-1">*ငွေပေးချေမှုနည်းလမ်း ရွေးချယ်ပါ။</span>}
            </div>
          </div>
          <div className="grid grid-cols-12 mb-2 items-center">
            <div className="col-span-5 mt-2 text-gray-300 font-medium">{content?.wallet?.account_name} : </div>
            <div className="col-span-7">
              <input type="text"
                className="w-full bg-transparent border-b border-gray-500 text-white focus:outline-none py-1 placeholder-white"
                placeholder={content?.wallet?.enter_account_name}
                value={account_name}
                onChange={(e) => setAccountName(e.target.value)}
              />
              {error && error.account_name && <span className="text-red-400 text-xs">*{error.account_name}</span>}
            </div>
          </div>
          <div className="grid grid-cols-12 mb-2 items-center">
            <div className="col-span-5 mt-2 text-gray-300 font-medium">{content?.wallet?.account}: </div>
            <div className="col-span-7">
              <input type="text"
                className="w-full bg-transparent border-b border-gray-500 text-white focus:outline-none py-1 placeholder-white"
                placeholder={content?.wallet?.enter_account}
                value={account_number}
                onChange={(e) => setAccountNumber(e.target.value)}
              />
              {error && error.account_number && <span className="text-red-400 text-xs">*{error.account_number}</span>}
            </div>
          </div>
          <div className="grid grid-cols-12 mb-2 items-center">
            <div className="col-span-5 mt-2 text-gray-300 font-medium">{content?.wallet?.amount} : </div>
            <div className="col-span-7">
              <input type="number"
                className="w-full bg-transparent border-b border-gray-500 text-white focus:outline-none py-1 placeholder-white"
                placeholder={content?.wallet?.enter_amount}
                value={amount}
                onChange={(e) => setAmount(e.target.value)}
              />
              {error && error.amount && <span className="text-red-400 text-xs">*{error.amount}</span>}
            </div>
          </div>
          <div className="grid grid-cols-12 mt-2 items-center">
            <div className="col-span-5 mt-2 text-gray-300 font-medium">{content?.auth?.password} : </div>
            <div className="col-span-7">
              <input type="password"
                className="w-full bg-transparent border-b border-gray-500 text-white focus:outline-none py-1 placeholder-white"
                placeholder={content?.auth?.enter_password}
                value={password}
                onChange={(e) => setPassword(e.target.value)}
              />
              {error && error.password && <span className="text-red-400 text-xs">*{error.password}</span>}
            </div>
          </div>
          <div className="text-end mt-3">
            {loading ? <Spinner /> :
              <button type='submit' className="px-4 py-2 bg-yellow-400 text-black rounded-lg font-medium hover:bg-yellow-500 transition-colors">
                {content?.wallet?.withdraw}
              </button>}
          </div>
        </form>
      </div>

    </div>
  )
}

export default WithDraw