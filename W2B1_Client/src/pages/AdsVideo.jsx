import React, { useContext } from 'react'
import Marquee from '../components/mobile/Marquee'
import { LanguageContext } from '../contexts/LanguageContext'
import useFetch from '../hooks/useFetch'
import BASE_URL from '../hooks/baseUrl'


const AdsVideo = () => {
  const { content } = useContext(LanguageContext);
  const { data: videos, loading } = useFetch(BASE_URL + '/videoads');
  const API_URL = BASE_URL.replace("/api", ""); // Creates the base URL for assets, e.g., https://luckymillion.pro

  return (<>
    <div className="d-flex align-items-center bg-black">
      <Marquee />
      
    </div>
    <div className='container py-5'>
      <div className='text-center mb-5'>
        <h1 className="fw-bold text-warning d-inline-block pb-2 px-4" style={{ borderBottom: '3px solid #FFD700', letterSpacing: '1px' }}>
          {content?.nav?.ads_video || "Ads Video"}
        </h1>
      </div>
      <div className="row d-flex justify-content-center">
        {loading ? (
          <div className="text-center">
            <div className="spinner-border text-warning" role="status">
              <span className="visually-hidden">Loading...</span>
            </div>
          </div>
        ) : videos && videos.length > 0 ? (
          videos.map((item, index) => (
            <div className='col-lg-8 col-md-10 mb-5' key={item.id}>
              <div className='card bg-dark text-white' style={{ borderRadius: '20px', overflow: 'hidden', border: '1px solid #444', boxShadow: '0 10px 30px rgba(0,0,0,0.5)' }}>
                <video 
                  width="100%" 
                  controls 
                  autoPlay 
                  muted 
                  loop 
                  playsInline
                >
                  <source src={API_URL + item.video_url} type="video/mp4" />
                  Your browser does not support the video tag.
                </video>
                {/*<div className="text-white">ww</div>*/}
                <div className='card-body text-center'>
                  <h5 className='card-title text-warning'>{`Promotion Video ${index + 1}`}</h5>
                  <p className='card-text text-white-50'>Stay updated with our latest events and offers.</p>
                </div>
              </div>
            </div>
          ))
        ) : (
          <div className='text-center'>
            <p className='text-white-50 fs-5'>{content?.no_data || "No videos available at the moment."}</p>
          </div>
        )}
      </div>
    </div>
  </>
  )
}

export default AdsVideo;
