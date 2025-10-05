import React, { useContext, useState } from 'react'
import { Spinner } from 'react-bootstrap';
import { GameContext } from '../../contexts/GameContext';
import BASE_URL from "../../hooks/baseUrl";

const HotTab = () => {
  const { hot_games, loading } = useContext(GameContext);
  const [launchingGameId, setLaunchingGameId] = useState(null);
  const [launchError, setLaunchError] = useState("");

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

  return (
    <div className='container-fluid mb-5'>
      <h2 className='my-3'>Hot Games</h2>
      {launchError && (
        <div className="alert alert-danger" role="alert">
          {launchError}
        </div>
      )}
      <div className="row mx-1 mb-5 pb-5">
        {loading ? <Spinner /> : 
        hot_games && hot_games.length > 0 ? hot_games.map((item, index) => (
        <div
          key={index}
          className="p-0 cursor-pointer col-4 col-md-2 mb-3"
          onClick={() => handleLaunchGame(item)}
        >
          <img
            src={item.image_url}
            alt={item.game_name}
            className="rounded-3 hotgame"
            style={{ 
              opacity: launchingGameId === item.id ? 0.6 : 1,
              pointerEvents: launchingGameId === item.id ? 'none' : 'auto'
            }}
          />
          {launchingGameId === item.id && (
            <div className="position-absolute top-50 start-50 translate-middle">
              <Spinner size="sm" />
            </div>
          )}
        </div>
        )) : <h4 className='text-center'>There is no game list.</h4>}
      </div>
    </div>
  )
}

export default HotTab
