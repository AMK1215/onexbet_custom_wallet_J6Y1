import React, { useContext, useState } from 'react'
import logo from '../assets/img/logo_red.png'
import tele from '../assets/img/tele.png'
import viber from '../assets/img/viber.png'
import fb from '../assets/img/fb.png'
import line from '../assets/img/line.webp'
import { Link } from 'react-router-dom'
import { LanguageContext } from '../contexts/LanguageContext'
import useFetch from '../hooks/useFetch'
import BASE_URL from '../hooks/baseUrl'
import Marquee from '../components/mobile/Marquee'
import { FaHeadset, FaClock, FaEnvelope, FaPhone, FaWhatsapp, FaTelegram, FaFacebook, FaLine } from 'react-icons/fa'

const ContactPage = () => {
  const { content } = useContext(LanguageContext);
  const { data, loading, error } = useFetch(BASE_URL + "/contact");
  const [hoveredContact, setHoveredContact] = useState(null);

  // Contact info data
  const contactInfo = [
    {
      icon: <FaHeadset className="text-2xl text-yellow-400" />,
      title: "24/7 Support",
      description: "Round the clock customer service"
    },
    {
      icon: <FaClock className="text-2xl text-green-400" />,
      title: "Quick Response",
      description: "Get help within minutes"
    },
    {
      icon: <FaEnvelope className="text-2xl text-blue-400" />,
      title: "Multiple Channels",
      description: "Contact us through various platforms"
    }
  ];

  const getContactIcon = (name) => {
    switch(name?.toLowerCase()) {
      case 'viber':
        return <FaWhatsapp className="text-xl" />;
      case 'telegram':
        return <FaTelegram className="text-xl" />;
      case 'facebook':
        return <FaFacebook className="text-xl" />;
      case 'line':
        return <FaLine className="text-xl" />;
      default:
        return null;
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-[#101223] via-[#1a1d3a] to-[#101223] flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-16 w-16 border-b-2 border-yellow-400 mx-auto mb-4"></div>
          <p className="text-white text-lg">Loading contact information...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-[#101223] via-[#1a1d3a] to-[#101223] flex items-center justify-center">
        <div className="text-center">
          <div className="text-red-400 text-6xl mb-4">⚠️</div>
          <p className="text-white text-lg mb-4">Failed to load contact information</p>
          <button 
            onClick={() => window.location.reload()} 
            className="bg-yellow-400 hover:bg-yellow-500 text-black px-6 py-2 rounded-lg font-semibold transition-all duration-200"
          >
            Try Again
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-[#101223] via-[#1a1d3a] to-[#101223]">
      {/* Header Section */}
      <div className="relative overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-r from-yellow-400/10 to-transparent"></div>
        <div className="relative z-10">
        <Marquee />
        </div>
      </div>

      {/* Main Content */}
      <div className="max-w-6xl mx-auto px-4 py-8">
        {/* Page Header */}
        <div className="text-center mb-12">
          <h1 className="text-4xl md:text-5xl font-bold text-white mb-4 bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
            {content?.nav?.contact || 'Contact Us'}
          </h1>
          <p className="text-gray-300 text-lg max-w-2xl mx-auto">
            Get in touch with our support team. We're here to help you 24/7 with any questions or concerns.
          </p>
        </div>

        {/* Company Info Card */}
        <div className="bg-gradient-to-r from-[#181A29] to-[#1f2338] rounded-3xl p-8 mb-12 shadow-2xl border border-yellow-400/20 hover:border-yellow-400/40 transition-all duration-300">
          <div className="flex flex-col md:flex-row items-center gap-8">
            <div className="flex-shrink-0">
              <img 
                src={"1x.png"} 
                alt="Company Logo" 
                className="w-32 h-32 md:w-40 md:h-40 object-contain filter drop-shadow-lg"
              />
            </div>
            <div className="text-center md:text-left flex-1">
              <h2 className="text-2xl md:text-3xl font-bold text-white mb-4">
                Welcome to Our Platform
              </h2>
              <p className="text-gray-300 text-lg mb-6 leading-relaxed">
                Experience the best gaming platform with 24/7 customer support, 
                secure transactions, and exciting rewards. Your satisfaction is our priority.
              </p>
              <div className="flex flex-wrap gap-4 justify-center md:justify-start">
                {contactInfo.map((info, index) => (
                  <div key={index} className="flex items-center gap-2 text-gray-300">
                    {info.icon}
                    <span className="text-sm font-medium">{info.title}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>

        {/* Contact Channels Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          {data && data.map((item, index) => (
            <div
              key={index}
              className="group relative"
              onMouseEnter={() => setHoveredContact(index)}
              onMouseLeave={() => setHoveredContact(null)}
            >
              <Link 
                to={item.value} 
                target='_blank'
                className="block bg-gradient-to-br from-[#181A29] to-[#1f2338] rounded-2xl p-6 shadow-lg border border-gray-700/50 hover:border-yellow-400/50 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl"
              >
                {/* Background Glow Effect */}
                <div className={`absolute inset-0 rounded-2xl bg-gradient-to-r from-yellow-400/10 to-orange-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300`}></div>
                
                {/* Content */}
                <div className="relative z-10 text-center">
                  <div className="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    {getContactIcon(item.name) || (
                      <img 
                        src={"https://gamestar77.online/api/.."+item.image} 
                        className='w-8 h-8 object-contain' 
                        alt={item.name}
                      />
                    )}
                  </div>
                  
                  <h3 className="text-lg font-bold text-white mb-2 group-hover:text-yellow-400 transition-colors duration-300">
                    {item.name}
                  </h3>
                  
                  <p className="text-gray-400 text-sm">
                    Click to connect
                  </p>
                  
                  {/* Hover Indicator */}
                  <div className="mt-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div className="w-8 h-1 bg-gradient-to-r from-yellow-400 to-orange-500 mx-auto rounded-full"></div>
                  </div>
                </div>
              </Link>
            </div>
          ))}
        </div>

        {/* Additional Info Section */}
        <div className="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
          <div className="bg-gradient-to-br from-[#181A29] to-[#1f2338] rounded-2xl p-6 text-center border border-gray-700/50 hover:border-yellow-400/30 transition-all duration-300">
            <div className="w-12 h-12 bg-yellow-400/20 rounded-full flex items-center justify-center mx-auto mb-4">
              <FaClock className="text-yellow-400 text-xl" />
            </div>
            <h3 className="text-white font-semibold mb-2">24/7 Availability</h3>
            <p className="text-gray-400 text-sm">Our support team is available round the clock to assist you</p>
          </div>
          
          <div className="bg-gradient-to-br from-[#181A29] to-[#1f2338] rounded-2xl p-6 text-center border border-gray-700/50 hover:border-yellow-400/30 transition-all duration-300">
            <div className="w-12 h-12 bg-green-400/20 rounded-full flex items-center justify-center mx-auto mb-4">
              <FaHeadset className="text-green-400 text-xl" />
            </div>
            <h3 className="text-white font-semibold mb-2">Expert Support</h3>
            <p className="text-gray-400 text-sm">Get help from our experienced customer service team</p>
          </div>
          
          <div className="bg-gradient-to-br from-[#181A29] to-[#1f2338] rounded-2xl p-6 text-center border border-gray-700/50 hover:border-yellow-400/30 transition-all duration-300">
            <div className="w-12 h-12 bg-blue-400/20 rounded-full flex items-center justify-center mx-auto mb-4">
              <FaEnvelope className="text-blue-400 text-xl" />
            </div>
            <h3 className="text-white font-semibold mb-2">Multiple Channels</h3>
            <p className="text-gray-400 text-sm">Contact us through various platforms for your convenience</p>
          </div>
        </div>
      </div>
    </div>
  )
}

export default ContactPage
