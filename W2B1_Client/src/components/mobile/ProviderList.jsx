import React, { useContext, useEffect } from 'react'
import { LanguageContext } from '../../contexts/LanguageContext';
import { Spinner } from 'react-bootstrap';
import { IoGameController } from 'react-icons/io5'
import { useNavigate } from 'react-router-dom';

export default function ProviderList({loading, providers, type}) {
    const { content } = useContext(LanguageContext);
    const navigate = useNavigate();
    // console.log(providers);
    

  return (
    <div className='row'>
            {loading ? <Spinner /> : providers && providers.length === 0 ? <p className='text-center'>{content?.no_data}</p> :
                providers && providers.map((item, index) => {
                    return <div key={index} className='cursor-pointer col-4 px-1 mb-4 text-center">'>
                        <div className='gameCardLg'>
                            <img src={item.img}
                                style={{ width: "100%" }}
                                className='img-fluid rounded-top-3' />
                            <div className="rounded-bottom-3 fw-semibold px-2 activeGameList text-center">
                                <h6
                                    className='pt-1 fw-semibold mb-0'
                                    width="100%"
                                    style={{ whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}
                                >{item.provider_name}</h6>
                                <div className="d-flex align-items-center gap-2">
                                    {/* <IoGameController size={35} /> */}
                                    <p className="fw-semibold text-white">{item.short_name}</p>
                                </div>
                            </div>
                            <div
                                className="gameCardLgBtn rounded-5 d-flex align-items-center justify-content-center shadow-lg"
                                onClick={() => navigate(`/games?type=${type}&provider=${item.code}`)}
                            >
                                <p className="fw-semibold">{content?.btn?.go_to_list}</p>
                            </div>
                        </div>
                    </div>
                })}
        </div>
  )
}
