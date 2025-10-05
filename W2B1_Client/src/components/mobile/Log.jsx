import React, { useContext } from 'react'
import TransferLogs from './TransferLogs';
import GameLogs from '../mobile/GameLogs';
import DepositLog from '../mobile/DepositLog';
import WithdrawLog from '../mobile/WithdrawLog';
import { Link, useSearchParams } from 'react-router-dom';
import { LanguageContext } from '../../contexts/LanguageContext';
import { FaArrowDown, FaArrowUp, FaGamepad } from 'react-icons/fa';

const Log = () => {
  const { content } = useContext(LanguageContext);
  const [searchParams] = useSearchParams();
  const type = searchParams.get('type');

  const tabs = [
    {
      id: 1,
      name: content?.wallet?.deposit || 'Deposit',
      value: 'deposit',
      link: '/information?tab=logs&type=deposit',
      icon: <FaArrowDown className="text-lg md:text-xl" />, 
      color: 'from-green-400 to-green-600',
      text: 'text-green-600',
      bg: 'bg-green-100',
      active: 'bg-gradient-to-r from-green-400 to-green-600 text-white shadow-lg',
    },
    {
      id: 2,
      name: content?.wallet?.withdraw || 'Withdraw',
      value: 'withdraw',
      link: '/information?tab=logs&type=withdraw',
      icon: <FaArrowUp className="text-lg md:text-xl" />, 
      color: 'from-red-400 to-red-600',
      text: 'text-red-600',
      bg: 'bg-red-100',
      active: 'bg-gradient-to-r from-red-400 to-red-600 text-white shadow-lg',
    },
    {
      id: 3,
      name: content?.log?.game_log || 'Game Log',
      value: 'game_logs',
      link: '/information?tab=logs&type=game_logs',
      icon: <FaGamepad className="text-lg md:text-xl" />, 
      color: 'from-purple-400 to-purple-600',
      text: 'text-purple-600',
      bg: 'bg-purple-100',
      active: 'bg-gradient-to-r from-purple-400 to-purple-600 text-white shadow-lg',
    },
  ];

  return (
      <div className="w-full max-w-3xl mx-auto my-4 mb-5 pb-5 px-2">
          <div className="flex flex-row items-center justify-center gap-2 mb-8">
              {tabs.map((tab, index) => (
                  <Link
                      to={tab.link}
                      key={index}
                      className={`flex flex-col items-center justify-center px-2 py-2 rounded-xl font-medium text-sm md:text-base transition-all duration-200 cursor-pointer focus:outline-none border-2 border-transparent hover:scale-105 hover:shadow-xl ${
                          type === tab.value
                              ? tab.active
                              : `${tab.bg} ${tab.text} hover:border-yellow-400/60`
                      }`}
                      style={{ minWidth: '80px' }}
                  >
                      <span className="mb-1 text-xs md:text-sm">{tab.icon}</span>
                      <span className="text-xs md:text-sm">{tab.name}</span>
                  </Link>
              ))}
          </div>

          {/* cash in */}
          {type === "deposit" && <DepositLog />}
          {/* cash out */}
          {type === "withdraw" && <WithdrawLog />}
          {/* game logs */}
          {type === "game_logs" && <GameLogs />}
      </div>
  )
}

export default Log