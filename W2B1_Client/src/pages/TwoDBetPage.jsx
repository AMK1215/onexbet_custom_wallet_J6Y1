// TwoDBetPage.jsx
import React, { useCallback, useEffect, useState } from "react";
// Assuming these components exist and handle their own internal Tailwind styling
import TwoDChooseOption from "../components/TwoDChooseOption";
import UserWallet from "../components/UserWallet";
// React-Bootstrap Modal is kept as a component for simplicity, but its styling is overwritten by Tailwind.
import { Modal } from "react-bootstrap";
// Static assets (images)
import info from "../assets/img/info.png";
import info2 from "../assets/img/info2.png";
// The 'clock' image and custom CSS are no longer used/needed
// import clock from "../assets/img/clock.png";
// import "../assets/css/chooseNumber.css";
import { useNavigate, useSearchParams } from "react-router-dom";
// React Toastify for UI feedback (requires installation and setup in your project)
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css'; // Toastify's default CSS
// Import custom CSS
import "../assets/css/twoD.css";

const TwoDBetPage = () => {
    // Removed AuthContext and authentication logic as per "UI only" requirement
    // const { auth } = useContext(AuthContext);
    const [param] = useSearchParams();
    const session = param.get("session");

    // Static user data, replacing API call for user wallet
    const user = { balance: 1234567, name: "John Doe" };
    
    const [amount, setAmount] = useState(100);
    const [inputs, setInputs] = useState([]);
    const navigate = useNavigate();

    // Removed authentication check useEffect as API is removed.
    // useEffect(() => {
    //   if (!auth) {
    //     navigate('/login');
    //   }
    // }, [auth, navigate]);

    // Static data for 2D numbers and their limits, replacing API calls
    const two_digits_static = Array.from({ length: 100 }, (_, i) => ({
        two_digit: i.toString().padStart(2, '0'),
        // Simulate varying remaining amounts for visual percentage bars
        over_all_remaining: Math.floor(Math.random() * 800) + 200, 
    }));
    const numbers_static = {
        two_digits: two_digits_static,
        default_break: 1000 // A default break amount for percentage calculation
    };

    // Add state for selected numbers (UI only)
    const [selectedDigits, setSelectedDigits] = useState([]);

    // Handler for selecting/deselecting a digit (UI only)
    const toggleDigit = (digit) => {
        setSelectedDigits((prev) =>
            prev.includes(digit)
                ? prev.filter((d) => d !== digit)
                : [...prev, digit]
        );
    };

    // Permutated numbers logic: generates a list including reversed numbers
    const permunated = () => {
        setSelectedDigits(prev => {
            const newDigits = new Set(prev);
            prev.forEach(num => {
                const reversed = num.split('').reverse().join('');
                newDigits.add(reversed);
            });
            return Array.from(newDigits);
        });
        toast.success('ပတ်လည် ဂဏန်းထည့်ပြီးပါပြီ။', {
            position: "top-right",
            autoClose: 2000,
            hideProgressBar: false,
            closeOnClick: true,
            pauseOnHover: true,
            draggable: true,
            progress: undefined,
            theme: "dark",
        });
    };

    // Pathee numbers logic: selects numbers based on a 'pathee' pattern
    const pathee = (id) => {
        let digits = [];
        if (id === 0) {
            digits = ["00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "20", "30", "40", "50", "60", "70", "80", "90"];
        } else if (id === 1) {
            digits = ["01", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "21", "31", "41", "51", "61", "71", "81", "91"];
        } else if (id === 2) {
            digits = ["02", "20", "22", "23", "24", "25", "26", "27", "28", "29", "32", "42", "52", "62", "72", "82", "92"];
        } else if (id === 3) {
            digits = ["03", "30", "33", "34", "35", "36", "37", "38", "39", "43", "53", "63", "73", "83", "93"];
        } else if (id === 4) {
            digits = ["04", "40", "44", "45", "46", "47", "48", "49", "54", "64", "74", "84", "94"];
        } else if (id === 5) {
            digits = ["05", "50", "55", "56", "57", "58", "59", "65", "75", "85", "95"];
        } else if (id === 6) {
            digits = ["06", "60", "66", "67", "68", "69", "76", "86", "96"];
        } else if (id === 7) {
            digits = ["07", "70", "77", "78", "79", "87", "97"];
        } else if (id === 8) {
            digits = ["08", "80", "88", "89", "98", "99"];
        } else if (id === 9) {
            digits = ["09", "90", "99"];
        }
        setInputs(digits.map((num) => ({ num, amount: amount })));
    };

    // Front number logic: selects numbers starting with a specific digit
    const frontNumber = (id) => {
        let digits = [];
        if (id === 0) {
            digits = ["00", "01", "02", "03", "04", "05", "06", "07", "08", "09"];
        } else if (id === 1) {
            digits = ["10", "11", "12", "13", "14", "15", "16", "17", "18", "19"];
        } else if (id === 2) {
            digits = ["20", "21", "22", "23", "24", "25", "26", "27", "28", "29"];
        } else if (id === 3) {
            digits = ["30", "31", "32", "33", "34", "35", "36", "37", "38", "39"];
        } else if (id === 4) {
            digits = ["40", "41", "42", "43", "44", "45", "46", "47", "48", "49"];
        } else if (id === 5) {
            digits = ["50", "51", "52", "53", "54", "55", "56", "57", "58", "59"];
        } else if (id === 6) {
            digits = ["60", "61", "62", "63", "64", "65", "66", "67", "68", "69"];
        } else if (id === 7) {
            digits = ["70", "71", "72", "73", "74", "75", "76", "77", "78", "79"];
        } else if (id === 8) {
            digits = ["80", "81", "82", "83", "84", "85", "86", "87", "88", "89"];
        } else if (id === 9) {
            digits = ["90", "91", "92", "93", "94", "95", "96", "97", "98", "99"];
        }
        setInputs(digits.map((num) => ({ num, amount: amount })));
    };

    // Back number logic: selects numbers ending with a specific digit
    const backNumber = (id) => {
        let digits = [];
        if (id === 0) {
            digits = ["00", "10", "20", "30", "40", "50", "60", "70", "80", "90"];
        } else if (id === 1) {
            digits = ["01", "11", "21", "31", "41", "51", "61", "71", "81", "91"];
        } else if (id === 2) {
            digits = ["02", "12", "22", "32", "42", "52", "62", "72", "82", "92"];
        } else if (id === 3) {
            digits = ["03", "13", "23", "33", "43", "53", "63", "73", "83", "93"];
        } else if (id === 4) {
            digits = ["04", "14", "24", "34", "44", "54", "64", "74", "84", "94"];
        } else if (id === 5) {
            digits = ["05", "15", "25", "35", "45", "55", "65", "75", "85", "95"];
        } else if (id === 6) {
            digits = ["06", "16", "26", "36", "46", "56", "66", "76", "86", "96"];
        } else if (id === 7) {
            digits = ["07", "17", "27", "37", "47", "57", "67", "77", "87", "97"];
        } else if (id === 8) {
            digits = ["08", "18", "28", "38", "48", "58", "68", "78", "88", "98"];
        } else if (id === 9) {
            digits = ["09", "19", "29", "39", "49", "59", "69", "79", "89", "99"];
        }
        setInputs(digits.map((num) => ({ num, amount: amount })));
    };

    // Power number logic
    const powerNumber = (id) => {
        let digits = [];
        if (id === 1) {
            digits = ["07", "18", "24", "35", "69"];
        } else if (id === 2) {
            digits = ["07", "18", "24", "35", "69", "70", "81", "42", "53", "96"];
        } else if (id === 3) {
            digits = ["05", "16", "27", "38", "49"];
        } else if (id === 4) {
            digits = ["05", "16", "27", "38", "49", "50", "61", "72", "83", "94"];
        } else if (id === 5) {
            digits = ["07", "19", "23", "48", "56"];
        } else if (id === 6) {
            digits = ["07", "19", "23", "48", "56", "70", "91", "32", "84", "65"];
        } else if (id === 7) {
            digits = ["09", "13", "26", "47", "58"];
        } else if (id === 8) {
            digits = ["09", "13", "26", "47", "58", "90", "31", "62", "74", "85"];
        }
        setInputs(digits.map((num) => ({ num, amount: amount })));
    };

    // Twenty numbers logic: selects numbers in ranges of twenty
    const twentyNumbers = (id) => {
        let digits = [];
        if (id === 0) {
            digits = ["00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19"];
        } else if (id === 1) {
            digits = ["20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39"];
        } else if (id === 2) {
            digits = ["40", "41", "42", "43", "44", "45", "46", "47", "48", "49", "50", "51", "52", "53", "54", "55", "56", "57", "58", "59"];
        } else if (id === 3) {
            digits = ["60", "61", "62", "63", "64", "65", "66", "67", "68", "69", "70", "71", "72", "73", "74", "75", "76", "77", "78", "79"];
        } else if (id === 4) {
            digits = ["80", "81", "82", "83", "84", "85", "86", "87", "88", "89", "90", "91", "92", "93", "94", "95", "96", "97", "98", "99"];
        }
        setInputs(digits.map((num) => ({ num, amount: amount })));
    };

    // Break numbers logic: selects numbers based on their sum of digits
    const breakNums = (id) => {
        id === 1 && setInputs([
            { num: "00", amount: amount }, { num: "19", amount: amount },
            { num: "28", amount: amount }, { num: "37", amount: amount },
            { num: "46", amount: amount }, { num: "55", amount: amount },
            { num: "64", amount: amount }, { num: "73", amount: amount },
            { num: "82", amount: amount }, { num: "91", amount: amount },
        ]);
        id === 2 && setInputs([
            { num: "01", amount: amount }, { num: "10", amount: amount },
            { num: "29", amount: amount }, { num: "38", amount: amount },
            { num: "47", amount: amount }, { num: "56", amount: amount },
            { num: "65", amount: amount }, { num: "74", amount: amount },
            { num: "83", amount: amount }, { num: "92", amount: amount },
        ]);
        id === 3 && setInputs([
            { num: "02", amount: amount }, { num: "20", amount: amount },
            { num: "39", amount: amount }, { num: "48", amount: amount },
            { num: "57", amount: amount }, { num: "66", amount: amount },
            { num: "75", amount: amount }, { num: "84", amount: amount },
            { num: "93", amount: amount },
        ]);
        id === 4 && setInputs([
            { num: "03", amount: amount }, { num: "30", amount: amount },
            { num: "49", amount: amount }, { num: "58", amount: amount },
            { num: "67", amount: amount }, { num: "76", amount: amount },
            { num: "85", amount: amount }, { num: "94", amount: amount },
        ]);
        id === 5 && setInputs([
            { num: "04", amount: amount }, { num: "40", amount: amount },
            { num: "59", amount: amount }, { num: "68", amount: amount },
            { num: "77", amount: amount }, { num: "86", amount: amount },
            { num: "95", amount: amount },
        ]);
        id === 6 && setInputs([
            { num: "05", amount: amount }, { num: "50", amount: amount },
            { num: "69", amount: amount }, { num: "78", amount: amount },
            { num: "87", amount: amount }, { num: "96", amount: amount },
        ]);
        id === 7 && setInputs([
            { num: "06", amount: amount }, { num: "60", amount: amount },
            { num: "79", amount: amount }, { num: "88", amount: amount },
            { num: "97", amount: amount },
        ]);
        id === 8 && setInputs([
            { num: "07", amount: amount }, { num: "70", amount: amount },
            { num: "89", amount: amount }, { num: "98", amount: amount },
        ]);
        id === 9 && setInputs([
            { num: "08", amount: amount }, { num: "80", amount: amount },
            { num: "99", amount: amount },
        ]);
        id === 10 && setInputs([
            { num: "09", amount: amount }, { num: "90", amount: amount },
        ]);
    };

    // Single & Double logic: handles various number categories like brothers, big/small, odd/even etc.
    const singleDouble = (id) => {
        // Brothers
        id === 1 && setInputs([
            { num: "01", amount: amount }, { num: "10", amount: amount },
            { num: "12", amount: amount }, { num: "21", amount: amount },
            { num: "23", amount: amount }, { num: "32", amount: amount },
            { num: "34", amount: amount }, { num: "43", amount: amount },
            { num: "45", amount: amount }, { num: "54", amount: amount },
            { num: "56", amount: amount }, { num: "65", amount: amount },
            { num: "67", amount: amount }, { num: "76", amount: amount },
            { num: "78", amount: amount }, { num: "87", amount: amount },
            { num: "89", amount: amount }, { num: "98", amount: amount },
        ]);
        // Big numbers (50-99)
        id === 2 && setInputs(
            Array.from({ length: 50 }, (_, i) => ({
                num: (i + 50).toString(),
                amount: amount,
            }))
        );
        // Small numbers (00-49)
        id === 3 && setInputs(
            Array.from({ length: 50 }, (_, i) => {
                const num = 49 - i;
                const numStr = num < 10 ? `0${num}` : `${num}`;
                return { num: numStr, amount: amount };
            })
        );
        // Odd numbers
        id === 4 && setInputs(
            Array.from({ length: 100 }, (_, i) => i)
                .filter(number => number % 2 !== 0)
                .map(number => ({
                    num: number.toString().padStart(2, '0'),
                    amount: amount
                }))
        );
        // Even numbers
        id === 5 && setInputs(
            Array.from({ length: 100 }, (_, i) => i)
                .filter(number => number % 2 === 0)
                .map(number => ({
                    num: number.toString().padStart(2, '0'),
                    amount: amount
                }))
        );
        // Even+Even numbers
        if (id === 6) {
            const evenRepeatedNumbers = [];
            for (let ones = 0; ones <= 8; ones += 2) {
                const number = ones.toString().padStart(2, '0');
                evenRepeatedNumbers.push({
                    num: number,
                    amount: amount
                });
            }
            for (let tens = 2; tens <= 9; tens += 2) {
                for (let ones = 0; ones <= 9; ones += 2) {
                    const number = tens * 10 + ones;
                    evenRepeatedNumbers.push({
                        num: number.toString().padStart(2, '0'),
                        amount: amount
                    });
                }
            }
            setInputs(evenRepeatedNumbers);
        }
        // Even+Odd numbers
        if (id === 7) {
            const evenOddNumbers = [];
            for (let tens = 0; tens <= 9; tens += 2) {
                for (let ones = 1; ones <= 9; ones += 2) {
                    const number = tens * 10 + ones;
                    evenOddNumbers.push({
                        num: number.toString().padStart(2, '0'),
                        amount: amount
                    });
                }
            }
            setInputs(evenOddNumbers);
        }
        // Odd+Even numbers
        if (id === 8) {
            const oddEvenNumbers = [];
            for (let tens = 1; tens <= 9; tens += 2) {
                for (let ones = 0; ones <= 9; ones += 2) {
                    const number = tens * 10 + ones;
                    oddEvenNumbers.push({
                        num: number.toString().padStart(2, '0'),
                        amount: amount
                    });
                }
            }
            setInputs(oddEvenNumbers);
        }
        // Odd+Odd numbers
        if (id === 9) {
            const oddRepeatedNumbers = [];
            for (let ones = 1; ones <= 9; ones += 2) {
                const number = (10 + ones).toString().padStart(2, '0');
                oddRepeatedNumbers.push({
                    num: number,
                    amount: amount
                });
            }
            for (let tens = 3; tens <= 9; tens += 2) {
                for (let ones = 1; ones <= 9; ones += 2) {
                    const number = tens * 10 + ones;
                    oddRepeatedNumbers.push({
                        num: number.toString().padStart(2, '0'),
                        amount: amount
                    });
                }
            }
            setInputs(oddRepeatedNumbers);
        }
        // Double numbers (00, 11, 22, ..., 99)
        id === 10 && setInputs([
            { "num": "00", "amount": amount }, { "num": "11", "amount": amount },
            { "num": "22", "amount": amount }, { "num": "33", "amount": amount },
            { "num": "44", "amount": amount }, { "num": "55", "amount": amount },
            { "num": "66", "amount": amount }, { "num": "77", "amount": amount },
            { "num": "88", "amount": amount }, { "num": "99", "amount": amount }
        ]);
    };

    // Helper function to calculate percentage for progress bar
    const percentage = (remain, limit) => {
        return (remain / limit) * 100;
    };

    // Add or remove a number from the selected inputs
    const addNumber = useCallback((num) => {
        setInputs(inputs => {
            const exists = inputs.some(input => input.num === num);
            if (exists) {
                return inputs.filter(input => input.num !== num);
            } else {
                return [...inputs, { num, amount }];
            }
        });
    }, [amount]);

    // Update amounts for all selected numbers when the global amount changes
    useEffect(() => {
        setInputs(inputs => inputs.map(input => ({ ...input, amount: Number(amount) })));
    }, [amount]);

    // Remove all selected numbers
    const remove = (e) => {
        e.preventDefault();
        setInputs([]);
    };

    // Handle the "Bet" action, saving data to local storage and navigating
    const bet = (e) => {
        e.preventDefault();
        let betData = {
            totalAmount: Number(amount) * inputs.length,
            amounts: inputs
        };
        localStorage.setItem("bets", JSON.stringify(betData));
        navigate('/2d/confirm');
    };

    // UI data for time limits and countdown
    const times = [
        { id: 1, time: "10:30 AM" },
        { id: 2, time: "12:00 PM" },
        { id: 3, time: "02:30 PM" },
        { id: 4, time: "04:30 PM" },
    ];
    const now = new Date();
    const targetTime = new Date();
    // Set target time based on session parameter
    if (session === "1") {
        targetTime.setHours(10);
        targetTime.setMinutes(30);
        targetTime.setSeconds(0);
        targetTime.setMilliseconds(0);
    } else if (session === "2") {
        targetTime.setHours(12);
        targetTime.setMinutes(0);
        targetTime.setSeconds(0);
        targetTime.setMilliseconds(0);
    } else if (session === "3") {
        targetTime.setHours(14);
        targetTime.setMinutes(30);
        targetTime.setSeconds(0);
        targetTime.setMilliseconds(0);
    } else if (session === "4") {
        targetTime.setHours(16);
        targetTime.setMinutes(30);
        targetTime.setSeconds(0);
        targetTime.setMilliseconds(0);
    }

    const diffInMilliseconds = targetTime - now;
    const diffInMinutes = Math.floor(diffInMilliseconds / 1000 / 60);
    const diffHours = Math.floor(diffInMinutes / 60);
    const diffMinutes = diffInMinutes % 60;

    // Determine the color of the progress bar based on percentage
    const getActiveBarColor = (percent) => {
        return percent === 100
            ? "#00CD15" // Green
            : percent >= 90
                ? "#FF0000" // Red
                : percent > 50
                    ? "#FF7A00" // Orange
                    : percent < 50 && percent > 0
                        ? "#00CD15" // Green (for lower percentages)
                        : ""; // Default if zero or invalid
    };

    // Options for the color explanation modal
    const colorOptions = [
        { id: 1, color: "#00CD15", text: "၅၀% အောက်" },
        { id: 2, color: "#FF7A00", text: "၅၀% မှ ၉၀% ကြား" },
        { id: 3, color: "#FF0000", text: "၉၀% အထက်" },
        { id: 4, color: "#AE9F9E", text: "ထိုးငွေပြည့်သွားပါပြီ" },
    ];
    const [show, setShow] = useState(false);

    const handleClose = () => setShow(false);
    const handleShow = () => setShow(true);

    // Handler to add digits from quick pick (no duplicates)
    const addDigits = (digits) => {
        setSelectedDigits(prev => Array.from(new Set([...prev, ...digits])));
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-blue-100 via-white to-blue-200 pb-28">
            <ToastContainer />
            {/* Top Action Buttons */}
            <div className="max-w-md mx-auto flex justify-between items-center pt-6 pb-2 px-6 gap-4">
                <div className="flex flex-col items-center">
                    <button className="w-14 h-14 flex items-center justify-center rounded-full bg-white/60 backdrop-blur-md shadow-lg border border-pink-200 hover:scale-105 active:scale-95 transition-all">
                        <svg width="32" height="32" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#fff"/><path d="M12 6v6l4 2" stroke="#FF2D55" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/></svg>
                    </button>
                    <span className="mt-2 text-xs font-semibold text-gray-700">အမြန်ထိုး</span>
                </div>
                <div className="flex flex-col items-center">
                    <button className="w-14 h-14 flex items-center justify-center rounded-full bg-white/60 backdrop-blur-md shadow-lg border border-pink-200 hover:scale-105 active:scale-95 transition-all">
                        <svg width="32" height="32" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#fff"/><path d="M16 12a4 4 0 1 1-4-4" stroke="#FF2D55" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/><path d="M12 8V4m0 0 2 2m-2-2-2 2" stroke="#FF2D55" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/></svg>
                    </button>
                    <span className="mt-2 text-xs font-semibold text-gray-700">ပတ်လည်</span>
                </div>
            </div>

            {/* Card Inputs (Session & Amount) */}
            <div className="w-full max-w-sm mx-auto px-2">
                <div className="bg-white/70 backdrop-blur-xl rounded-3xl shadow-2xl border border-blue-200 p-6 mb-10 flex flex-col gap-3">
                  <div className="flex justify-between items-center mb-1">
                    <span className="text-sm font-semibold text-gray-700">ပိတ်ရက်ချိန်</span>
                    <select className="px-2 py-1 rounded-xl border border-blue-200 bg-white/80 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm">
                      <option>12:00 PM</option>
                      <option>04:30 PM</option>
                    </select>
                  </div>
                  <div className="text-xs text-gray-600 mb-1">လောင်းကြေးထည့်ပါ။</div>
                  <input
                    type="number"
                    value={amount}
                    onChange={e => setAmount(Number(e.target.value))}
                    className="w-full px-4 py-2 rounded-xl border border-yellow-300 bg-white/80 text-lg text-center font-bold focus:outline-none focus:ring-2 focus:ring-yellow-400 shadow-sm"
                    placeholder="100"
                    min={1}
                  />
                </div>

                {/* Number Grid */}
                <div className="bg-white/70 backdrop-blur-xl rounded-3xl shadow-2xl border border-blue-200 mb-8 p-4 relative">
                    {/* Info icon for color codes */}
                    <button onClick={handleShow} className="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-gradient-to-br from-yellow-200 to-pink-200 shadow border border-yellow-300 hover:scale-110 transition-all">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#fff"/><path d="M12 8v4m0 4h.01" stroke="#FF7A00" strokeWidth="2" strokeLinecap="round"/></svg>
                    </button>
                    <div className="h-96 overflow-y-auto rounded-2xl border border-blue-100 bg-white/60 p-3 shadow-inner">
                        <div className="grid grid-cols-5 gap-3">
                            {numbers_static.two_digits.map((item, idx) => {
                                const percent = Math.round((item.over_all_remaining / numbers_static.default_break) * 100);
                                let barColor = "bg-yellow-400";
                                if (percent < 30) barColor = "bg-pink-400";
                                else if (percent < 60) barColor = "bg-green-400";
                                else if (percent < 90) barColor = "bg-gray-300";
                                const isSelected = selectedDigits.includes(item.two_digit);
                                return (
                                    <button
                                        key={item.two_digit}
                                        type="button"
                                        onClick={() => toggleDigit(item.two_digit)}
                                        className={`flex flex-col items-center justify-center rounded-full px-0 py-2 border-2 focus:outline-none select-none transition-all duration-200
                                            ${isSelected ? 'bg-gradient-to-br from-blue-200 to-blue-100 border-blue-500 ring-2 ring-blue-400 shadow-md scale-105' : 'bg-white/80 border-transparent hover:bg-blue-50'}
                                        `}
                                    >
                                        <span className="font-extrabold text-lg text-gray-800 drop-shadow-sm tracking-wide">{item.two_digit}</span>
                                        <div className="w-8 h-1.5 rounded-full mt-1 mb-1 overflow-hidden bg-gray-200">
                                            <div className={`h-full rounded-full transition-all duration-300 ${barColor}`} style={{ width: `${Math.min(percent, 100)}%` }}></div>
                                        </div>
                                    </button>
                                );
                            })}
                        </div>
                    </div>
                </div>

                {/* Sticky Bet Button */}
                <div className="fixed bottom-20 left-1/2 -translate-x-1/2 w-full max-w-sm px-4 z-50">
                    <button
                        className="w-full py-3 rounded-2xl bg-gradient-to-br from-yellow-400 via-orange-400 to-orange-500 text-white font-extrabold text-lg shadow-xl hover:scale-105 active:scale-95 transition-all tracking-wide drop-shadow-lg"
                        onClick={() => {
                            const betData = selectedDigits.map(num => ({ num, amount }));
                            localStorage.setItem("bets", JSON.stringify({ totalAmount: betData.reduce((sum, b) => sum + b.amount, 0), amounts: betData }));
                            navigate('/2d/confirm');
                        }}
                    >
                        ထိုးမည်
                    </button>
                </div>
            </div>

            {/* Modern Glassy Modal */}
            <Modal show={show} onHide={handleClose} centered contentClassName="!rounded-3xl !bg-white/80 !backdrop-blur-xl !shadow-2xl !border !border-blue-200">
                <Modal.Header closeButton className="!border-b-0 !rounded-t-3xl !bg-white/60">
                    <Modal.Title>
                        <span className="font-bold text-lg">အရောင်ရှင်းလင်းချက်</span>
                    </Modal.Title>
                </Modal.Header>
                <Modal.Body className="flex flex-col gap-4 p-6">
                    <span className="text-center font-bold text-xs">ထီထိုးငွေ ၁၀၀% ပြည့်ပါက ဂဏန်းပိတ်ပါမည်။</span>
                    <div className="flex flex-col gap-3 mt-2">
                        {colorOptions.map((item) => (
                            <div key={item.id} className="flex items-center gap-3">
                                <div
                                    style={{ background: item.color }}
                                    className="rounded-full w-6 h-6 border border-gray-300 shadow"
                                ></div>
                                <span className="text-sm text-gray-700 font-semibold">{item.text}</span>
                            </div>
                        ))}
                    </div>
                </Modal.Body>
                <Modal.Footer className="flex justify-end gap-3 p-4 !rounded-b-3xl !bg-white/60">
                    <button onClick={handleClose} className="px-5 py-2 rounded-xl bg-gray-400/80 text-white font-semibold shadow hover:bg-gray-500 transition-all">Close</button>
                </Modal.Footer>
            </Modal>
        </div>
    );
};

export default TwoDBetPage;
