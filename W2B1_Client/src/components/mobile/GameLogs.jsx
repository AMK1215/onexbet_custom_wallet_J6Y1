import React, { useContext, useState } from 'react'
import { LanguageContext } from '../../contexts/LanguageContext';
import useFetch from '../../hooks/useFetch';
import BASE_URL from '../../hooks/baseUrl';
import { FaGamepad, FaCalendarAlt, FaChartLine, FaTrophy, FaCoins, FaArrowUp, FaArrowDown, FaClock, FaSpinner } from 'react-icons/fa'

export default function GameLogs() {
  const { content } = useContext(LanguageContext);
  const [selectedDate, setSelectedDate] = useState('today');
  const {data:logs, loading} = useFetch(`${BASE_URL}/player/game-logs?type=${selectedDate}`); 

  // Date filter options
  const dateFilters = [
    { key: 'today', label: content?.log?.today || 'Today', icon: <FaCalendarAlt /> },
    { key: 'yesterday', label: content?.log?.yesterday || 'Yesterday', icon: <FaClock /> },
    { key: 'this_week', label: content?.log?.this_week || 'This Week', icon: <FaChartLine /> },
    { key: 'last_week', label: content?.log?.last_week || 'Last Week', icon: <FaTrophy /> }
  ];

  // Loading state
  if (loading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-[#101223] via-[#1a1d3a] to-[#101223] flex items-center justify-center">
        <div className="text-center">
          <FaSpinner className="animate-spin text-4xl text-yellow-400 mx-auto mb-4" />
          <p className="text-white text-lg">Loading game logs...</p>
        </div>
      </div>
    );
  }

  return (
      <div className="max-w-4xl mx-auto my-4 mb-5 pb-5 px-1 sm:px-2">
          <h2 className="text-2xl font-extrabold text-yellow-400 mb-6 text-center tracking-wide drop-shadow flex items-center justify-center gap-2">
              <FaGamepad className="text-yellow-400" /> Game Logs
          </h2>
          <p className="text-gray-300 text-center mb-4">Track your gaming activity and performance across different time periods</p>
          {/* Date Filter Buttons in a Row */}
          <div className="flex flex-row flex-nowrap overflow-x-auto items-center justify-start gap-2 mb-6 px-4 scrollbar-hide">
              {dateFilters.map((filter) => (
                  <button
                      key={filter.key}
                      onClick={() => setSelectedDate(filter.key)}
                      className={`flex items-center gap-1 min-w-[100px] px-3 py-1.5 rounded-lg font-medium text-sm transition-all duration-300 focus:outline-none border-2 border-transparent hover:scale-105 hover:shadow ${
                          selectedDate === filter.key
                              ? 'bg-gradient-to-r from-yellow-400 to-orange-500 text-black shadow-md'
                              : 'bg-[#1f2338] text-white border border-gray-600 hover:border-yellow-400/50 hover:bg-[#2a2e45]'
                      } whitespace-nowrap`}
                  >
                      <span className="text-base">{filter.icon}</span>
                      <span>{filter.label}</span>
                  </button>
              ))}
          </div>

          {/* Game Logs Table (always visible, responsive) */}
          <div className="overflow-x-auto rounded-2xl border border-gray-700 bg-white/5 shadow-lg">
              {(!logs || logs.length === 0) ? (
                  <div className="w-full py-16 flex flex-col items-center justify-center">
                      <div className="text-gray-400 text-6xl mb-4">📊</div>
                      <h3 className="text-xl font-semibold text-white mb-2">No Game Logs Found</h3>
                      <p className="text-gray-400 max-w-md mx-auto">
                          {content?.no_data || 'No game logs available for the selected time period. Start playing to see your activity here!'}
                      </p>
                  </div>
              ) : (
                  <table className="min-w-full w-full text-base text-left">
                      <thead>
                      <tr className="bg-[#181A29] text-yellow-300 text-lg">
                          <th className="px-4 py-3 font-bold whitespace-nowrap"><FaGamepad className="inline mr-1 text-yellow-400" />{content?.log?.game_name || 'Game'}</th>
                          <th className="px-4 py-3 font-bold whitespace-nowrap"><FaClock className="inline mr-1 text-blue-400" />{content?.log?.date || 'Date'}</th>
                          <th className="px-4 py-3 font-bold whitespace-nowrap"><FaChartLine className="inline mr-1 text-green-400" />{content?.log?.count || 'Spins'}</th>
                          <th className="px-4 py-3 font-bold whitespace-nowrap"><FaCoins className="inline mr-1 text-yellow-400" />{content?.log?.bet_amount || 'Bet'}</th>
                          <th className="px-4 py-3 font-bold whitespace-nowrap">{content?.log?.win_lose || 'Win/Loss'}</th>
                      </tr>
                      </thead>
                      <tbody>
                      {logs.map((log, index) => (
                          <tr key={index} className={`transition ${index % 2 === 0 ? 'bg-[#23243a]' : 'bg-[#181A29]'} hover:bg-[#2c2d4a]`}>
                              <td className="px-4 py-3 whitespace-nowrap text-white font-medium">{log.game_name}</td>
                              <td className="px-4 py-3 whitespace-nowrap text-white font-medium">{log.from} - {log.to}</td>
                              <td className="px-4 py-3 whitespace-nowrap text-white font-medium">{log.spin_count}</td>
                              <td className="px-4 py-3 whitespace-nowrap text-white font-medium">{parseFloat(log.turnover).toLocaleString()}</td>
                              <td className="px-4 py-3 whitespace-nowrap font-bold">
                    <span className={parseFloat(log.win_loss) >= 0 ? 'text-green-400 flex items-center gap-1' : 'text-red-400 flex items-center gap-1'}>
                      {parseFloat(log.win_loss) >= 0 ? <FaArrowUp /> : <FaArrowDown />}
                        {parseFloat(log.win_loss) >= 0 ? '+' : ''}{parseFloat(log.win_loss).toLocaleString()}
                    </span>
                              </td>
                          </tr>
                      ))}
                      </tbody>
                  </table>
              )}
          </div>
      </div>
  )
}
