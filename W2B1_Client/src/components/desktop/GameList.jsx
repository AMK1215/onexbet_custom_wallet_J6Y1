import React, { useContext, useEffect, useState,useFetch } from "react";
import { LanguageContext } from "../../contexts/LanguageContext";
import { Spinner } from "react-bootstrap";
import { GameContext } from "../../contexts/GameContext";
import BASE_URL from "../../hooks/baseUrl";
import styles from "./GameList.module.css";
import RelatedProviderLists from "./RelatedProviderLists.jsx";




export function GameList({loading, games}) {
  const {content} = useContext(LanguageContext);
  const {current_type, current_provider} = useContext(GameContext);

  const [launchingGameId, setLaunchingGameId] = useState(null);
  const [launchError, setLaunchError] = useState("");

  const [page, setPage] = useState(1);
  const [allGames, setAllGames] = useState(games || []);
  const [hasMore, setHasMore] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);
  const [searchTerm, setSearchTerm] = useState("");

  // Effect to handle external 'games' prop
  useEffect(() => {
    if (games) {
      setAllGames(games);
      setHasMore(false); // Disable 'Load More' for manually passed games
    } else {
      // If 'games' prop is not provided, fetch initial data
      setPage(1);
      setAllGames([]);
      setHasMore(true);
      if (current_type && current_provider) {
        fetchInitialGames(1);
      }
    }
  }, [games, current_type, current_provider]);

  const fetchInitialGames = (pageNum) => {
    setLoadingMore(true);
    fetch(`${BASE_URL}/game_lists/${current_type}/${current_provider}?page=${pageNum}`, {
      headers: {
        Authorization: "Bearer " + localStorage.getItem("token"),
        Accept: "application/json",
      },
    })
        .then((res) => res.json())
        .then((data) => {
          setAllGames(data.data || []);
          setHasMore((data.data || []).length === 20);
          setLoadingMore(false);
        });
  };

  // Load more games (only if not using 'games' prop)
  const handleLoadMore = () => {
    if (games) return; // Don't load more if games are passed directly

    const nextPage = page + 1;
    setLoadingMore(true);

    fetch(`${BASE_URL}/game_lists/${current_type}/${current_provider}?page=${nextPage}`, {
      headers: {
        Authorization: "Bearer " + localStorage.getItem("token"),
        Accept: "application/json",
      },
    })
        .then((res) => res.json())
        .then((data) => {
          const newGames = data.data || [];
          setAllGames((prev) => {
            const existingIds = new Set(prev.map((g) => g.id));
            const uniqueNewGames = newGames.filter((g) => !existingIds.has(g.id));
            return [...prev, ...uniqueNewGames];
          });
          setPage(nextPage);
          setHasMore(newGames.length === 20);
          setLoadingMore(false);
        });
  };

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
          window.location.href = result.url;
        } else if (result.content) {
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

  let customGameTypes;

  switch (current_type) {
    case '1':
      customGameTypes = "SLOT";
      break;
    case '2':
      customGameTypes = "LIVE_CASINO";
      break;
    case '3':
      customGameTypes = "SPORT_BOOK";
      break;
    case '4':
      customGameTypes = "VIRTUAL_SPORT";
      break;
    case '5':
      customGameTypes = "LOTTERY";
      break;
    case '8':
      customGameTypes = "FISHING";
      break;
    case '12':
      customGameTypes = "POKER";
      break;
    case '13':
      customGameTypes = "OTHERS";
      break;
    default:
      customGameTypes = null;
      break;
  }
  
  const displayGames = searchTerm
    ? allGames.filter((game) =>
        game.game_name.toLowerCase().includes(searchTerm.toLowerCase())
      )
    : allGames;

  if (loading && displayGames.length === 0) return (
    <div className="flex justify-center items-center py-8 w-full">
      <div className="w-8 h-8 border-4 border-yellow-400 border-t-transparent rounded-full animate-spin" />
    </div>
  );
  if (!displayGames || displayGames.length === 0)
    return (<>
      <div className="scroll-container ">
        <RelatedProviderLists customGameType={customGameTypes} />
      </div>
      <div className="flex justify-end mb-3">
        <input
            className="px-3 py-2 rounded-lg bg-[#23243a] text-white placeholder-gray-400 border border-yellow-400/60 focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400 w-full max-w-xs transition"
            placeholder="Search..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
        />
      </div>
      <p className="text-center text-gray-400 py-6">{content?.no_data || "No games found."}</p>

      <style >{`
    .scroll-container {
      display: flex;
      overflow-x: auto;
    
      gap: 0.5rem;
      //padding-bottom: 0rem;
      scroll-snap-type: x mandatory;
      padding-bottom: 2px;
      scrollbar-width: thin;
      scrollbar-color: #FFD700 #23272f;
    }
    //.scroll-container::-webkit-scrollbar {
    //  display: none; /* Chrome, Safari */
    //}

    .custom-margin {
      margin-top: 5px;
    }

    @media (max-width: 986px) { 
        .custom-margin {
          margin-top: -15px;
        }
    }
      
    
  `}</style>
      </>);



  // console.log('customGameTypes', customGameTypes);
  return (
      <>


        {launchError && (
        <div className="mb-3">
          <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded relative text-center">
                {launchError}
              </div>
            </div>
        )}
        <div className="scroll-container ">
          <RelatedProviderLists customGameType={customGameTypes} />
        </div>

        <style >{`
    .scroll-container {
      display: flex;
      overflow-x: auto;
    
      gap: 0.5rem;
      padding-bottom: 2px;
      scrollbar-width: thin;
      scrollbar-color: #FFD700 #23272f;
    }
    .scroll-container::-webkit-scrollbar {
      //display: none; /* Chrome, Safari */
    }

    .custom-margin {
      margin-top: 5px;
    }

    @media (max-width: 986px) { 
        .custom-margin {
          margin-top: -13px;
        }
    }
      
    
  `}</style>


        <div className="flex justify-end mb-3 custom-margin" >
          <input
              className="px-3 py-2 rounded-lg bg-[#23243a] text-white placeholder-gray-400 border border-yellow-400/60 focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400 w-full max-w-xs transition"
              placeholder="Search..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
      <div className="grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 w-full">
        {displayGames.map((item) => (
            <div
                key={item.id}
                className="cursor-pointer group flex flex-col items-center"
                onClick={() => handleLaunchGame(item)}
          >
            <div className="relative w-full aspect-square overflow-hidden rounded-2xl bg-gradient-to-br from-yellow-400/70 via-white/10 to-yellow-600/70 p-1 group-hover:from-yellow-400 group-hover:to-orange-400 group-hover:shadow-2xl transition-all duration-200">
              <div className="w-full h-full bg-black/60 rounded-2xl flex items-center justify-center group-hover:bg-black/80 transition-all duration-200">
                <img
                  src={item.image_url}
                  alt={item.game_name}
                  className="w-full h-full object-cover rounded-2xl group-hover:scale-105 transition-transform duration-200"
                />
                <button
                  className="absolute bottom-2 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-gradient-to-r from-orange-400 to-yellow-500 text-white font-bold text-xs shadow hover:scale-105 transition whitespace-nowrap border border-yellow-300"
                  onClick={e => { e.stopPropagation(); handleLaunchGame(item); }}
                  disabled={launchingGameId === item.id}
                >
                  {launchingGameId === item.id ? (
                    <span className="inline-block w-4 h-4 border-2 border-yellow-400 border-t-transparent rounded-full animate-spin align-middle" />
                  ) : (
                    content?.btn?.play_game || "Play"
                  )}
                </button>
              </div>
            </div>
            <div className="mt-2 flex justify-center w-full">
              <span className="px-4 py-1 bg-black/80 text-yellow-300 font-bold text-xs rounded-full shadow text-center max-w-[90%] truncate whitespace-nowrap border border-yellow-300 group-hover:scale-105 transition-transform duration-200">
                {item.game_name}
              </span>
            </div>
            </div>
        ))}
      </div>

        {hasMore && !games && (
            <div className="text-center my-3">
              <button
            className="px-6 py-2 rounded-full bg-gradient-to-r from-yellow-400 to-orange-400 text-black font-bold text-sm shadow hover:scale-105 transition"
            style={{ maxWidth: 200 }}
                  onClick={handleLoadMore}
                  disabled={loadingMore}
              >
            {loadingMore ? (
              <span className="inline-block w-4 h-4 border-2 border-yellow-400 border-t-transparent rounded-full animate-spin align-middle" />
            ) : "Load More"}
              </button>
            </div>
        )}
      </>
  );
}
