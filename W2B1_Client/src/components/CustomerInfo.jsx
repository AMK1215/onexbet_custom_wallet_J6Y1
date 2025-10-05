import React from 'react'
import tele from '../assets/img/tele.png'
import viber from '../assets/img/viber.png'
import { Link } from 'react-router-dom'

const CustomerInfo = () => {
    
    const data=[
        {img:tele,link:'/'},
        {img:viber,link:'/'},
    ]
  return (
    <div className='rounded-4 py-2 px-3 border ' style={{background:'#1D2765'}}>
      <small className="mb-3 fw-bold text-center">ငွေဖြည့်ရန်အဆင်မပြေမှုတစ်စုံတစ်ရာရှိပါက ဆက်သွယ်ရန်</small>
      <div className="d-flex mt-3 mb-4 align-items-center justify-content-center gap-4">
        {data?.map((item, index)=>{
            return <div key={index}>
                <Link to={item.link}>
                <img src={item.img} style={{width:'30px',height:'30px'}} />
                </Link>
            </div>
        })}
      </div>
    </div>
  )
}

export default CustomerInfo
