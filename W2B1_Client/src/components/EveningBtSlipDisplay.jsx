import React from 'react';
import useFetch from '../hooks/useFetch';
import BASE_URL from '../hooks/baseUrl.jsx';
import '../tailwind_css.css';

// BetSlipDisplay Component: Displays the details of a two-digit bet slip.
// It receives the bet slip data as a prop.
const EveningBtSlipDisplay = () => {
  // Fetch bet slip data from API
  const { data, loading, error } = useFetch(`${BASE_URL}/evening-twod-bet-slips`);

  // Debug logs
  if (loading) {
    console.log('Loading bet slip data...');
  }
  if (error) {
    console.log('Error fetching bet slip data:', error);
  }
  if (data) {
    console.log('Fetched bet slip data:', data);
  }

  if (loading) {
    return (
      <div className="p-6 bg-gray-800 rounded-lg shadow-lg text-white text-center">
        <p className="text-xl text-blue-400">Loading...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="p-6 bg-gray-800 rounded-lg shadow-lg text-white text-center">
        <p className="text-xl text-red-400">{error}</p>
      </div>
    );
  }

  // Check if data is null/undefined or empty
  if (!data || data.length === 0) {
    return (
      <div className="p-6 bg-gray-800 rounded-lg shadow-lg text-white text-center">
        <p className="text-xl text-yellow-400">လောင်းကြေးစာရင်း အချက်အလက် မရှိပါ။</p> {/* Myanmar: "No bet slip data available." */}
      </div>
    );
  }

  // Display the first slip in the data array
  const slip = data[0];

  return (
    <div className="p-6 bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl mx-auto text-white font-inter border border-gray-700">
      <h2 className="text-3xl font-bold text-teal-400 mb-6 text-center">လောင်းကြေးစာရင်း အသေးစိတ်</h2> {/* Myanmar: "Bet Slip Details" */}

      {/* Slip Header */}
      <div className="flex justify-between items-center mb-4 pb-4 border-b border-gray-700">
        <p className="text-lg font-semibold text-gray-300">
          <span className="text-teal-300">စလစ်နံပါတ်:</span> {slip.slip_no} {/* Myanmar: "Slip No:" */}
        </p>
        <p className="text-lg font-semibold text-gray-300">
          <span className="text-teal-300">ကစားသမား:</span> {slip.player_name} {/* Myanmar: "Player:" */}
        </p>
      </div>

      {/* Main Slip Details */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div className="bg-gray-700 p-4 rounded-lg shadow-inner">
          <p className="text-md text-gray-400">စုစုပေါင်း လောင်းကြေးပမာဏ:</p> {/* Myanmar: "Total Bet Amount:" */}
          <p className="text-2xl font-bold text-white">${parseFloat(slip.total_bet_amount).toFixed(2)}</p>
        </div>
        <div className="bg-gray-700 p-4 rounded-lg shadow-inner">
          <p className="text-md text-gray-400">Session:</p>
          <p className="text-2xl font-bold text-white capitalize">{slip.session}</p>
        </div>
        <div className="bg-gray-700 p-4 rounded-lg shadow-inner">
          <p className="text-md text-gray-400">အခြေအနေ:</p> {/* Myanmar: "Status:" */}
          <p className={`text-2xl font-bold capitalize ${slip.status === 'pending' ? 'text-yellow-400' : 'text-green-400'}`}>
            {slip.status}
          </p>
        </div>
        <div className="bg-gray-700 p-4 rounded-lg shadow-inner">
          <p className="text-md text-gray-400">ဂိမ်းရက်စွဲ:</p> {/* Myanmar: "Game Date:" */}
          <p className="text-xl font-bold text-white">{slip.game_date}</p>
        </div>
      </div>

      {/* Balance Changes */}
      <div className="flex justify-between items-center mb-6 p-4 bg-gray-700 rounded-lg shadow-inner">
        <div>
          <p className="text-md text-gray-400">အရင်လက်ကျန်:</p> {/* Myanmar: "Before Balance:" */}
          <p className="text-xl font-bold text-white">${parseFloat(slip.before_balance).toFixed(2)}</p>
        </div>
        <div>
          <p className="text-md text-gray-400">ပြီးနောက်လက်ကျန်:</p> {/* Myanmar: "After Balance:" */}
          <p className="text-xl font-bold text-white">${parseFloat(slip.after_balance).toFixed(2)}</p>
        </div>
      </div>

      {/* Individual Bets Section */}
      <h3 className="text-2xl font-bold text-teal-400 mb-4 text-center">လောင်းကြေးများ</h3> {/* Myanmar: "Bets" */}
      <div className="space-y-3">
        {slip.two_bets.map((betItem) => (
          <div key={betItem.id} className="bg-gray-700 p-4 rounded-lg shadow-md flex justify-between items-center">
            <div>
              <p className="text-lg font-semibold text-white">ဂဏန်း: <span className="text-yellow-300">{betItem.bet_number}</span></p> {/* Myanmar: "Number:" */}
              <p className="text-md text-gray-400">ပမာဏ: ${parseFloat(betItem.bet_amount).toFixed(2)}</p> {/* Myanmar: "Amount:" */}
            </div>
            <div className="text-right">
              <p className="text-lg font-semibold text-white">
                အနိုင်/အရှုံး:{" "} {/* Myanmar: "Win/Lose:" */}
                <span className={`${betItem.win_lose ? 'text-green-400' : 'text-red-400'}`}>
                  {betItem.win_lose ? 'အနိုင်' : 'အရှုံး'} {/* Myanmar: "Win" : "Lose" */}
                </span>
              </p>
              <p className="text-md text-gray-400">ဖြစ်နိုင်ခြေရှိသော ပေးချေမှု: ${parseFloat(betItem.potential_payout).toFixed(2)}</p> {/* Myanmar: "Potential Payout:" */}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

export default EveningBtSlipDisplay;
