import React, { useContext, useState } from 'react'
import useFetch from '../../hooks/useFetch'
import BASE_URL from '../../hooks/baseUrl'
import Games from './Games';
import { Spinner } from 'react-bootstrap';
import { GameContext } from '../../contexts/GameContext';

const AllTab = () => {
  const {data: types, loading} = useFetch(BASE_URL + "/game_types");
  const [launchingGameId, setLaunchingGameId] = useState(null);
  const [launchError, setLaunchError] = useState("");

  // Seamless launch game function
  const handleLaunchGame = async (game) => {
    setLaunchingGameId(game.id);
    setLaunchError("");
  
    try {
      const res = await fetch(`${BASE_URL}/seamless/launch-game`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          Authorization: "Bearer " + localStorage.getItem("token"),
        },
        body: JSON.stringify({
          game_code: game.game_code,
          product_code: game.product_code,
          game_type: game.game_type,
        }),
      });
  
      const result = await res.json();
  
      if (result.code === 200) {
        if (result.url) {
          // ✅ Redirect works in Telegram browser
          window.location.href = result.url;
        } else if (result.content) {
          // Fallback only for desktop browsers (Telegram will break here)
          const gameWindow = window.open();
          if (gameWindow) {
            gameWindow.document.write(result.content);
            gameWindow.document.close();
          } else {
            setLaunchError("Popup blocked. Please allow popups.");
          }
        } else {
          setLaunchError(result.message || "Launch failed: No URL or content.");
        }
      } else {
        setLaunchError(result.message || "Failed to launch game.");
      }
    } catch (e) {
      setLaunchError("Network error. Please try again.");
    } finally {
      setLaunchingGameId(null);
    }
  };

  console.log('MobileTypes',types);
  return (
    <div className='p-3'>
      <h5>All Games</h5>
      {launchError && (
        <div className="alert alert-danger" role="alert">
          {launchError}
        </div>
      )}
      {
        loading && <Spinner />
      }
      {types && types.map((type, index) => (
        <div key={index}>
          <h2 className='mb-3'>{type.name}</h2>
          <Games games={type.providers} type={type.name} onLaunchGame={handleLaunchGame} />
        </div>
      ))}
    </div>
  )
}

export default AllTab
