import React, { useContext, useEffect, useState } from "react";
import Footer from "../components/mobile/Footer";
import Carousel from "../components/mobile/Carousel";
import { Link, useNavigate } from "react-router-dom";
import { FaGift } from "react-icons/fa";
import fb from "../assets/img/fb.png";
import Marquee from "../components/mobile/Marquee";
import { IoWalletOutline } from "react-icons/io5";
import GameTabsLg from "../components/desktop/GameTabsLg";
import LanguageDropdown from "../components/LanguageDropdown";
import tele from '../assets/img/telew.svg'
import viber from '../assets/img/viberw.svg'
import AdsBanner from "../components/AdsBanner";
import { LanguageContext } from "../contexts/LanguageContext";
import { GeneralContext } from "../contexts/GeneralContext";
import AllTab from "../components/mobile/AllTab";
import BottomMenu from "../components/mobile/BottomMenu";
//import "../tailwind_css.css";

const HomePage = () => {
  const { banners, ads_banner, banner_text, contacts } = useContext(GeneralContext);
  const { content } = useContext(LanguageContext);
  const navigate = useNavigate();
  const [isSiteBgPlay, setIsSiteBgPlay] = useState(false);

  contacts?.map((contact) => ({
    ...contact,
    image: contact.name == "Viber"
      ? viber
      : contact.name == "Telegram"
        ? tele
        : contact.name == "Facebook"
          ? fb
          : null,
  }));

  useEffect(() => {
    navigate('/?type=all');
  }, []);

  return (
    <div className="w-full min-h-screen bg-[#101223] flex flex-col">
      <AdsBanner ads_banner={ads_banner} />
      <Carousel />
      
      <div className="w-full max-w-6xl mx-auto flex flex-col gap-2 px-2 sm:px-4 pt-1">
        <div className="flex items-center w-full bg-transparent mb-1">
        <Marquee />
      </div>
        {/* Desktop Games Tabs */}
        <div className="mt-0">
          <GameTabsLg />
        </div>
      </div>

      <div className="mt-auto w-full pt-8">
        <Footer />
      </div>
    </div>
  );
};

export default HomePage;
