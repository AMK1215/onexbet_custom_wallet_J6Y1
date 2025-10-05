import React, { useContext, useState, useRef, useEffect } from 'react'
import mm from '../assets/img/mm.png'
import en from '../assets/img/en.png'
import zh from '../assets/img/zh.png'
import th from '../assets/img/th.png'
import { LanguageContext } from '../contexts/LanguageContext'

const flags = {
  mm: mm,
  en: en,
  zh: zh,
  th: th,
};

const LanguageDropdown = () => {
  const { lan, updateLanguage } = useContext(LanguageContext);
  const [open, setOpen] = useState(false);
  const ref = useRef();

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (ref.current && !ref.current.contains(event.target)) {
        setOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  return (
    <div className="relative" ref={ref}>
      <button
        className="flex items-center justify-center w-9 h-9 md:w-12 md:h-12 rounded-xl bg-cyan-400 hover:bg-cyan-500 transition p-0 border-2 border-cyan-300 shadow"
        onClick={() => setOpen((o) => !o)}
        aria-label="Select language"
      >
        <img src={flags[lan] || en} className="w-7 h-7 md:w-9 md:h-9 object-contain rounded-full" alt={lan} />
      </button>
      {open && (
        <div className="absolute right-0 mt-2 w-24 bg-[#23243a] border border-gray-700 rounded-xl shadow-lg flex flex-col z-50">
          {Object.entries(flags).map(([key, flag]) => (
            <button
              key={key}
              onClick={() => { updateLanguage(key); setOpen(false); }}
              className="flex items-center gap-2 px-3 py-2 hover:bg-cyan-100/10 transition rounded-xl"
            >
              <img src={flag} className="w-6 h-6 object-contain rounded-full" alt={key} />
              <span className="text-xs text-white capitalize">{key}</span>
            </button>
          ))}
        </div>
      )}
    </div>
  )
}

export default LanguageDropdown
