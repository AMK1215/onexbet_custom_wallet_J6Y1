import React, { useContext, useEffect, useState } from "react";
import { LanguageContext } from "../../contexts/LanguageContext";
import { Spinner } from "react-bootstrap";
import { GameContext } from "../../contexts/GameContext";
import BASE_URL from "../../hooks/baseUrl";
import styles from "./GameList.module.css";

export default function GameList({ loading, games }) {
  const { content } = useContext(LanguageContext);
  const { current_type, current_provider } = useContext(GameContext);
  const [launchingGameId, setLaunchingGameId] = useState(null);
  const [launchError, setLaunchError] = useState("");

  // Pagination states
  const [page, setPage] = useState(1);
  const [allGames, setAllGames] = useState([]);
  const [hasMore, setHasMore] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);
  const [searchTerm, setSearchTerm] = useState("");

  // Reset when type/provider changes
  useEffect(() => {
    setPage(1);
    setAllGames([]);
    setHasMore(true);
  }, [current_type, current_provider]);

  // Initial load or when type/provider changes
  useEffect(() => {
    if (!current_type || !current_provider) return;
    setLoadingMore(true);
    fetch(`${BASE_URL}/game_lists/${current_type}/${current_provider}?page=1`, {
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
  }, [current_type, current_provider]);

  // Load more handler
  const handleLoadMore = () => {
    const nextPage = page + 1;
    setLoadingMore(true);
    fetch(
      `${BASE_URL}/game_lists/${current_type}/${current_provider}?page=${nextPage}`,
      {
        headers: {
          Authorization: "Bearer " + localStorage.getItem("token"),
          Accept: "application/json",
        },
      }
    )
      .then((res) => res.json())
      .then((data) => {
        setAllGames((prev) => [...prev, ...(data.data || [])]);
        setPage(nextPage);
        setHasMore((data.data || []).length === 20);
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
      if (result.code === 200 && result.url) {
        window.open(result.url, "_blank", "noopener");
      } else {
        setLaunchError(result.message || "Failed to launch game.");
      }
    } catch (e) {
      setLaunchError("Network error. Please try again.");
    } finally {
      setLaunchingGameId(null);
    }
  };

  const filteredGames = allGames.filter((game) =>
    game.game_name.toLowerCase().includes(searchTerm.toLowerCase())
  );

  if ((loading || loadingMore) && allGames.length === 0) return <Spinner />;
  if (!allGames || allGames.length === 0)
    return <p className="text-center">{content?.no_data}</p>;

  return (
    <>
      {/* <div className="row"> */}
      {launchError && (
        <div className="col-12 mb-3">
          <div className="alert alert-danger" role="alert">
            {launchError}
          </div>
        </div>
      )}

      <div className="text-end">
        <input
          className="card-subnav-search mb-2 mt-2 border-0"
          placeholder="Search..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
        />
      </div>
      {filteredGames.map((item) => (
        <div
          key={item.id}
          className="col-3 col-sm-3 col-md-3 col-lg-2 cursor-pointer mb-2 px-1 "
        >
          <div className="gold-card rounded-4">
            <div
              style={{
                width: "100%",
                aspectRatio: "1 / 1",
                overflow: "hidden",
                borderTopLeftRadius: "1rem",
                borderTopRightRadius: "1rem",
              }}
            >
              <img
                src={item.image_url}
                style={{
                  width: "100%",
                  height: "100%",
                  objectFit: "cover",
                }}
                onClick={() => handleLaunchGame(item)}
                alt={item.game_name}
              />
            </div>
            <div
              className="px-3  mt-1 text-center"
              style={{
                backgroundColor: "rgba(0, 0, 0, 0.7)",
                color: "#fff",
                borderRadius: "200px",
              }}
            >
              <h6
                className="mb-1"
                style={{
                  whiteSpace: "nowrap",
                  overflow: "hidden",
                  textOverflow: "ellipsis",
                  fontSize: "13px",
                }}
                title={item.game_name}
              >
                {item.game_name}
              </h6>
            </div>
            <button
              className={styles["play-btn"]}
              onClick={() => handleLaunchGame(item)}
              disabled={launchingGameId === item.id}
            >
              {launchingGameId === item.id ? (
                <Spinner animation="border" size="sm" />
              ) : (
                content?.btn?.play_game
              )}
            </button>
          </div>
        </div>
      ))}
      {/* </div> */}
      {hasMore && (
        <div className="text-center my-3">
          <button
            className={styles["play-btn"]}
            style={{ maxWidth: 200 }}
            onClick={handleLoadMore}
            disabled={loadingMore}
          >
            {loadingMore ? (
              <Spinner animation="border" size="sm" />
            ) : (
              "Load More"
            )}
          </button>
        </div>
      )}
    </>
  );
}
