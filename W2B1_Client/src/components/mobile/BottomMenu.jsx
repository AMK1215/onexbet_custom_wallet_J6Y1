import React, { useContext } from "react";
import { Link, useLocation, useSearchParams } from "react-router-dom";
import { LanguageContext } from "../../contexts/LanguageContext";
import { GameContext } from "../../contexts/GameContext";
import { AuthContext } from "../../contexts/AuthContext";

const BottomMenu = () => {
  const { user } = useContext(AuthContext);
  const location = useLocation();
  const [searchParams] = useSearchParams();
  const { content } = useContext(LanguageContext);

  const isActive = (path, typeId = null, tabParam = null) => {
    const pathMatch = location.pathname === path;
    const typeMatch = typeId ? searchParams.get("type") === typeId : true;
    const tabMatch = tabParam ? searchParams.get("tab") === tabParam : true;
    return pathMatch && typeMatch && tabMatch;
  };

  const baseImg = [
    "/images/Final_All/Home/Home Icon.png",
    "/images/Final_All/Message/Message.png",
    "/images/Final_All/Money/Money.png",
    "/images/Final_All/Video ADs/Video Ads.png"
  ];
  const activeImg = [
    "/images/Final_All/Home/APNG Home 2/APNG Home 2.png",
    "/images/Final_All/Message/Message APNG/Message APNG.png",
    "/images/Final_All/Money/Money APNG/Money APNG.png",
    "/images/Final_All/Video ADs/Video ADS PNG/Video ADS PNG.png"
  ];

  return (
    user && (
      <nav className="fixed bottom-0 left-0 w-full z-40 bg-yellow-400 border-t border-yellow-300 shadow-t flex justify-between items-center px-2 sm:px-6 py-1 md:py-2">
        <Link to="/?type=all" className="flex-1 flex flex-col items-center py-1">
          <img
            src={isActive("/", "all") ? activeImg[0] : baseImg[0]}
            className="w-8 h-8 md:w-10 md:h-10 mb-0.5 transition-transform duration-200 hover:scale-110"
            alt="Home"
          />
        </Link>
        <Link to="/ads-video?tab=AdsVideo" className="flex-1 flex flex-col items-center py-1">
          <img
            src={isActive("/ads-video", null, "AdsVideo") ? activeImg[3] : baseImg[3]}
            className="w-8 h-8 md:w-10 md:h-10 mb-0.5 transition-transform duration-200 hover:scale-110"
            alt="Ads"
          />
        </Link>
        <Link to="/Promotion?tab=Promotion" className="flex-1 flex flex-col items-center py-1">
          <img
            src={"/images/Final_All/Promotion/APNG Promotion/APNG Promotion.png"}
            className="w-8 h-8 md:w-10 md:h-10 mb-0.5 transition-transform duration-200 hover:scale-110"
            alt="Promotion"
          />
        </Link>
        <Link to="/information?tab=transfer" className="flex-1 flex flex-col items-center py-1">
          <img
            src={isActive("/information", null) && searchParams.get("tab") === "transfer" ? activeImg[2] : baseImg[2]}
            className="w-8 h-8 md:w-10 md:h-10 mb-0.5 transition-transform duration-200 hover:scale-110"
            alt="Wallet"
          />
        </Link>
        <Link to="/Contact?tab=Contact" className="flex-1 flex flex-col items-center py-1">
          <img
            src={isActive("/Contact", null, "Contact") ? activeImg[1] : baseImg[1]}
            className="w-8 h-8 md:w-10 md:h-10 mb-0.5 transition-transform duration-200 hover:scale-110"
            alt="Contact"
          />
        </Link>
      </nav>
    )
  );
};

export default BottomMenu;
