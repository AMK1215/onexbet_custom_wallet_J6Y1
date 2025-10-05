import React, { useState } from 'react'
import useFetch from '../../hooks/useFetch'
import BASE_URL from '../../hooks/baseUrl'

export default function Transaction() {
    const [url, setUrl] = useState(BASE_URL + "/exchange-transactions-log");
    const {data: transferLogs} = useFetch(url);
    let logs = transferLogs?.data;
    let pages = transferLogs?.links;
    // console.log(transferLogs);

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
        }).format(date);
    };

    // return;

  return (
    <div className='container pb-5 mb-5'>
        <small className="text-center d-block py-3">ဂိမ်းပိုက်ဆံ မှတ်တမ်းများ</small>
        {logs && logs.map((log, index) => (
        <div className='mb-4 d-flex justify-content-between align-items-center' key={index}>
            <div>
                <small className='d-block mb-2'>လွှဲပြောင်းခြင်း</small>
                <small className={`badge text-bg-${log.type === "mainBalanceToGaming" ? "info" : "success"}`}>
                {log.type === "mainBalanceToGaming" ? 
                    <>ပိုက်ဆံအိတ် <i className='fas fa-arrow-right'></i> ဂိမ်း</> : 
                    <>ဂိမ်း <i className='fas fa-arrow-right'></i> ပိုက်ဆံအိတ်</>}
                </small>

            </div>
            <div>
                <small className='d-block mb-2'>ပမာဏ</small>
                <small>{Number(log.amount).toLocaleString()} ကျပ်</small>
            </div>
            <div>
                <small className='d-block mb-2'>အချိန်</small>
                <small>{formatDate(log.created_at)}</small>
            </div>
        </div>
        ))}
        <div className="d-flex justify-content-center">
        {pages && pages.map((page, index) => (
            <button
                className={`btn btn-sm btn-${page.active === true ? "light" : "outline-light"} mx-1 `} {...(page.active === true ? {disabled: true} : {})}
                key={index}
                onClick={() => setUrl(page.url)}
            >
                {index === 0 ? "<" : index === pages.length - 1 ? ">" : page.label}
            </button>
        ))}
        </div>
    </div>
  )
}
