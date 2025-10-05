import React from "react";
import { Outlet } from "react-router-dom";
import NavBar from "./desktop/NavBar";
import { Toaster } from "react-hot-toast";
import { AuthContextProvider } from "../contexts/AuthContext";
import { GeneralContextProvider } from "../contexts/GeneralContext";
import { GameContextProvider } from "../contexts/GameContext";
import BottomMenu from "../components/mobile/BottomMenu";

const Layout = () => {
  return (
    <AuthContextProvider>
      <GeneralContextProvider>
        <GameContextProvider>
          <Toaster />
          <div className="flex flex-col min-h-screen bg-[#101223]">
            <NavBar />
            <main className="flex-1 w-full max-w-6xl mx-auto px-2 sm:px-4 pt-2 pb-10">
              <Outlet />
            </main>
            <div className="fixed bottom-0 left-0 right-0 z-50 sm:hidden">
              <BottomMenu />
            </div>
          </div>
        </GameContextProvider>
      </GeneralContextProvider>
    </AuthContextProvider>
  );
};

export default Layout;
