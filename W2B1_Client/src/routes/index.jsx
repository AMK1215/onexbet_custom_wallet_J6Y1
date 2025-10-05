import { createBrowserRouter } from "react-router-dom";
 import HomePage from "../pages/Home";
// import LoginPage from "../pages/Login";
// import RegisterPage from "../pages/Register";
import GamesPage from "../pages/Games";
import Promotion from "../pages/Promotion";
import RankingPage from "../pages/Ranking";
import ContactPage from "../pages/Contact";
import InformationPage from "../pages/Information";
import AboutPage from "../pages/About";
import DemoPlayPage from "../pages/DemoPlay";
import Layout from "../components/Layout";
import ReelsPage from "../pages/ReelsPage";
import AdsVideo from "../pages/AdsVideo";
import DigitBetGame from "../components/desktop/Digits/DigitBetGame";
import ShanGame from "../components/desktop/Shan/ShanGame";
import TwoDPage from "../pages/TwoDPage";
import TwoDBetPage from "../pages/TwoDBetPage";
import TwoDConfirmPage from "../pages/TwoDConfirmPage";
import WalletPage from "../pages/Wallet";
import InternalTransfer from "../pages/Slot/InternalTransfer";
import WalletHistoryPage from "../pages/Slot/WalletHistory";
import TransactionsPage from "../pages/Slot/Transaction";
import ExchangeTranLog from "../components/desktop/ExchangeTranLog";
import MorningBetSlipDisplay from "../components/MorningBetSlipDisplay";
import EveningBtSlipDisplay from "../components/EveningBtSlipDisplay";
import TwoDDailyWinner from "../components/TwoDDailyWinner";


export const router = createBrowserRouter([
    {
      path: '/',
      element: <Layout />,
      children: [
        {
          index: true,
          element: <HomePage />
        },
        {
          path: '/reels',
          element: <ReelsPage />
        },
        {
          path: '/games',
          element: <GamesPage />
        },
        {
          path: '/demo-play',
          element: <DemoPlayPage />
        },
        {
          path: '/ranking',
          element: <RankingPage />
        },
        {
          path: '/promotion',
          element: <Promotion />
        },
        {
          path: '/contact',
          element: <ContactPage />
        },
        {
          path: '/information',
          element: <InformationPage />
        },
        {
          path: '/about',
          element: <AboutPage />
        },
        {
          path: '/ads-video',
          element: <AdsVideo />
        },
        {
          path: '/digitbet',
          element: <DigitBetGame />
        },
        {
          path: '/shan',
          element: <ShanGame />
        },

        {
          path: '/2d',
          element: <TwoDPage />
        },
        {
          path: '/2d/bet',
          element: <TwoDBetPage />
        },
        {
          path: '/2d/confirm',
          element: <TwoDConfirmPage />
        }, 
        {
          path: '/morning-bet-slip',
          element: <MorningBetSlipDisplay />
        },
        {
          path: '/evening-bet-slip',
          element: <EveningBtSlipDisplay />
        },
        {
          path: '/2d/daily-winner',
          element: <TwoDDailyWinner />
        },
        {
          path : '/wallet',
          element : <WalletPage />
      },
      {
          path: '/wallet/internal-transfer',
          element: <InternalTransfer />
      },
      {
          path:'/wallet-history',
          element:<WalletHistoryPage/>
      },
      {
        path:'/transactions',
        element:<TransactionsPage/>
      },
      {
        path: '/exchange-log',
        element: <ExchangeTranLog />
      }
      ]
    },
    // {
    //   path: '/login',
    //   element: <LoginPage />
    // },
    // {
    //   path: '/register',
    //   element: <RegisterPage />
    // },
  ]);