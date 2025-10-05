import React, { useContext, useEffect, useState } from 'react';
import { LanguageContext } from '../../contexts/LanguageContext';
import { IoGameController } from 'react-icons/io5';
import { useNavigate } from 'react-router-dom';
import { GameContext } from '../../contexts/GameContext';
import BASE_URL from '../../hooks/baseUrl';

export default function ProviderList({ typeCode, type }) {
    const { content } = useContext(LanguageContext);
    const { updateType, updateProvider } = useContext(GameContext);
    const navigate = useNavigate();

    const [providers, setProviders] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        if (typeCode) {
            setLoading(true);
            fetch(`${BASE_URL}/providers/${typeCode}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                }
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                setProviders(data.data || []);
                setLoading(false);
            })
            .catch(error => {
                console.error('Error fetching providers:', error);
                setLoading(false);
            });
        }
    }, [typeCode]);

    if (loading) return (
      <div className="flex justify-center items-center py-8 w-full">
        <div className="w-8 h-8 border-4 border-yellow-400 border-t-transparent rounded-full animate-spin" />
      </div>
    );

    return (
      <div className="grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 w-full">
        {providers.length === 0 ? (
          <p className="col-span-full text-center text-gray-400 py-6">{content?.no_data}</p>
        ) : (
          providers.map((item, index) => (
            <div
              key={index}
              className="cursor-pointer group flex flex-col items-center"
              onClick={() => {
                navigate(`/?type=${type?.id}&provider=${item.id}`);
                updateType(type.id);
                updateProvider(item.id);
              }}
            >
              <div className="relative w-full aspect-square overflow-hidden rounded-2xl bg-gradient-to-br from-yellow-400/70 via-white/10 to-yellow-600/70 p-1 group-hover:from-yellow-400 group-hover:to-orange-400 group-hover:shadow-2xl transition-all duration-200">
                <div className="w-full h-full bg-black/60 rounded-2xl flex items-center justify-center group-hover:bg-black/80 transition-all duration-200">
                  <img
                    src={"https://gamestar77.online/api/.." + item.img_url}
                    alt={item.product_name}
                    className="w-full h-full object-cover rounded-2xl group-hover:scale-105 transition-transform duration-200"
                  />
                </div>
              </div>
              <div className="mt-2 flex justify-center w-full">
                <span className="px-4 py-1 bg-gradient-to-r from-yellow-400 via-orange-400 to-yellow-600 text-black font-bold text-xs rounded-full shadow text-center max-w-[90%] truncate whitespace-nowrap border border-yellow-300 group-hover:scale-105 transition-transform duration-200">
                  {item.product_title}
                </span>
              </div>
            </div>
          ))
        )}
      </div>
    );
}
