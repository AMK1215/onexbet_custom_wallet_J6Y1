import React from 'react'
import { FaUser } from "react-icons/fa";
import '../assets/css/accountInfo.css'
import { FaBell } from "react-icons/fa6";
import mm from '../assets/img/mm.svg';
import useFetch from '../hooks/useFetch';
import BASE_URL from '../hooks/baseUrl';


const AccountInfo = () => {
  const {data:user} = useFetch(BASE_URL + "/user");
  return (
    <div className='cursor-pointer py-2 px-3 text-white d-flex items-center  justify-content-between' style={{background:'#8592E1'}}>
     <div className="d-flex align-items-center gap-2">
     <div className='accountIcon border d-flex align-items-center justify-content-center' style={{background:'#8B93C4'}}>
        <FaUser color='#2A346D' />
      </div>
      <div>
        <h6 className='mb-0'>{user?.name}</h6>
        <small>ID: {user?.user_name}</small>
      </div>
     </div>
     <div className='d-flex align-items-center gap-2'>
        <img src={mm} className='mmFlag' />
        <FaBell color='#FF1267' size={25} />
     </div>
    </div>
  )
}

export default AccountInfo
