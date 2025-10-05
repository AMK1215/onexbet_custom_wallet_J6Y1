import React, { useContext, useEffect, useState } from 'react'
import { Button, Modal, Spinner } from 'react-bootstrap'
import { LanguageContext } from '../../contexts/LanguageContext'
import useFetch from '../../hooks/useFetch'
import BASE_URL from '../../hooks/baseUrl'
import { message } from 'antd'
import useFormSubmit from '../../hooks/useFormSubmit'


const Deposit = () => {
  const { content } = useContext(LanguageContext);
  const [show, setShow] = useState(false);
  const [amount, setAmount] = useState('');
  const [refrence_no, setRefrence_no] = useState('');
  const { data: banks } = useFetch(BASE_URL + "/banks");
  const [selectedBank, setSelectedBank] = useState(null);

  useEffect(() => {
    if (banks && banks.length > 0) {
      setSelectedBank(banks[0]);
    }
  }, [banks]);

  const handleCopyText = (e) => {
    e.preventDefault();
    if (selectedBank?.account_number) {
      navigator.clipboard.writeText(selectedBank.account_number);
      message.success('Copied to clipboard');
    }
  };

  const {inputSubmit, error, loading, errMsg} = useFormSubmit();
  const deposit = async (e) => {
    e.preventDefault();
    let url = BASE_URL + "/depositfinicial";
    let inputData = {
      'agent_payment_type_id': selectedBank?.id,
      amount, 
      refrence_no
    }
    let method = "POST";
    let redirect = "/information?tab=logs&type=deposit";
    let msg = "Deposit successfully";
    await inputSubmit(url, inputData, method, redirect, msg);
  }



  return (
    <div>
      <form 
      className="bg-white/10 backdrop-blur-sm px-3 py-4 rounded-2xl border border-white/20 w-full max-w-md mx-auto" 
      onSubmit={deposit}
      >
        <div className="d-flex justify-content-between">
          <h5 className="font-bold mb-3 text-white">{content?.wallet?.deposit}</h5>
          <div className="text-right">
            <button type="button" className="px-3 py-1 rounded-lg border border-yellow-400 text-yellow-400 bg-transparent hover:bg-yellow-400 hover:text-black transition mb-4" onClick={() => setShow(!show)}>{content?.wallet?.choose_bank}</button>
          </div>
        </div>

        {selectedBank && (
          <div className="border border-gray-300 bg-transparent rounded-2xl p-3 my-3 shadow-lg">
            <div className="flex justify-between items-center">
              <div className="flex items-center">
                <div>
                  <img
                    className="rounded-lg shadow w-12 h-12 object-cover"
                    src={"https://ag.onexbetmm.site" + selectedBank.image}
                    alt={selectedBank.payment_type}
                  />
                </div>
                <div className="ml-3">
                  <h6 className="font-bold text-white">{selectedBank.payment_type}</h6>
                  <h6 className="font-bold text-white">{selectedBank.account_number}</h6>
                </div>
              </div>
              <div>
                <button type="button" className="px-3 py-1 rounded-lg border border-white text-white bg-transparent hover:bg-white hover:text-black transition" onClick={handleCopyText}>
                  Copy
                </button>
              </div>
            </div>
          </div>
        )}

        <div className="row mb-2">
          <div className="col-span-5 mt-2 text-gray-300 font-medium">{content?.wallet?.amount} : </div>
          <div className="col-span-7">
            <input type="text"
              className="w-full bg-transparent border-b border-gray-500 text-white focus:outline-none py-1 placeholder-white" 
              placeholder={content?.wallet?.enter_amount}
              onChange={(e) => setAmount(e.target.value)}
              value={amount}
            />
            {error ? (error.amount && <span className='text-red-400 text-xs'>*{error.amount}</span>) : errMsg && <span className='text-red-400 text-xs'>*{errMsg}</span>}
          </div>
        </div>
        <div className="row mb-2">
          <div className="col-span-5 mt-2 text-gray-300 font-medium">{content?.wallet?.receipt}</div>
          <div className="col-span-7">
            <input
              type="text"
              className="w-full bg-transparent border-b border-gray-500 text-white focus:outline-none py-1 placeholder-white"
              placeholder={content?.wallet?.enter_last_6_digits}
              onChange={(e) => setRefrence_no(e.target.value)}
              value={refrence_no}
            />
            {error && error.refrence_no && <span className='text-red-400 text-xs'>*{error.refrence_no}</span>}
          </div>
        </div>
        <div className="text-end mt-3">
          <button 
            type='submit'
            className="px-4 py-2 bg-yellow-400 text-black rounded-lg font-medium hover:bg-yellow-500 transition-colors">
            {loading && <span className='inline-block mr-1 align-middle'><Spinner animation='border' size='sm' /></span>}
            {content?.wallet?.deposit}
          </button>
        </div>
      </form>

      {show && (
        <div className="fixed inset-0 z-50 flex items-center justify-center  bg-opacity-60 animate-fadeIn" style={{backgroundColor:"rgba(0,0,0,0.7)"}}>
          <div className="bg-white rounded-2xl shadow-2xl p-4 w-full max-w-xs relative">
            <div className="text-center mb-4">
              <h5 className="font-bold text-lg text-yellow-600">{content?.wallet?.choose_bank}</h5>
            </div>
            <div className="space-y-2 mb-4">
              {banks && banks.map((bank, index) => (
                <div key={index} onClick={() => {
                  setShow(false);
                  setSelectedBank(bank);
                }} className="flex gap-2 bg-gray-100 hover:bg-yellow-100 cursor-pointer p-2 rounded-lg text-black items-center">
                  <img src={"https://ag.onexbetmm.site" + bank.image} className="w-10 h-10 rounded-md object-cover" alt="bank" />
                  <div>
                    <p className="text-xs">{content?.wallet?.account} : {bank.account_number}</p>
                    <p className="text-xs">{content?.wallet?.account_name} : {bank.account_name}</p>
                  </div>
                </div>
              ))}
            </div>
            <button onClick={() => setShow(false)} className="w-full px-4 py-2 bg-yellow-400 text-black rounded-lg font-bold mt-2 hover:bg-yellow-500 transition">
              ပိတ်မည်
            </button>
          </div>
        </div>
      )}
    </div>
  )
}

export default Deposit


