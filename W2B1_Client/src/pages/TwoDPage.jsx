// TwoDPage.jsx
import React, { useState, useEffect, useContext } from "react";
import { NavLink, useNavigate } from "react-router-dom";
// React-Bootstrap Modal is kept for simplicity.
import { Modal } from "react-bootstrap";
// React Icons for visual elements
import { BiCheckCircle } from "react-icons/bi";
// Static assets (images)
import list from "../assets/img/list.png";
import winner from "../assets/img/winner.png";
import holiday from "../assets/img/holiday.png";
// UserWallet component (assumed to be styled with Tailwind internally)
import UserWallet from "../components/UserWallet";
// Import custom CSS
import "../assets/css/twoD.css";
import { LanguageContext } from "../contexts/LanguageContext";

const TwoDPage = () => {
    // Static times for the modal, removing unused ones
    const times = [
        { id: 2, time: "12:00 PM" },
        { id: 4, time: "04:30 PM" },
    ];

    const [show, setShow] = useState(false);
    const handleClose = () => setShow(false);
    const handleShow = () => setShow(true);

    // Static user data, replacing API call
    const user = { balance: 9876543, name: "Jane Doe" };

    // Static data for lottery home links
    const lottoHome = [
        { id: 1, title: "မှတ်တမ်း", img: list, link: "/morning-bet-slip" },
        { id: 2, title: "ကံထူးရှင်များ", img: winner, link: "/2d/daily-winner" },
        { id: 3, title: "ပိတ်ရက်", img: holiday, link: "/2d/holiday" },
    ];

    // --- Live 2D Data Integration ---
    const [liveData, setLiveData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
      let intervalId;

      const fetchLiveData = () => {
        fetch("https://api.thaistock2d.com/live")
          .then(res => res.json())
          .then(data => {
            setLiveData(data);
            setLoading(false);
          })
          .catch(err => {
            setError("Failed to load live data");
            setLoading(false);
          });
      };

      fetchLiveData(); // Initial fetch
      intervalId = setInterval(fetchLiveData, 2000); // Fetch every 2 seconds

      return () => clearInterval(intervalId); // Cleanup on unmount
    }, []);
    // --- End Live 2D Data Integration ---

    const navigate = useNavigate();
    const { content } = useContext(LanguageContext);

    // Removed authentication check useEffect as API is removed.
    // useEffect(() => {
    //   if (!auth) {
    //     navigate("/login");
    //   }
    // }, [auth, navigate]);

    // Static data for modern and internet digits, replacing API calls
    const modern = {
        modern_morningData: { modern_digit: "45" },
        modern_eveningData: { modern_digit: "78" },
    };
    const internet = {
        morningData: { internet_digit: "32" },
        eveningData: { internet_digit: "01" },
    };

    // Add state for history modal
    const [showHistoryModal, setShowHistoryModal] = useState(false);
    const handleHistoryModalOpen = () => setShowHistoryModal(true);
    const handleHistoryModalClose = () => setShowHistoryModal(false);

    // Loading and error states for live data
    if (loading) return <div>Loading...</div>;
    if (error) return <div>{error}</div>;

    return (
        <div
          className="min-h-screen bg-gradient-to-br from-teal-300 via-blue-400 to-purple-500 pb-20"
        >
          {/* User Wallet */}
          {/* UserWallet removed as per user request */}

          {/* Lottery Home Navigation */}
          <div className="w-full max-w-md mx-auto flex justify-between gap-2 mb-2 sticky top-16 z-30">
            {lottoHome.map((item) => (
              item.title === "မှတ်တမ်း" ? (
                <button
                  key={item.id}
                  className="flex-1"
                  onClick={handleHistoryModalOpen}
                >
                  <div className="flex flex-col items-center p-3 bg-white/60 rounded-xl shadow-lg border-2 border-transparent bg-gradient-to-br from-yellow-400/40 via-white/10 to-blue-400/40 hover:from-yellow-400/80 hover:to-blue-400/80 hover:shadow-2xl transition-all">
                    <img src={item.img} alt={item.title} className="w-8 h-8 mb-1" />
                    <span className="text-xs font-semibold text-gray-800 drop-shadow">{item.title}</span>
                  </div>
                </button>
              ) : (
                <NavLink to={item.link} key={item.id} className="flex-1">
                  <div className="flex flex-col items-center p-3 bg-white/60 rounded-xl shadow-lg border-2 border-transparent bg-gradient-to-br from-yellow-400/40 via-white/10 to-blue-400/40 hover:from-yellow-400/80 hover:to-blue-400/80 hover:shadow-2xl transition-all">
                    <img src={item.img} alt={item.title} className="w-8 h-8 mb-1" />
                    <span className="text-xs font-semibold text-gray-800 drop-shadow">{item.title}</span>
                  </div>
                </NavLink>
              )
            ))}
          </div>

          {/* Current 2D Result Section */}
          <div className="w-full max-w-md mx-auto bg-white/80 rounded-3xl shadow-2xl mb-4 p-6 flex flex-col items-center py-8 border-2 border-transparent bg-gradient-to-br from-yellow-400/40 via-white/10 to-blue-400/40">
            <h1 className="text-7xl font-extrabold text-blue-500 mb-4 drop-shadow-lg animate-pulse bg-white/60 rounded-2xl px-8 py-2 border-4 border-blue-300/40 shadow-xl">{liveData?.live?.twod ?? "--"}</h1>
            <div className="flex items-center gap-2 text-gray-700 mb-2">
              <BiCheckCircle className="text-teal-400 text-xl" />
              <span className="text-xs font-semibold">Updated: {liveData?.live?.date} - {liveData?.live?.time}</span>
            </div>
            <span className="text-xs text-gray-400 mb-4">
              {liveData?.server_time ? `${liveData.server_time} တွင် ထီပိတ်ပါမည်။` : ""}
            </span>

            {/* Previous Results Table */}
            <div className="w-full bg-blue-100/40 rounded-2xl p-4 space-y-4 border border-blue-200/40 shadow">
              {liveData?.result?.map((number, index) => (
                <div key={index} className={`pb-3 ${index !== liveData.result.length - 1 ? 'border-b border-blue-200/60' : ''}`}> 
                  <div className="text-xs font-bold mb-2 text-blue-700 flex items-center gap-2">
                    <span className="inline-block w-2 h-2 rounded-full bg-blue-400"></span>
                    {number.open_time}
                  </div>
                  <div className="grid grid-cols-3 text-center text-xs font-bold text-blue-700 mb-1">
                    <div>Set</div>
                    <div>Value</div>
                    <div>2D</div>
                  </div>
                  <div className="grid grid-cols-3 text-center text-sm font-semibold">
                    <div>{number.set}</div>
                    <div>{number.value}</div>
                    <div>{number.twod}</div>
                  </div>
                </div>
              ))}
            </div>

            {/* Modern and Internet Digits */}
            <div className="w-full bg-purple-100/40 rounded-2xl px-4 py-4 mt-6 space-y-4 border border-purple-300/40 shadow">
              <div className="flex justify-between items-center border-b border-purple-200/60 pb-3">
                <span className="text-xs font-bold flex items-center gap-1"><svg className="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>9:00 AM</span>
                <div className="text-center">
                  <div className="text-xs text-gray-500 flex items-center gap-1"><svg className="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><path d="M12 2v20" /></svg>မော်ဒန်</div>
                  <div className="text-lg font-semibold">{modern?.modern_morningData?.modern_digit ?? "-"}</div>
                </div>
                <div className="text-center">
                  <div className="text-xs text-gray-500 flex items-center gap-1"><svg className="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><path d="M2 12h20" /></svg>အင်တာနက်</div>
                  <div className="text-lg font-semibold">{internet?.morningData?.internet_digit ?? "-"}</div>
                </div>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-xs font-bold flex items-center gap-1"><svg className="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>2:00 PM</span>
                <div className="text-center">
                  <div className="text-xs text-gray-500 flex items-center gap-1"><svg className="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><path d="M12 2v20" /></svg>မော်ဒန်</div>
                  <div className="text-lg font-semibold">{modern?.modern_eveningData?.modern_digit ?? "-"}</div>
                </div>
                <div className="text-center">
                  <div className="text-xs text-gray-500 flex items-center gap-1"><svg className="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><path d="M2 12h20" /></svg>အင်တာနက်</div>
                  <div className="text-lg font-semibold">{internet?.eveningData?.internet_digit ?? "-"}</div>
                </div>
              </div>
            </div>
          </div>

          {/* Floating Action Button for Place Bet */}
          {!show && (
            <button
              onClick={handleShow}
              className="fixed bottom-24 left-1/2 -translate-x-1/2 z-40 bg-gradient-to-br from-yellow-400 to-orange-500 text-white font-bold py-3 px-8 rounded-full shadow-2xl text-lg border-2 border-yellow-400/80 hover:scale-105 active:scale-95 transition-all animate-pulse"
            >
              ထိုးမည်
            </button>
          )}

          {/* Time Selection Modal */}
          <Modal show={show} onHide={handleClose} centered dialogClassName="z-50">
            <Modal.Header closeButton className="!border-b-0 bg-gradient-to-br from-yellow-400/40 via-white/10 to-blue-400/40 rounded-t-2xl">
              <Modal.Title>
                <span className="font-bold text-lg text-blue-700 drop-shadow">ထိုးမည့်အချိန် ရွေးပါ။</span>
              </Modal.Title>
            </Modal.Header>
            <Modal.Body className="flex flex-col gap-4 p-6 bg-white/80 rounded-b-2xl border-t-0">
              {/* Updated text above buttons, smaller and less prominent */}
              {liveData?.live?.date && liveData?.live?.time && (
                <div className="flex items-center justify-center text-xs text-gray-500 mb-2 gap-1">
                  <BiCheckCircle className="text-teal-400 text-base" />
                  <span>Updated: {liveData.live.date} - {liveData.live.time}</span>
                </div>
              )}
              {times.map((item, idx) => (
                <button
                  key={item.id}
                  onClick={() => {
                    setShow(false);
                    navigate(`/2d/bet?time=${item.id}`);
                  }}
                  className="w-full py-4 mb-2 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-extrabold text-xl shadow-lg border-2 border-blue-400/60 hover:from-blue-600 hover:to-indigo-700 transition-all focus:outline-none focus:ring-2 focus:ring-blue-300"
                  style={{ marginBottom: idx === times.length - 1 ? 0 : '1rem' }}
                >
                  {item.time}
                </button>
              ))}
            </Modal.Body>
          </Modal>

          {/* History Session Modal */}
          <Modal show={showHistoryModal} onHide={handleHistoryModalClose} centered dialogClassName="z-50 mb-32">
            <Modal.Header closeButton className="!border-b-0 bg-gradient-to-br from-yellow-400/40 via-white/10 to-blue-400/40 rounded-t-2xl">
              <Modal.Title>
                <span className="font-bold text-lg text-blue-700 drop-shadow">
                  {content?.two_d_history_modal?.title || "မှတ်တမ်း ရွေးပါ။"}
                </span>
              </Modal.Title>
            </Modal.Header>
            <Modal.Body className="flex flex-col gap-4 p-6 bg-white/80 rounded-b-2xl border-t-0">
              <div className="flex flex-col gap-4">
                <button
                  onClick={() => { setShowHistoryModal(false); navigate('/morning-bet-slip'); }}
                  className="w-full py-4 rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 text-white font-extrabold text-xl shadow-lg border-2 border-blue-400/60 hover:from-blue-500 hover:to-blue-700 transition-all focus:outline-none focus:ring-2 focus:ring-blue-300"
                >
                  မနက်ပိုင်းမှတ်တမ်း
                </button>
                <button
                  onClick={() => { setShowHistoryModal(false); navigate('/evening-bet-slip'); }}
                  className="w-full py-4 rounded-2xl bg-gradient-to-br from-yellow-400 to-orange-500 text-white font-extrabold text-xl shadow-lg border-2 border-yellow-400/60 hover:from-yellow-500 hover:to-orange-600 transition-all focus:outline-none focus:ring-2 focus:ring-yellow-300"
                >
                  ညနေပိုင်းမှတ်တမ်း
                </button>
              </div>
            </Modal.Body>
          </Modal>
        </div>
      );
};

export default TwoDPage;
