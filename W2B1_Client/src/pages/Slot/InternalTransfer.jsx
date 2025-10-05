import React, { useContext, useEffect, useState } from 'react'
import UserWallet from '../../components/UserWallet'
import BASE_URL from '../../hooks/baseUrl';
import { ToastContainer, toast } from 'react-toastify';
import SmallSpinner from '../../components/Loader/SmallSpinner';
import { AuthContext } from '../../contexts/AuthContext';
import { Link, useNavigate } from 'react-router-dom';

export default function InternalTransfer() {
    const [amount, setAmount] = useState(0);
    const [error, setError] = useState(null);
    const [loader, setLoader] = useState(false);
    const { auth, updateProfile } = useContext(AuthContext);
    const navigate = useNavigate();
    const [gameToMain, setGameToMain] = useState(false);
    const transferUrl = `${BASE_URL}${gameToMain ? '/exchange-game-to-main' : '/exchange-main-to-game'}`;

    useEffect(() => {
      if (!auth) {
        navigate('/login');
      }
    }, [auth, navigate]);

    const transfer = (e) => {
        e.preventDefault();
        setLoader(true);
        if(amount <= 0){
            setError("ပမာဏ ရိုက်ထည့်ပါ။");
            setLoader(false);
            return;
        }else{
            setError("");
        }
        let inputData = {
            amount: amount,
        }

        fetch(transferUrl , {
            method: "POST",
            headers: {
                "Accept" : "application/json",
                "Content-Type": "application/json",
                "Authorization": "Bearer " + localStorage.getItem("token")
            },
            body: JSON.stringify(inputData)
        })
        .then(res => {
            if (!res.ok) {
                setLoader(false);
                throw Error("Something Went Wrong!");
            }
            return res.json();
        })
        .then(data => {
            setLoader(false);
            toast.success("လွှဲပြောင်းခြင်း အောင်မြင်ပါသည်။");
            setAmount(0);
            // Fetch latest user profile and update context
            fetch(BASE_URL + "/user", {
                headers: {
                    "Authorization": "Bearer " + localStorage.getItem("token"),
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(profileData => {
                if (profileData && profileData.data) {
                    updateProfile(profileData.data);
                }
            });
        })
    }

    return (
    <div>
        <ToastContainer />
        <h6 className="text-center my-3">ပိုက်ဆံအိတ် လွှဲပြောင်းခြင်း။</h6>
        <UserWallet />
        {!gameToMain && (
            <div className="d-flex justify-content-around my-4">
                <div className='text-center'>
                    <small>ကျွန်ုပ်ပိုက်ဆံအိတ်</small>
                    <p>မှ</p>
                </div>
                <div className='cursorPointer' onClick={()=>setGameToMain(!gameToMain)}>
                    <div></div>
                    <button className="btn btn-sm btn-light">
                        <i className="fa-solid fa-arrow-right-arrow-left"></i>
                    </button>
                </div>
                <div className='text-center'>
                    <small>ဂိမ်းပိုက်ဆံအိတ်</small>
                    <p>သို့</p>
                </div>
            </div>
        )}
        {gameToMain && (
            <div className="d-flex justify-content-around my-4">
                <div className='text-center'>
                    <small>ဂိမ်းပိုက်ဆံအိတ်</small>
                    <p>မှ</p>
                </div>
                <div className='cursorPointer' onClick={()=>setGameToMain(!gameToMain)}>
                    <div></div>
                    <button className="btn btn-sm btn-light">
                        <i className="fa-solid fa-arrow-right-arrow-left"></i>
                    </button>
                </div>
                <div className='text-center'>
                    <small>ကျွန်ုပ်ပိုက်ဆံအိတ်</small>
                    <p>သို့</p>
                </div>
            </div>
        )}

        <div className='container pt-4'>
            <form method="post" onSubmit={transfer}>
                <label htmlFor="amount" className="form-label"><small>လွှဲပြောင်းမည့် ပမာဏ (ကျပ်)</small> <Link to={'/transactions'}><small className='text-primary text-decoration-underline'>မှတ်တမ်းကြည့်ရန် နှိပ်ပါ။</small></Link></label>
                <input type="number" id='amount' className='form-control bg-transparent text-white' onChange={(e) => setAmount(e.target.value)} value={amount} />
                {error && <span>*{error}</span>}
                <div className="d-flex justify-content-end">
                    <button className='btn btn-sm btn-outline-light mt-3' type="submit">
                        {loader && <SmallSpinner />}
                        လွှဲပြောင်းမည်
                    </button>
                </div>
            </form>
        </div>
    </div>
  )
}
