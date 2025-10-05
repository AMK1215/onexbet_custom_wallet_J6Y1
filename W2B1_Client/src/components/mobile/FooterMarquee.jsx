import React from 'react'
import Marquee from "react-fast-marquee";
import g1 from '../../assets/img/pd12.png'
import g2 from '../../assets/img/pd2.png'
import g3 from '../../assets/img/pd3.png'
import g4 from '../../assets/img/pd4.png'
import g5 from '../../assets/img/pd5.png'
import g6 from '../../assets/img/pd6.png'
import g7 from '../../assets/img/pd11.png'
import g8 from '../../assets/img/pd8.png'
import g9 from '../../assets/img/pd9.png'
import g10 from '../../assets/img/pd10.png'

const FooterMarquee = () => {
    const imgs=[g1,g2,g3,g4,g5,g6,g7,g8,g9,g10]
  return (
    <div className='my-5 py-5'>
      <Marquee>
            {imgs.map((item,index)=>{
                return <img src={item} key={index} className='me-4 footerMarqueeImg' />
            })}
      </Marquee>
    </div>
  )
}

export default FooterMarquee
