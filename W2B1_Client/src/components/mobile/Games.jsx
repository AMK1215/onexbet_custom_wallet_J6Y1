import React from 'react'
import { Link } from 'react-router-dom';

export default function Games({games, type, onLaunchGame}) {

    // console.log(games);
  return (
    <div className='mb-5'>
        <div className="row">
            {games && games.map((game, index) => (
                <div className="col-md-2 col-4 mb-3" key={index}>
                    {onLaunchGame ? (
                        <div 
                            className="cursor-pointer"
                            onClick={() => onLaunchGame(game)}
                        >
                            <img src={game.imgUrl} className="img-fluid w-100 rounded-3 gameImg" alt="game" />
                        </div>
                    ) : (
                        <Link to={`/games?type=${type}&&list=${game.short_name}`}>
                            <img src={game.imgUrl} className="img-fluid w-100 rounded-3 gameImg" alt="game" />
                        </Link>
                    )}
                </div>
            ))}
            
        </div>

    </div>
  )
}
