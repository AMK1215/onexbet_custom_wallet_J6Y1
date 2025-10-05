import React from 'react'

const Transfer = () => {
    const data=[
        {name:'Wallet',value:'0.00'},
        {name:'Promotion',value:'0.00'},
        {name:'JDB',value:'0.00'},
        {name:'AG',value:'0.00'},
        {name:'UG',value:'0.00'},
        {name:'568 WIN',value:'0.00'},
        {name:'AWC',value:'0.00'},
        {name:'SC',value:'0.00'},
        {name:'JILI',value:'0.00'},
        {name:'FC',value:'0.00'},
        {name:'PG',value:'0.00'},
        {name:'YB',value:'0.00'},
     ]
  return (
    <div>
      <div className="profileForm px-3 py-4 rounded-4 mx-auto">
        <div className="row gap-1   ">
            {data.map((item,index)=>{
                return <div key={index} className=" cursor-pointer mb-2 mx-auto text-center col-5 border border-white rounded-3 p-2">
                    <p className="fw-semibold" style={{textWrap:'nowrap'}}>{item.name}</p>
                    <p className="fw-semibold">{item.value}</p>
                </div>
            })}
        </div>
      </div>
    </div>
  )
}

export default Transfer
