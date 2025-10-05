import React, { useState } from 'react'
import { Col, Nav, Row, Tab, Table } from 'react-bootstrap'
import useFetch from '../../hooks/useFetch'
import BASE_URL from '../../hooks/baseUrl'

const WalletHistoryPage = () => {
  const [url1, setUrl1] = useState(BASE_URL + "/transaction/deposit-requestlog");
  const [url2, setUrl2] = useState(BASE_URL + "/transaction/withdraw-requestlog");
  const {data: cashInRequests} = useFetch(url1);
  const {data: cashOutRequests} = useFetch(url2);
  // console.log(cashInRequests);
  
  const log1s = cashInRequests?.data;
  const log2s = cashOutRequests?.data;
  let page1s = cashInRequests?.links;
  let page2s = cashOutRequests?.links;

  const {data: channels} = useFetch(BASE_URL + "/agent-payment-type");
  const {data: user_payment} = useFetch(BASE_URL + "/user-payment");

  const banking = (id) => {
    const channel = channels.find((channel) => channel.id === id);
    return channel ? channel.payment_type.name : null;
  };
  const AccNo = (id) => {
    const channel = channels.find((channel) => channel.id === id);
    return channel ? channel.account_no : null;
  };
  const AccName = (id) => {
    const channel = channels.find((channel) => channel.id === id);
    return channel ? channel.account_name : null;
  };

  const formatDate = (dateString) => {
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      }).format(date);
  };

  return (
    <div className='py-3 px-2 px-sm-3'>
       <Tab.Container id="left-tabs-example" defaultActiveKey="1">
      <Row>
        <Col sm={12}>
          <Nav variant="pills" className="flex-row gap-3 mb-4">
            <Nav.Item  className=' text-white rounded-4'>
              <Nav.Link className='text-white rounded-4' eventKey="1">ငွေသွင်း</Nav.Link>
            </Nav.Item>
            <Nav.Item  className=' text-white rounded-4'>
              <Nav.Link className='text-white rounded-4' eventKey="2">ငွေထုတ်</Nav.Link>
            </Nav.Item>
          </Nav>
        </Col>
        <Col sm={12}>
          <Tab.Content>
            <Tab.Pane eventKey="1">
              <div className="table-responsive mb-5 pb-5">
                  {log1s?.map((log, index) => (
                  <div className='rounded-3 border border-1 shadow p-2 mb-2' key={index}>
                    <div className="d-flex justify-content-between">
                      <div>
                        <div className={`badge text-bg-${log.status === 0 ? "warning" : (log.status === 1 ? "success" : "danger") }`}>
                          {log.status === 0 && "စောင့်ဆိုင်း"}
                          {log.status === 1 && "လွှဲပြီး"}
                          {log.status === 2 && "ငြင်းပယ်"}
                        </div>
                      </div>
                      <div className="d-flex mb-1">
                        <div className='me-3'>
                          နေ့ရက်:
                        </div>
                        <div>
                          {formatDate(log.created_at)}
                        </div>
                      </div>
                    </div>
                    <div className="d-flex mb-1">
                        <div className='me-3'>
                          ဘဏ်: 
                        </div>
                        <div>
                          {banking(log.user_payment_id)}
                        </div>
                    </div>
                    <div className="d-flex mb-1">
                      <div className='me-3'>
                        အကောင့်ပိုင်ရှင်: 
                      </div>
                      <div>
                      {AccName(log.user_payment_id)}
                      </div>
                    </div>
                    <div className="d-flex mb-1">
                      <div className='me-3'>
                        အကောင့် / ဖုန်း: 
                      </div>
                      <div>
                      {AccNo(log.user_payment_id)}
                      </div>
                    </div>
                    <div className="d-flex mb-1">
                      <div className='me-3'>
                        ပမာဏ :
                      </div>
                      <div>
                        {Number(log.amount).toLocaleString()} MMK
                      </div>
                    </div>
                    <div className="d-flex mb-1">
                      <div className='me-3'>
                        ငွေလွှဲကုဒ် :
                      </div>
                      <div>
                        {log.refrence_no}
                      </div>
                    </div>
                  </div>
                  ))}
                <div className="d-flex justify-content-center">
                  {page1s && page1s.map((page, index) => (
                      <button
                          className={`btn btn-sm btn-${page.active === true ? "light" : "outline-light"} mx-1 `} {...(page.active === true ? {disabled: true} : {})}
                          key={index}
                          onClick={() => setUrl1(page.url)}
                      >
                          {index === 0 ? "<" : index === page1s.length - 1 ? ">" : page.label}
                      </button>
                  ))}
                </div>
              </div>
            </Tab.Pane>
            <Tab.Pane eventKey="2">
              <div className="table-responsive mb-5 pb-5">
                    {log2s?.map((log, index) => (
                    <div className='rounded-3 border border-1 shadow p-2 mb-2' key={index}>
                      <div className="d-flex justify-content-between">
                        <div>
                          <div className={`badge text-bg-${log.status === 0 ? "warning" : (log.status === 1 ? "success" : "danger") }`}>
                            {log.status === 0 && "စောင့်ဆိုင်း"}
                            {log.status === 1 && "လွှဲပြီး"}
                            {log.status === 2 && "ငြင်းပယ်"}
                          </div>
                        </div>
                        <div className="d-flex mb-1">
                          <div className='me-3'>
                            နေ့ရက်:
                          </div>
                          <div>
                            {formatDate(log.created_at)}
                          </div>
                        </div>
                      </div>
                      <div className="d-flex mb-1">
                        <div className='me-3'>
                          ဘဏ်: 
                        </div>
                        <div>
                          {user_payment?.payment_type?.name}
                        </div>
                      </div>
                      <div className="d-flex mb-1">
                        <div className='me-3'>
                          အကောင့်ပိုင်ရှင်: 
                        </div>
                        <div>
                        {user_payment?.account_name}
                        </div>
                      </div>
                      <div className="d-flex mb-1">
                        <div className='me-3'>
                          အကောင့် / ဖုန်း: 
                        </div>
                        <div>
                        {user_payment?.account_no}
                        </div>
                      </div>
                      <div className="d-flex mb-1">
                        <div className='me-3'>
                          ပမာဏ :
                        </div>
                        <div>
                          {Number(log.amount).toLocaleString()} MMK
                        </div>
                      </div>
                    </div>
                    ))}
                <div className="d-flex justify-content-center">
                  {page2s && page2s.map((page, index) => (
                      <button
                          className={`btn btn-sm btn-${page.active === true ? "light" : "outline-light"} mx-1 `} {...(page.active === true ? {disabled: true} : {})}
                          key={index}
                          onClick={() => setUrl2(page.url)}
                      >
                          {index === 0 ? "<" : index === page2s.length - 1 ? ">" : page.label}
                      </button>
                  ))}
                </div>
              </div>
            </Tab.Pane>
          </Tab.Content>
        </Col>
      </Row>
    </Tab.Container>
    </div>
  )
}

export default WalletHistoryPage
