import React, { useContext } from 'react'
import GameHeading from '../components/mobile/GameHeading'
import { GameContext } from '../contexts/GameContext'

export default function Games() {
    const {types} = useContext(GameContext);
  return (
    <>
    <GameHeading />
    <div className='p-3'>
        {/* <h5>All Games</h5> */}
        {types && types.map((type, index) => (
            <div key={index}>
            <h5>{type.name}</h5>
            
            </div>
        ))}
    </div>
    </>
  )
}
