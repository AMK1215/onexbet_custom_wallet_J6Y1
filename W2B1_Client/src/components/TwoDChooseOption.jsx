import React, { useState } from "react";
import maze from "../assets/img/maze.png";
import flash from "../assets/img/flash.png";
import repeat from "../assets/img/repeat.png";
import dream from "../assets/img/dream.png";
import { Button, Modal, Offcanvas } from "react-bootstrap";
import "../assets/css/chooseNumber.css";

const TwoDChooseOption = ({pathee, frontNumber, backNumber, powerNumber, twentyNumbers, permunated, breakGroup, singleDouble, addDigits}) => {
  const [isOp1Show, setIsOp1Show] = useState(false);
  const [isOp2Show, setIsOp2Show] = useState(false);
  const [isOp3Show, setIsOp3Show] = useState(false);
  const [isOp4Show, setIsOp4Show] = useState(false);

  const breakNums = [
    {"id" : 1, "group" : "0/10"},
    {"id" : 2, "group" : "1/11"},
    {"id" : 3, "group" : "2/12"},
    {"id" : 4, "group" : "3/13"},
    {"id" : 5, "group" : "4/14"},
    {"id" : 6, "group" : "5/15"},
    {"id" : 7, "group" : "6/16"},
    {"id" : 8, "group" : "7/17"},
    {"id" : 9, "group" : "8/18"},
    {"id" : 10, "group" : "9/19"},
  ];
  const sizes = [
    { id: 1, group: "ညီအစ်ကို" },
    { id: 2, group: "ကြီး" },
    { id: 3, group: "ငယ်" },
    { id: 4, group: "မ" },
    { id: 5, group: "စုံ" },
    { id: 6, group: "စုံစုံ" },
    { id: 7, group: "စုံမ" },
    { id: 8, group: "အပူး" },
    // Head groups (0-ထိပ် to 9-ထိပ်)
    ...Array.from({ length: 10 }, (_, i) => ({ id: 9 + i, group: `${i}-ထိပ်` })),
    // Tail groups (0-နောက် to 9-နောက်)
    ...Array.from({ length: 10 }, (_, i) => ({ id: 19 + i, group: `${i}-နောက်` })),
  ];

  const pate = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
  const htate = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
  const naut = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
  const natkhat = [
    {id: 1, group: "မြန်မာနက္ခတ် 07, 18, 24, 35, 69"},
    {id: 2, group: "မြန်မာနက္ခတ် R 07, 18, 24, 35, 69"},
    {id: 3, group: "ပါဝါ 05, 16, 27, 38, 49"},
    {id: 4, group: "ပါဝါ R 50, 61, 72, 83, 94"},
    {id: 5, group: "ထိုင်းနက္ခတ် 07, 19, 23, 48, 56"},
    {id: 6, group: "ထိုင်းနက္ခတ် R 70, 91, 32, 84, 65"},
    {id: 7, group: "ထိုင်းပါဝါ 09, 13, 26, 47, 58"},
    {id: 8, group: "ထိုင်းပါဝါ R 90, 31, 62, 74, 85"},
  ];
  const kwat20 = ["00-19", "20-39", "40-59", "60-79", "80-99"];

  // Mapping from break group id to actual digit sets
  const breakGroupDigits = {
    1: ["01", "10", "29", "38", "47", "56"],
    2: ["11", "20", "39", "48", "57", "66"],
    3: ["21", "30", "49", "58", "67"],
    4: ["31", "40", "22", "59", "68", "77"],
    5: ["41", "50", "69", "78", "32"],
    6: ["51", "60", "79", "33", "88"],
    7: ["61", "70", "89", "43", "98"],
    8: ["71", "80", "99", "53", "44"],
    9: ["81", "90", "18", "27", "36"],
    10: ["91", "28", "37", "46", "55"]
  };

  // Helper to generate head and tail digit sets
  const headDigits = (x) => Array.from({ length: 10 }, (_, i) => `${x}${i}`.padStart(2, '0'));
  const tailDigits = (x) => Array.from({ length: 10 }, (_, i) => `${i}${x}`.padStart(2, '0'));

  // Corrected singleDoubleDigits mapping
  const singleDoubleDigits = {
    1: ["01","12","23","34","45","56","67","78","89","90"], // ညီအစ်ကို
    2: [ "55","56","57","58","59","65","66","67","68","69","75","76","77","78","79","85","86","87","88","89","95","96","97","98","99"], // ကြီး
    3: ["00","01","02","03","04","10","11","12","13","14","20","21","22","23","24","30","31","32","33","34","40","41","42","43","44"], // ငယ်
    4: ["11","13","15","17","19","31","33","35","37","39","51","53","55","57","59","71","73","75","77","79","91","93","95","97","99"], // မ
    5: ["00","02","04","06","08","20","22","24","26","28","40","42","44","46","48","60","62","64","66","68","80","82","84","86","88"], // စုံ
    6: ["01","12","23","34","45","56","67","78","89","90","10","21","32","43","54","65","76","87","98","09","30","41","52","63","74"], // စုံစုံ
    7: ["00","11","22","33","44","55","66","77","88","99"], // စုံမ
    8: ["00","11","22","33","44","55","66","77","88","99"], // အပူး
    // Head groups (0-ထိပ် to 9-ထိပ်)
    ...Object.fromEntries(Array.from({ length: 10 }, (_, i) => [9 + i, headDigits(i)])),
    // Tail groups (0-နောက် to 9-နောက်)
    ...Object.fromEntries(Array.from({ length: 10 }, (_, i) => [19 + i, tailDigits(i)])),
  };


  return (
    <div className="flex justify-around items-center px-3 py-3 mb-3 gap-4">
      {/* <div className="text-center" onClick={() => setIsOp1Show(!isOp1Show)}>
        <img
          src={maze}
          className="w-8 h-8 mb-2"
        />
        <div>
          <span className="text-xs">အခွေ</span>
        </div>
      </div> */}
      <div className="flex flex-col items-center cursor-pointer" onClick={() => setIsOp2Show(!isOp2Show)}>
        <img
          src={flash}
          className="w-8 h-8 mb-2"
        />
        <span className="text-xs">အမြန်ရွေး</span>
      </div>

      <div className="flex flex-col items-center cursor-pointer" onClick={() => permunated()}>
        <img
          src={repeat}
          className="w-8 h-8 mb-2"
        />
        <span className="text-xs">ပတ်လည်</span>
      </div>
      {/* <div className="flex flex-col items-center cursor-pointer" onClick={() => setIsOp4Show(!isOp4Show)}>
        <img
          src={dream}
          className="w-8 h-8 mb-2"
        />
        <span className="text-xs">အိပ်မက်</span>
      </div> */}
      {/* အခွေ */}
      <Modal
        show={isOp1Show}
        onHide={() => setIsOp1Show(false)}
        centered
      >
        <Modal.Header closeButton className="!border-b-0">
          <Modal.Title>
            <span className="font-semibold text-base">ခွေမည့်ဂဏန်းများ ရိုက်ထည့်ပါ</span>
          </Modal.Title>
        </Modal.Header>
        <Modal.Body className="flex flex-col gap-4 p-6">
          <span className="text-center font-bold text-xs">
            တူညီသော ဂဏန်း(၂)လုံး ခွင့်မပြုပါ။ (ဥပမာ - 020, 131,...)
          </span>
          <input
            className="w-full border-2 rounded-lg px-3 py-2 text-base"
            placeholder="ဂဏန်း ၂လုံးမှ ၁၀လုံးအထိ"
          />
        </Modal.Body>
        <Modal.Footer className="flex justify-end gap-3 p-4">
          <button
            className="px-4 py-2 rounded-lg bg-gray-400 text-white font-semibold shadow hover:bg-gray-500 transition"
            onClick={() => setIsOp1Show(false)}
          >
            ဖျက်မည်
          </button>
          <button
            className="px-4 py-2 rounded-lg bg-pink-300 text-black font-semibold shadow hover:bg-pink-400 transition"
            onClick={() => setIsOp1Show(false)}
          >
            အိုကေ
          </button>
        </Modal.Footer>
      </Modal>
      
      <Offcanvas
        placement="top"
        show={isOp2Show}
        onHide={() => setIsOp2Show(false)}
        className="!bg-white"
      >
        <Offcanvas.Header closeButton className="!border-b-0">
          <Offcanvas.Title className="font-bold text-lg">အမြန်ရွေး</Offcanvas.Title>
        </Offcanvas.Header>
        <Offcanvas.Body className="p-4 space-y-6 max-h-[70vh] overflow-y-auto">
          <div>
            <p className="text-blue-700 font-bold mb-2">ဘရိတ်</p>
            <div className="flex flex-wrap gap-2 mb-4">
              {breakNums.map((num, index) => {
                return (
                  <button
                    key={index}
                    className="px-4 py-2 rounded-lg bg-blue-100 text-blue-700 font-semibold shadow hover:bg-blue-200 transition"
                    onClick={() => { breakGroup(num.id); addDigits(breakGroupDigits[num.id] || []); setIsOp2Show(false); }}
                  >
                    {num.group}
                  </button>
                );
              })}
            </div>
          </div>
          <div>
            <p className="text-blue-700 font-bold mb-2">Single & Double Size</p>
            <div className="flex overflow-x-auto flex-nowrap gap-x-2 pb-2 mb-4">
              {sizes.map((size, index) => (
                <button
                  key={index}
                  className="px-4 py-2 rounded-lg bg-purple-100 text-purple-700 font-semibold shadow hover:bg-purple-200 transition shrink-0"
                  onClick={() => { singleDouble(size.id); addDigits(singleDoubleDigits[size.id] || []); setIsOp2Show(false); }}
                >
                  {size.group}
                </button>
              ))}
            </div>
          </div>
          <div>
            <p className="text-blue-700 font-bold mb-2">ပတ်သီး</p>
            <div className="flex flex-wrap gap-2 mb-4">
              {pate.map((n) => (
                <button
                  key={n}
                  className="px-4 py-2 rounded-lg bg-green-100 text-green-700 font-semibold shadow hover:bg-green-200 transition"
                  onClick={() => { pathee(n); addDigits([n.padStart(2, '0')]); setIsOp2Show(false); }}
                >
                  {n}
                </button>
              ))}
            </div>
          </div>
          <div>
            <p className="text-blue-700 font-bold mb-2">ထိပ်</p>
            <div className="flex flex-wrap gap-2 mb-4">
              {htate.map((n) => (
                <button
                  key={n}
                  className="px-4 py-2 rounded-lg bg-yellow-100 text-yellow-700 font-semibold shadow hover:bg-yellow-200 transition"
                  onClick={() => { frontNumber(n); addDigits([n.padStart(2, '0')]); setIsOp2Show(false); }}
                >
                  {n}
                </button>
              ))}
            </div>
          </div>
          <div>
            <p className="text-blue-700 font-bold mb-2">နောက်</p>
            <div className="flex flex-wrap gap-2 mb-4">
              {naut.map((n) => (
                <button
                  key={n}
                  className="px-4 py-2 rounded-lg bg-pink-100 text-pink-700 font-semibold shadow hover:bg-pink-200 transition"
                  onClick={() => { backNumber(n); addDigits([n.padStart(2, '0')]); setIsOp2Show(false); }}
                >
                  {n}
                </button>
              ))}
            </div>
          </div>
          <div>
            <p className="text-blue-700 font-bold mb-2">ပါဝါ</p>
            <div className="flex flex-wrap gap-2 mb-4">
              {Array.isArray(powerNumber) && powerNumber.map((n) => (
                <button
                  key={n}
                  className="px-4 py-2 rounded-lg bg-red-100 text-red-700 font-semibold shadow hover:bg-red-200 transition"
                  onClick={() => [powerNumber(n), setIsOp2Show(false)]}
                >
                  {n}
                </button>
              ))}
            </div>
          </div>
          <div>
            <p className="text-blue-700 font-bold mb-2">20 ဂဏန်း</p>
            <div className="flex flex-wrap gap-2 mb-4">
              {kwat20.map((n) => (
                <button
                  key={n}
                  className="px-4 py-2 rounded-lg bg-indigo-100 text-indigo-700 font-semibold shadow hover:bg-indigo-200 transition"
                  onClick={() => [twentyNumbers(n), setIsOp2Show(false)]}
                >
                  {n}
                </button>
              ))}
            </div>
          </div>
        </Offcanvas.Body>
      </Offcanvas>
    </div>
  );
};

export default TwoDChooseOption;
