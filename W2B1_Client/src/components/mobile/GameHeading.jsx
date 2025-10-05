import React, { useEffect, useState } from "react";
import allWhite from "../../assets/img/allWhite.svg";
import allBlack from "../../assets/img/allBlack.svg";
import hotWhite from "../../assets/img/hotWhite.png";
import hotActive from "../../assets/img/hotActive.png";
import promotion from "../../assets/img/promotion2.svg";
import "../../assets/css/games.css";
import { Link, useNavigate, useSearchParams } from "react-router-dom";

const GameHeading = () => {
  const [searchParams]=useSearchParams();
  const navigate=useNavigate();
   const [selectedTab, setSelectedTab] = useState(searchParams.get('tab')||'');
   useEffect(()=>{
    setSelectedTab(searchParams.get('tab')||'')
   },[searchParams])
   return (
    <div
      className="gameHeading row px-0 py-2 cursor-pointer"
      style={{ overflowX: "hidden" }}
    >
      <div
        onClick={() => {
          setSelectedTab("all")
          navigate('/games?tab=all')
        }}
        className={`${
          selectedTab === "all" || searchParams.get('type')==='all'  ? "activeGameHeading" : ""
        } text-center  col-4 py-1 py-sm-2 `}
      >
        <img
          style={{ width: "25px", height: "25px" }}
          src={allWhite}
        />
        <small className="fw-bold d-block mt-sm-1">All</small>
      </div>
      <div
        onClick={() =>{ 
          setSelectedTab("hot")
          navigate('/games?tab=hot')
        }
        }
        className={`${
          selectedTab === "hot" ||  searchParams.get('type')==='hot'  ? "activeGameHeading" : ""
        } text-center  col-4  py-1 py-sm-2 `}
      >
        <img
          className="gameHeadingImg"
          src={selectedTab === "hot" ? hotActive : hotWhite}
        />
        <small className="fw-bold d-block  mt-sm-1">Hot</small>
      </div>
      <div
        className={`${
          selectedTab === "promotion" ? "activeGameHeading" : ""
        } text-center col-4  py-1 py-sm-2  `}
      >
        <Link to={"/promotion"}>
          <img className="gameHeadingImg" src={promotion} />
          <small className="fw-bold d-block  mt-sm-1">Promotions</small>
        </Link>
      </div>
    </div>
  );
};

export default GameHeading;
