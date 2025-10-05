import React from 'react'
import dragon from '../assets/img/dragon.svg'
import trophy from '../assets/img/trophy.png'
import '../assets/css/ranking.css';
import {CircleUserRoundIcon} from 'lucide-react'
import useFetch from '../hooks/useFetch';
import BASE_URL from '../hooks/baseUrl';


const RankingPage = () => {

  const { data: winners } = useFetch(BASE_URL + "/toptenwithdraw");

  return (
    <div className='py-4 px-2 px-sm-4 mb-5'>
      <div className="d-flex align-items-center justify-content-center gap-sm-4">
      <img src={dragon} className='dragon' />
      <div className='text-center'>
            <h4 className="text-warning mt-3 mb-2 rankingText fw-bold">
                Winner of the Month
           </h4>
            <img src={trophy} className='trophy' />
             <h5 className=" fw-bold rankingText">NGAJAYXXX X26</h5>
             <h5 className=" fw-bold rankingText text-white">25,6000</h5>
      </div>
      </div>
      <div className="my-5">
        <button className='activeGameList rounded text-white shadow-sm px-3 py-1'>Today</button>
        <div className='mb-5 mt-3'>
            {winners && winners.map((item,index)=>{
                return <div key={index} className={`${index < 3 ? 'activeGameList' : 'winnerBg'} mb-2 shadow-sm d-flex align-items-center justify-content-between fw-semibold  py-3 px-2 `}>
                    <p>{++index}</p>
                    <p><CircleUserRoundIcon className='me-1' /> {item.player_id}</p>
                    <p>{Number(item.amount).toLocaleString()} MMK</p>
                 </div>
            })}
        </div>
      </div>
       
    </div>
  )
}

export default RankingPage
