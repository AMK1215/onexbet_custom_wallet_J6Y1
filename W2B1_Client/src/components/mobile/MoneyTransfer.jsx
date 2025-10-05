import React, { useContext ,useState } from 'react'
import WithDraw from './WithDraw';
import '../../assets/css/moneyTransfer.css'
import Deposit from './Deposit';
import Transfer from './Transfer';
import { LanguageContext } from "../../contexts/LanguageContext";

const MoneyTransfer = () => {
   const { content } = useContext(LanguageContext);
    const [selectedTab,setSelectedTab]=useState(1);
    const tabs=[
        {id:1,name: content?.wallet?.deposit || 'Deposit',value:''},
        {id:2,name: content?.wallet?.withdraw ||'Withdraw',value:''},
        // {id:3,name:'လွှဲပြောင်းရန်',value:''},
     ]
  return (
      <div>
          <div className="flex flex-row flex-nowrap overflow-x-auto items-center justify-center gap-2 px-2 py-2">
              {tabs.map((tab, index) => (
                  <div
                      key={index}
                      onClick={() => setSelectedTab(tab.id)}
                      className={`cursor-pointer px-4 py-1.5 sm:px-5 sm:py-2 rounded-lg whitespace-nowrap font-semibold text-sm sm:text-base flex-shrink-0 transition-all duration-200 ${
                          tab.id === selectedTab
                              ? 'bg-yellow-400 text-black shadow-md'
                              : 'bg-gray-100 text-gray-700 hover:bg-yellow-100'
                      }`}
                  >
                      <p className="mb-0">{tab.name}</p>
                  </div>
              ))}
          </div>
          <div className="px-sm-5 mx-sm-5 mx-auto my-4 mb-5 pb-5">
              {selectedTab===1 && <Deposit/> }
              {selectedTab===2 && <WithDraw/> }
              {selectedTab===3 && <Transfer/> }
          </div>
      </div>
  )
}

export default MoneyTransfer
