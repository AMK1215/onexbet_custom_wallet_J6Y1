import React, { useState } from "react";
import { Outlet } from "react-router-dom";
import Navbar from "./mobile/Navbar";
import Footer from "./mobile/Footer";
import BottomMenu from "./mobile/BottomMenu";
import { Modal } from "react-bootstrap";
import { CgClose } from "react-icons/cg";
import popUp from "../assets/img/popupImg.png";
import useFetch from "../hooks/useFetch";
import BASE_URL from "../hooks/baseURL";
import SidebarLg from "./desktop/SidebarLg";
import Marquee from './mobile/Marquee'
import Carousel from './mobile/Carousel'
import { Toaster } from "react-hot-toast";

const UnAuthLayout = () => {
  
  const [isModalOpen, setIsModalOpen] = useState(false);
  return (
    <div>
      <Toaster/>
      <Navbar />
      {/* Mobile */}
      {/* <div className="mobileScreenContainer">
        <Outlet />
      </div> */}
      <div > 
      <div className="row " style={{minHeight:'100vh',overflowY:'scroll',height:'max-content'}}>
        <div className="col-3 sidebarLg app-gradient">
          <SidebarLg/>
        </div>
        <div className="col-9 px-0">
          <Outlet/>
        </div>
      </div>
      </div>
      <BottomMenu />
    </div>
  );
};

export default UnAuthLayout;
