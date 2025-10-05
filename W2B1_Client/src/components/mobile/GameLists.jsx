import React, { useContext, useEffect } from 'react';
import '../../assets/css/games.css';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { LanguageContext } from '../../contexts/LanguageContext';
import useFetch from '../../hooks/useFetch';
import BASE_URL from '../../hooks/baseUrl';
import { GameContext } from '../../contexts/GameContext';

const GameLists = () => {
  const { content } = useContext(LanguageContext);
  const { providers } = useContext(GameContext);
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const type = searchParams.get('type');
  const tab = searchParams.get('tab');
  const provider = searchParams.get('provider');
  
  const handleProviderClick = (providerCode) => {
    navigate(`/games?type=${type}&provider=${providerCode}`);
  };
  
  return (
   <div className="container">
     <div className='d-flex justify-content-around gap-1 provider_list my-3'>
      {!tab && providers.map((p, index) => (
        <div
          key={index}
          onClick={() => handleProviderClick(p.code)}
          className={`cursor-pointer py-1 px-3 px-sm-4 rounded-2 gameProvider ${
            provider == p.code ? 'activeGameList' : ''
          }`}
          style={{ minWidth: '100px', textAlign: 'center' }}
        >
          {p.short_name}
        </div>
      ))}
    </div>
   </div>
  );
};

export default GameLists;