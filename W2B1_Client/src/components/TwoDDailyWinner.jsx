import React, { useState, useEffect, useContext } from 'react';
import { useNavigate } from 'react-router-dom';
import { AuthContext } from '../contexts/AuthContext';
import { LanguageContext } from '../contexts/LanguageContext';
import BASE_URL from '../hooks/baseUrl';
import { FaTrophy, FaCalendarAlt, FaClock, FaUsers, FaMoneyBillWave, FaStar } from 'react-icons/fa';
import { MdEmojiEvents, MdTrendingUp } from 'react-icons/md';

const TwoDDailyWinner = () => {
    const { auth, user } = useContext(AuthContext);
    const { content } = useContext(LanguageContext);
    const navigate = useNavigate();
    
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [selectedDate, setSelectedDate] = useState(new Date().toISOString().split('T')[0]);
    const [selectedSession, setSelectedSession] = useState('');

    useEffect(() => {
        if (!auth) {
            navigate('/login');
            return;
        }

        fetchWinners();
    }, [selectedDate, selectedSession]);

    const fetchWinners = async () => {
        setLoading(true);
        setError(null);

        try {
            let url = `${BASE_URL}/two-d-daily-winners?date=${selectedDate}`;
            if (selectedSession) {
                url += `&session=${selectedSession}`;
            }

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });

            if (response.status === 401) {
                localStorage.removeItem('token');
                navigate('/login');
                return;
            }

            if (response.status === 403) {
                setError('Only players can view winners. Please log in with a player account.');
                setLoading(false);
                return;
            }

            if (!response.ok) {
                throw new Error('Failed to fetch winners');
            }

            const result = await response.json();
            
            if (result.status === 'Request was successful.') {
                setData(result.data);
            } else {
                setError(result.message || 'Failed to fetch winners');
            }
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    };

    const getSessionText = (session) => {
        return session === 'morning' ? 'မနက်ပိုင်း' : 'ညနေပိုင်း';
    };

    const getSessionColor = (session) => {
        return session === 'morning' 
            ? 'from-blue-400 to-blue-600' 
            : 'from-yellow-400 to-orange-500';
    };

    const getSessionIcon = (session) => {
        return session === 'morning' ? '🌅' : '🌆';
    };

    if (!auth) {
        return null;
    }

    return (
        <div className="min-h-screen bg-gradient-to-br from-gray-900 via-purple-900 to-gray-900 p-4">
            {/* Header */}
            <div className="max-w-4xl mx-auto mb-6">
                <div className="flex items-center justify-between mb-6">
                    <button
                        onClick={() => navigate(-1)}
                        className="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl shadow-lg hover:from-purple-700 hover:to-blue-700 transition-all duration-300"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                        </svg>
                        {content?.back || 'Back'}
                    </button>
                    
                    <div className="text-center">
                        <h1 className="text-3xl font-bold text-white mb-2 flex items-center justify-center gap-3">
                            <FaTrophy className="text-yellow-400" />
                            <span className="bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                                {content?.two_d_winners?.title || '2D Daily Winners'}
                            </span>
                            <FaTrophy className="text-yellow-400" />
                        </h1>
                        <p className="text-gray-300 text-sm">{content?.two_d_winners?.subtitle || 'ထိုးဂဏန်းအနိုင်ရရှိသူများ'}</p>
                    </div>
                    
                    <div className="w-20"></div> {/* Spacer for centering */}
                </div>

                {/* Filters */}
                <div className="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {/* Date Picker */}
                        <div className="space-y-2">
                            <label className="text-white font-semibold flex items-center gap-2">
                                <FaCalendarAlt className="text-blue-400" />
                                {content?.two_d_winners?.date || 'Date'}
                            </label>
                            <input
                                type="date"
                                value={selectedDate}
                                onChange={(e) => setSelectedDate(e.target.value)}
                                className="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                            />
                        </div>

                        {/* Session Filter */}
                        <div className="space-y-2">
                            <label className="text-white font-semibold flex items-center gap-2">
                                <FaClock className="text-green-400" />
                                {content?.two_d_winners?.session || 'Session'}
                            </label>
                            <select
                                value={selectedSession}
                                onChange={(e) => setSelectedSession(e.target.value)}
                                className="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-black focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent"
                            >
                                <option value="">{content?.two_d_winners?.all_sessions || 'All Sessions'}</option>
                                <option value="morning">{content?.two_d_winners?.morning || 'မနက်ပိုင်း'}</option>
                                <option value="evening">{content?.two_d_winners?.evening || 'ညနေပိုင်း'}</option>
                            </select>
                        </div>

                        {/* Refresh Button */}
                        <div className="space-y-2">
                            <label className="text-white font-semibold flex items-center gap-2">
                                <MdTrendingUp className="text-purple-400" />
                                {content?.two_d_winners?.actions || 'Actions'}
                            </label>
                            <button
                                onClick={fetchWinners}
                                disabled={loading}
                                className="w-full px-4 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl font-semibold hover:from-purple-700 hover:to-blue-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {loading ? (content?.two_d_winners?.loading || 'Loading...') : (content?.two_d_winners?.refresh || 'Refresh')}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {/* Content */}
            <div className="max-w-4xl mx-auto">
                {loading && (
                    <div className="flex items-center justify-center py-20">
                        <div className="animate-spin rounded-full h-16 w-16 border-b-2 border-yellow-400"></div>
                    </div>
                )}

                {error && (
                    <div className="bg-red-500/20 border border-red-400/50 rounded-2xl p-6 text-center">
                        <p className="text-red-300 font-semibold">{error}</p>
                    </div>
                )}

                {!loading && !error && data && (
                    <div className="space-y-6">
                        {/* Single Session Result */}
                        {data.date && (
                            <div className="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl">
                                <div className="flex items-center justify-between mb-6">
                                    <div className="flex items-center gap-4">
                                        <div className={`text-2xl ${getSessionIcon(data.session)}`}></div>
                                        <div>
                                            <h2 className="text-2xl font-bold text-white">
                                                {getSessionText(data.session)}
                                            </h2>
                                            <p className="text-gray-300">{data.date}</p>
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <div className="text-sm text-gray-400">{content?.two_d_winners?.winning_number || 'Winning Number'}</div>
                                        <div className="text-4xl font-bold bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                                            {data.win_digit}
                                        </div>
                                    </div>
                                </div>

                                {/* Winners List */}
                                <div className="space-y-4">
                                    <div className="flex items-center gap-2 mb-4">
                                        <FaUsers className="text-blue-400" />
                                        <h3 className="text-xl font-semibold text-white">
                                            {content?.two_d_winners?.winners || 'Winners'} ({data.winners?.length || 0})
                                        </h3>
                                    </div>

                                    {data.winners && data.winners.length > 0 ? (
                                        <div className="grid gap-4">
                                            {data.winners.map((winner, index) => (
                                                <div
                                                    key={index}
                                                    className="bg-gradient-to-r from-white/10 to-white/5 rounded-xl p-4 border border-white/20 hover:border-yellow-400/50 transition-all duration-300"
                                                >
                                                    <div className="flex items-center justify-between">
                                                        <div className="flex items-center gap-4">
                                                            <div className="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full">
                                                                <FaStar className="text-white text-lg" />
                                                            </div>
                                                            <div>
                                                                <h4 className="text-lg font-semibold text-white">
                                                                    {winner.member_name}
                                                                </h4>
                                                                <p className="text-gray-400 text-sm">
                                                                    {content?.two_d_winners?.bet_amount || 'Bet Amount'}: {formatCurrency(winner.bet_amount)} Ks
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div className="text-right">
                                                            <div className="text-sm text-gray-400">{content?.two_d_winners?.win_amount || 'Win Amount'}</div>
                                                            <div className="text-2xl font-bold text-green-400">
                                                                {formatCurrency(winner.win_amount)} Ks
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <div className="text-center py-8">
                                            <MdEmojiEvents className="text-6xl text-gray-500 mx-auto mb-4" />
                                            <p className="text-gray-400 text-lg">{content?.two_d_winners?.no_winners || 'No winners for this session'}</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}

                        {/* Multiple Sessions Results */}
                        {data.latest_results && (
                            <div className="space-y-6">
                                <h2 className="text-2xl font-bold text-white text-center mb-6">
                                    Latest Results
                                </h2>
                                
                                {data.latest_results.map((result, index) => (
                                    <div
                                        key={index}
                                        className="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl"
                                    >
                                        <div className="flex items-center justify-between mb-6">
                                            <div className="flex items-center gap-4">
                                                <div className={`text-2xl ${getSessionIcon(result.session)}`}></div>
                                                <div>
                                                    <h3 className="text-xl font-bold text-white">
                                                        {getSessionText(result.session)}
                                                    </h3>
                                                    <p className="text-gray-300">{result.date}</p>
                                                </div>
                                            </div>
                                            <div className="text-right">
                                                <div className="text-sm text-gray-400">Winning Number</div>
                                                <div className="text-3xl font-bold bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                                                    {result.win_digit}
                                                </div>
                                            </div>
                                        </div>

                                        {/* Winners for this session */}
                                        <div className="space-y-3">
                                            <div className="flex items-center gap-2 mb-3">
                                                <FaUsers className="text-blue-400" />
                                                <span className="text-white font-semibold">
                                                    Winners ({result.winners?.length || 0})
                                                </span>
                                            </div>

                                            {result.winners && result.winners.length > 0 ? (
                                                <div className="grid gap-3">
                                                    {result.winners.map((winner, winnerIndex) => (
                                                        <div
                                                            key={winnerIndex}
                                                            className="bg-gradient-to-r from-white/10 to-white/5 rounded-lg p-3 border border-white/20"
                                                        >
                                                            <div className="flex items-center justify-between">
                                                                <div className="flex items-center gap-3">
                                                                    <div className="w-8 h-8 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center">
                                                                        <FaStar className="text-white text-sm" />
                                                                    </div>
                                                                    <div>
                                                                        <h4 className="text-white font-medium">
                                                                            {winner.member_name}
                                                                        </h4>
                                                                        <p className="text-gray-400 text-xs">
                                                                            Bet: {formatCurrency(winner.bet_amount)} Ks
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div className="text-right">
                                                                    <div className="text-lg font-bold text-green-400">
                                                                        {formatCurrency(winner.win_amount)} Ks
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    ))}
                                                </div>
                                            ) : (
                                                <div className="text-center py-4">
                                                    <p className="text-gray-400">No winners</p>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                )}

                {!loading && !error && !data && (
                    <div className="text-center py-20">
                        <FaTrophy className="text-6xl text-gray-500 mx-auto mb-4" />
                        <p className="text-gray-400 text-lg">No data available</p>
                    </div>
                )}
            </div>
        </div>
    );
};

export default TwoDDailyWinner; 