// TwoDConfirmPage.jsx
import React, { useState, useEffect, useContext } from "react";
import { useNavigate } from "react-router-dom";
// React-Bootstrap Modal is kept for simplicity.
import { Modal } from "react-bootstrap";
// React Toastify for UI feedback (requires installation and setup in your project)
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
// Import custom CSS
import "../assets/css/twoD.css";
import { AuthContext } from '../contexts/AuthContext';
import BASE_URL from "../hooks/baseUrl";
import axios from 'axios';

const TwoDConfirmPage = () => {
    const navigate = useNavigate();
    const { updateProfile } = useContext(AuthContext);
    const bets = JSON.parse(localStorage.getItem("bets"));
    useEffect(() => {
        if (!bets || !bets.amounts || bets.amounts.length === 0) {
            navigate('/');
        }
    }, [bets, navigate]);
    const [total, setTotal] = useState(bets?.totalAmount || 0);
    const [betData, setBetData] = useState(bets?.amounts || []);
    const [smShow, setSmShow] = useState(false);
    const [betAmount, setBetAmount] = useState(0);
    const [num, setNum] = useState("");
    const [loading, setLoading] = useState(false);

    // Function to open the edit modal with specific number and amount
    const popUpModal = (numberToEdit, amountToEdit) => {
        setSmShow(true);
        setBetAmount(amountToEdit);
        setNum(numberToEdit);
    };

    // Function to handle editing a bet
    const editBet = (editNum, newAmount) => {
        // Client-side validation for minimum bet amount
        if (newAmount < 100) {
            toast.error("ထိုးကြေး အနည်းဆုံး ၁၀၀ ထည့်ပါ။", {
                position: "top-right",
                autoClose: 2000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined,
                theme: "dark",
            });
            return; // Stop execution if validation fails
        }

        setBetData((prevState) => {
            let updatedTotal = 0;
            // Map over previous state to update the specific bet
            const updatedBetData = prevState.map((bet) => {
                if (bet.num === editNum) {
                    return { num: editNum, amount: Number(newAmount) };
                }
                return bet;
            });

            // Recalculate total from the updated data
            updatedTotal = updatedBetData.reduce((sum, bet) => sum + bet.amount, 0);
            setTotal(updatedTotal);

            // Update local storage with the new bet data
            localStorage.setItem("bets", JSON.stringify({ totalAmount: updatedTotal, amounts: updatedBetData }));
            return updatedBetData;
        });

        setSmShow(false); // Close the modal after editing
    };

    // Function to delete a single bet
    const deleteBet = (numToDelete) => {
        setBetData((prevState) => {
            // Filter out the bet to be deleted
            const updatedBetData = prevState.filter((bet) => bet.num !== numToDelete);

            // Find the amount of the deleted bet to subtract from total
            const amountToDeduct = prevState.find((bet) => bet.num === numToDelete)?.amount || 0;
            setTotal((prevTotal) => prevTotal - amountToDeduct);

            // Create new object for local storage
            const remainBet = {
                totalAmount: prevState.reduce((totalAcc, bet) => totalAcc + (bet.num !== numToDelete ? bet.amount : 0), 0),
                amounts: updatedBetData
            };
            localStorage.setItem("bets", JSON.stringify(remainBet));

            return updatedBetData;
        });
    };

    const handleConfirmBet = async () => {
        setLoading(true);
        try {
            const res = await fetch(`${BASE_URL}/twod-bet`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${localStorage.getItem("token")}`,
                },
                body: JSON.stringify({
                    totalAmount: total,
                    amounts: betData,
                }),
            });
            const data = await res.json();
            if (res.ok && data.status === "Request was successful.") {
                // Fetch latest user profile and update context
                try {
                    const profileRes = await fetch('/api/user', {
                        headers: {
                            "Authorization": `Bearer ${localStorage.getItem("token")}`,
                        },
                    });
                    const profileData = await profileRes.json();
                    if (profileData && profileData.data) {
                        updateProfile(profileData.data);
                    }
                } catch (e) {
                    // Optionally handle profile update error
                }
                toast.success(data.message || "ထီအောင်မြင်စွာ ထိုးပြီးပါပြီ။", {
                    position: "top-right",
                    autoClose: 2000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined,
                    theme: "dark",
                });
            } else if (Array.isArray(data)) {
                toast.error("Over-limit digits: " + data.join(", "));
            } else {
                toast.error(data.message || "Bet failed!");
            }
        } catch (err) {
            toast.error("Network or server error");
        }
        setLoading(false);
    };

    return (
        <>
            <ToastContainer />
            <div
                className="min-h-screen bg-gradient-to-br from-slate-100 via-blue-50 to-blue-100 py-6 flex flex-col items-center"
            >
                <div className="w-full max-w-md mx-auto bg-white/90 rounded-3xl shadow-xl p-6 mb-6">
                    <div className="flex justify-between items-center mb-4">
                        <h5 className="font-bold text-lg mb-0">ရွေးချယ်ထားသောဂဏန်းများ</h5>
                        <button className="px-4 py-2 rounded-lg bg-blue-500 text-white font-semibold shadow hover:bg-blue-600 transition" onClick={() => navigate(-1)}>
                            ထပ်ထည့်မည်
                        </button>
                    </div>
                    <div className="divide-y divide-blue-100">
                        {betData.map((bet, idx) => (
                            <div
                                key={idx}
                                className="flex justify-between items-center py-4"
                            >
                                <div>
                                    <span className="font-bold text-2xl text-blue-700">{bet.num}</span>
                                    <span className="ml-3 text-gray-500 text-base">{bet.amount} ကျပ်</span>
                                </div>
                                <div className="flex gap-2">
                                    <button className="px-3 py-1 rounded-lg bg-yellow-400 text-white font-semibold shadow hover:bg-yellow-500 transition" onClick={() => popUpModal(bet.num, bet.amount)}>
                                        ပြင်ဆင်မည်
                                    </button>
                                    <button className="px-3 py-1 rounded-lg bg-red-500 text-white font-semibold shadow hover:bg-red-600 transition" onClick={() => deleteBet(bet.num)}>
                                        ဖျက်မည်
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                    <div className="flex justify-between items-center mt-6 pt-4 border-t border-blue-100">
                        <span className="font-bold text-lg">စုစုပေါင်း</span>
                        <span className="font-bold text-lg">{total} ကျပ်</span>
                    </div>
                </div>
                <button
                    className="w-full max-w-md mx-auto py-4 rounded-2xl bg-gradient-to-br from-yellow-400 to-orange-500 text-white font-bold text-xl shadow-lg hover:scale-105 active:scale-95 transition-all disabled:opacity-60"
                    onClick={handleConfirmBet}
                    disabled={loading}
                >
                    {loading ? 'တင်နေသည်...' : 'အတည်ပြုမည်'}
                </button>

                {/* Edit Bet Modal */}
                <Modal show={smShow} onHide={() => setSmShow(false)} centered>
                    <Modal.Header closeButton className="!border-b-0">
                        <Modal.Title>
                            <span className="font-bold text-lg">ပြင်ဆင်မည့် ဂဏန်းနှင့် ထိုးကြေး</span>
                        </Modal.Title>
                    </Modal.Header>
                    <Modal.Body className="flex flex-col gap-4 p-6">
                        <div className="flex flex-col gap-2">
                            <label className="font-semibold">ဂဏန်း</label>
                            <input
                                type="text"
                                value={num}
                                disabled
                                className="w-full px-4 py-2 rounded-lg border border-gray-300 bg-gray-100 text-lg font-bold text-center"
                            />
                        </div>
                        <div className="flex flex-col gap-2">
                            <label className="font-semibold">ထိုးကြေး</label>
                            <input
                                type="number"
                                value={betAmount}
                                onChange={e => setBetAmount(e.target.value)}
                                className="w-full px-4 py-2 rounded-lg border border-gray-300 text-lg font-bold text-center"
                            />
                        </div>
                        <button
                            className="w-full py-3 rounded-xl bg-gradient-to-br from-blue-400 to-purple-400 text-white font-bold text-lg shadow hover:from-blue-500 hover:to-purple-500 transition"
                            onClick={() => editBet(num, betAmount)}
                        >
                            သိမ်းမည်
                        </button>
                    </Modal.Body>
                </Modal>
            </div>
        </>
    );
};

export default TwoDConfirmPage;
