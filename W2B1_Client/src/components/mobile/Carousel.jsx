import React, { useContext, useState } from 'react';
import { GeneralContext } from '../../contexts/GeneralContext';
import { AuthContext } from "../../contexts/AuthContext";

const Carousels = () => {
  const { banners } = useContext(GeneralContext);
  const { user } = useContext(AuthContext);
  const [current, setCurrent] = useState(0);

  // Debug logging
  // console.log('Carousel Debug:', { banners, user, bannersLength: banners?.length });

  // Temporarily remove user check to see if data is being fetched
  // if (!user) return null;

  const goTo = (idx) => setCurrent(idx);
  const prev = () => setCurrent((prev) => (prev === 0 ? (banners.length - 1) : prev - 1));
  const next = () => setCurrent((prev) => (prev === banners.length - 1 ? 0 : prev + 1));

  return (
    <div className="w-full px-0 sm:px-2 pt-0 pb-2">
      <div className="relative w-full rounded-3xl overflow-hidden shadow-2xl bg-gradient-to-br from-yellow-400/70 via-white/10 to-yellow-600/70 p-1 border-2 border-transparent">
        <div className="w-full h-full rounded-3xl bg-[#181A29]">
          {banners && banners.length > 0 ? (
            <img
              src={"https://ag.onexbetmm.site/api/.." + banners[current].img_url}
              className="w-full h-48 sm:h-64 object-cover object-center transition-all duration-500 rounded-3xl"
              alt={"Banner " + (current + 1)}
            />
          ) : (
            <div className="w-full h-48 sm:h-64 flex items-center justify-center text-white">
              <p>No banners available</p>
            </div>
          )}
          {/* Navigation Buttons */}
          {banners && banners.length > 1 && (
            <>
              <button
                className="absolute left-2 top-1/2 -translate-y-1/2 bg-black/60 hover:bg-yellow-400/80 text-white rounded-full p-2 z-10 focus:outline-none shadow-lg border border-white/10"
                onClick={prev}
                aria-label="Previous"
              >
                <svg className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" d="M15 19l-7-7 7-7" /></svg>
              </button>
              <button
                className="absolute right-2 top-1/2 -translate-y-1/2 bg-black/60 hover:bg-yellow-400/80 text-white rounded-full p-2 z-10 focus:outline-none shadow-lg border border-white/10"
                onClick={next}
                aria-label="Next"
              >
                <svg className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" d="M9 5l7 7-7 7" /></svg>
              </button>
            </>
          )}
          {/* Dots */}
          {banners && banners.length > 1 && (
            <div className="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2 z-10">
              {banners.map((_, idx) => (
                <button
                  key={idx}
                  className={`w-3 h-3 rounded-full border-2 border-white/30 transition-all duration-300 ${current === idx ? 'bg-yellow-400 scale-125 shadow-lg border-yellow-400' : 'bg-white/60'}`}
                  onClick={() => goTo(idx)}
                  aria-label={`Go to slide ${idx + 1}`}
                />
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default Carousels;
