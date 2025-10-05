import React, { useContext } from 'react'
import logo from '../../assets/img/logo.png';
import { FaViber } from 'react-icons/fa';
import profile from '../../assets/img/profile.svg';
import contact from '../../assets/img/contact.svg';
import titok from '../../assets/img/titokWhite.svg';
import about from '../../assets/img/about.svg';
import home from '../../assets/img/home.svg';
import money from '../../assets/img/money.png';
import log from '../../assets/img/log.png';
import promotion from '../../assets/img/promotion.svg';
import { Link } from 'react-router-dom';
import slot from "../../assets/img/slot.svg";
import card from "../../assets/img/card.svg";
import fish from "../../assets/img/fish.svg";
import football from "../../assets/img/sport.svg";
import depositLg from '../../assets/img/depositLg.png';
import phone from '../../assets/img/social24.svg'
import line from '../../assets/img/lineW.svg'
import tele from '../../assets/img/telew.svg'
import viber from '../../assets/img/viberw.svg'
import fb from "../../assets/img/fb.png";
import coin from '../../assets/img/coin.png'
import { CoinsIcon } from 'lucide-react';
import { AuthContext } from '../../contexts/AuthContext';
import { LanguageContext } from '../../contexts/LanguageContext';
import useLogout from "../../hooks/useLogout";
import { Spinner } from 'react-bootstrap';
import useFetch from '../../hooks/useFetch';
import BASE_URL from '../../hooks/baseUrl';
import ranking from "../../assets/img/trophy.svg";

const SidebarLg = ({ show, onClose }) => {
  const { user } = useContext(AuthContext);
  const { content } = useContext(LanguageContext);
  const { data } = useFetch(BASE_URL + "/contact");
  const contacts = data?.map((contact) => ({
    ...contact // Copy existing object properties
    // image: contact.name == "Viber"
    //   ? viber
    //   : contact.name == "Telegram"
    //     ? tele
    //     : contact.name == "Facebook"
    //       ? fb
    //       : null, // Default to null if no condition matches
  }));

  // console.log('contacts', contacts);

  const { logout, loading } = useLogout();
  const handleLogout = async (e) => {
    e.preventDefault();
    await logout();
    onClose();
  }

  const navLinks = [
    { img: home, name: content?.nav?.home?.toUpperCase(), link: '/?type=all' },
    { img: titok, name: content?.nav?.videos?.toUpperCase(), link: '/reels' },
    { img: profile, name: content?.profile?.my_profile?.toUpperCase(), link: '/information?tab=profile' },
    { img: money, name: content?.wallet?.wallet?.toUpperCase(), link: '/information?tab=transfer' },
    { img: log, name: content?.nav?.logs?.toUpperCase(), link: '/information?tab=logs&type=deposit' },
    { img: promotion, name: content?.nav?.promotion?.toUpperCase(), link: '/promotion' },
    { img: contact, name: content?.nav?.contact?.toUpperCase(), link: '/contact' },
    // { img: ranking, name: content?.nav?.ranking, link: '/ranking' },
    // { img: log, name: 'MORNING BET SLIP', link: '/morning-bet-slip' }
  ];
  const items = [
    { img: slot, link: "/?type=slot", value: "2", name: content?.game_type?.slot },
    { img: fish, link: "/?type=arcade", value: "4", name: content?.game_type?.arcade },
    { img: football, link: "/?type=table", value: "5", name: content?.game_type?.table },
    { img: card, link: "/?type=casino", value: "6", name: content?.game_type?.casino },
    { img: fish, link: "/?type=lottery", value: "8", name: content?.game_type?.lottery },
    { img: football, link: "/?type=bingo", value: "9", name: content?.game_type?.bingo },
    { img: fish, link: "/?type=fishing", value: "0", name: content?.game_type?.fishing },
    // { img: football, link: "/?type=sport book&list=SBO", value: "sport book", name: 'အားကစားဂိမ်းများ' },
  ];

  return (
    <>
      {/* Overlay */}
      {show && (
        <div className="fixed inset-0 z-40 bg-black/60" onClick={onClose} aria-label="Close sidebar" />
      )}
      {/* Sidebar Drawer */}
      <div className={`fixed top-0 right-0 h-full w-80 max-w-full bg-[#181c2f] z-50 transition-transform duration-300 ${show ? 'translate-x-0' : 'translate-x-full'} flex flex-col`}>
        <div className="flex items-center justify-end p-4 border-b border-gray-700">
          <button
            className="text-2xl text-white hover:bg-white/10 rounded-full p-2"
            onClick={onClose}
            aria-label="Close sidebar"
          >
            &times;
          </button>
        </div>
        <div className="flex-1 overflow-y-auto flex flex-col items-center gap-4 p-4">
          <Link to={'/information?tab=profile'} onClick={onClose} className="flex items-center gap-3 w-full">
            <img src={profile} className="w-10 h-10 rounded-full" alt="Profile" />
            <span className="text-white font-semibold text-lg">{user?.name}</span>
          </Link>
          <div className="flex items-center gap-2 bg-gray-700 rounded-full px-4 py-2 w-full justify-center">
            <img src={coin} className="w-6 h-6" alt="Coin" />
            <span className="text-white font-bold">{Number(user?.balance).toLocaleString()} Ks</span>
          </div>
          <Link to={'/information?tab=transfer'} onClick={onClose} className="flex items-center justify-center w-full bg-gradient-to-r from-yellow-400 to-yellow-600 text-gray-900 font-bold py-2 rounded-full mt-2">
            <CoinsIcon size={24} className='mr-2' />
            <span>{content?.wallet?.deposit?.toUpperCase()}</span>
          </Link>
          <div className="flex flex-col gap-2 w-full mt-4">
            {navLinks.map((item, index) => (
              <Link to={item.link} key={index} onClick={onClose} className="flex items-center gap-2 py-2 px-4 rounded-xl hover:bg-white/10 transition-colors text-white font-semibold text-base">
                <img src={item.img} className="w-7 h-7" alt={item.name} />
                <span>{item.name}</span>
              </Link>
            ))}
          </div>
          <div className="flex justify-center gap-4 mt-6 mb-8">
            {contacts && contacts.map((contact, index) => (
              <Link to={contact.link} key={index} onClick={onClose}>
                <img src={"https://ag.onexbetmm.site/api/../"+contact.image} className="rounded-lg w-12 h-12" alt={contact.name} />
              </Link>
            ))}
          </div>
          <div className="flex justify-center my-4">
            <a href='https://ag.onexbetmm.site/assets/app/bossi.apk' target='_blank' rel="noreferrer" className="inline-block text-center border border-white px-4 py-2 rounded-full text-white no-underline font-semibold hover:bg-white hover:text-gray-900 transition-colors duration-200">
              Download App
            </a>
          </div>

        

            <button
                onClick={handleLogout}
                className="w-full py-4 bg-red-600 hover:bg-red-700 text-white font-bold text-lg rounded-b-xl transition-colors mt-auto mb-10"
            >
                {loading ? <span className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span> : null}
                {content?.profile?.logout?.toUpperCase()}
            </button>

        </div>
        {/* Sticky Logout Button at the bottom */}



      </div>


    </>
  )
}

export default SidebarLg
