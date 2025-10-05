import React from 'react'
import UserWallet from '../components/UserWallet'
import AccountInfo from '../components/AccountInfo'
import moneyBag from '../assets/img/winner.png'
import list from '../assets/img/list.png'
import holiday from '../assets/img/holiday.png'
import { Link } from 'react-router-dom'
import useFetch from '../hooks/useFetch'
import BASE_URL from '../hooks/baseUrl'

const WalletPage = () => {
    const wallets=[
        {id:1,img:moneyBag,title:'ငွေဖြည့်',link:'/top-up/bank'},
        {id:2,img:list,title:'ငွေထုတ်',link:'/with-draw'},
        {id:3,img:holiday,title:'မှတ်တမ်း',link:'/wallet-history'},
    ];

    const {data:user} = useFetch(BASE_URL + "/user");

  return (
    <div className='pb-5'>
        <AccountInfo user={user}/>
      <div className="p-2">
      <UserWallet user={user}/>
      <div className="mt-3 p-2 bg-white d-flex align-items-center justify-content-between rounded-4">
        {wallets.map((item)=>{
            return <div key={item.id} className='text-center'>
                <Link to={item.link}>
                <img src={item.img} />
                <small style={{fontSize:'13px'}} className="d-block fw-bold  text-black">{item.title}</small>
                </Link>
            </div>
        })}
      </div>
      <div className='my-4 p-3 rounded-4  text-white border' style={{background: '#2B3576'}}>
      <h6 className="text-center fw-bold">ငွေဖြည့်လိုပါက</h6>
      <small className='d-block mt-4 mb-2'>၁။ "ငွေဖြည့်" ကို နှိပ်ပါ။</small>
      <small className='my-2 d-block'>၂။ ငွေဖြည့် ပမာဏ ကို ရိုက်ထည့်ပါ။</small>
      <small className='my-2 d-block'>၃။ သက်ဆိုင်ရာ Pay ဖြင့် ငွေသွင်းနိုင်သော အကောင့်များ ပေါ်လာပါ လိမ့်မည်</small>
    </div>
      </div>
    </div>
  )
}

export default WalletPage
