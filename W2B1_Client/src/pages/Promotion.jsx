import React, { useContext, useState } from 'react'
import Marquee from '../components/mobile/Marquee'
import LanguageDropdown from '../components/LanguageDropdown'
import { LanguageContext } from '../contexts/LanguageContext'
import { GeneralContext } from '../contexts/GeneralContext'
import { FaGift, FaStar, FaClock, FaUsers, FaArrowRight, FaCalendarAlt } from 'react-icons/fa'

const Promotion = () => {
  const { content } = useContext(LanguageContext);
  const { promotions, loading } = useContext(GeneralContext);
  const [selectedPromotion, setSelectedPromotion] = useState(null);
  const [hoveredIndex, setHoveredIndex] = useState(null);

  // Loading state
  if (loading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-[#101223] via-[#1a1d3a] to-[#101223] flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-16 w-16 border-b-2 border-yellow-400 mx-auto mb-4"></div>
          <p className="text-white text-lg">Loading promotions...</p>
        </div>
      </div>
    );
  }

  // No promotions state
  if (!promotions || promotions.length === 0) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-[#101223] via-[#1a1d3a] to-[#101223]">
        <div className="relative overflow-hidden">
          <div className="absolute inset-0 bg-gradient-to-r from-yellow-400/10 to-transparent"></div>
          <div className="relative z-10">
            <Marquee />
          </div>
        </div>
        
        <div className="max-w-6xl mx-auto px-4 py-8">
          <div className="text-center">
            <div className="text-yellow-400 text-6xl mb-6">🎁</div>
            <h1 className="text-4xl md:text-5xl font-bold text-white mb-4 bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
              {content?.nav?.promotion || 'Promotions'}
            </h1>
            <p className="text-gray-300 text-lg max-w-2xl mx-auto">
              No promotions available at the moment. Check back soon for exciting offers and bonuses!
            </p>
          </div>
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
          <div className="flex items-center justify-center mb-4">
            <FaGift className="text-4xl text-yellow-400 mr-3" />
            <h1 className="text-4xl md:text-5xl font-bold text-white bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
              {content?.nav?.promotion || 'Promotions'}
            </h1>
          </div>
          <p className="text-gray-300 text-lg max-w-2xl mx-auto">
            Discover amazing offers, bonuses, and exclusive rewards designed just for you!
          </p>
    </div>

        {/* Promotions Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {promotions && promotions.map((item, index) => (
            <div
              key={index}
              className="group relative"
              onMouseEnter={() => setHoveredIndex(index)}
              onMouseLeave={() => setHoveredIndex(null)}
            >
              <div className="bg-gradient-to-br from-[#181A29] to-[#1f2338] rounded-3xl p-6 shadow-2xl border border-gray-700/50 hover:border-yellow-400/50 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
                {/* Background Glow Effect */}
                <div className={`absolute inset-0 rounded-3xl bg-gradient-to-r from-yellow-400/10 to-orange-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300`}></div>
                
                {/* Content */}
                <div className="relative z-10">
                  {/* Header */}
                  <div className="flex items-center justify-between mb-6">
                    <div className="flex items-center gap-3">
                      <div className="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center">
                        <FaStar className="text-white text-lg" />
                      </div>
                      <h3 className="text-xl font-bold text-white group-hover:text-yellow-400 transition-colors duration-300">
                        {item.title}
                      </h3>
                    </div>
                    <div className="flex items-center gap-2 text-gray-400">
                      <FaClock className="text-sm" />
                      <span className="text-sm">Limited Time</span>
                    </div>
                  </div>

                  {/* Image */}
                  <div className="relative mb-6 overflow-hidden rounded-2xl">
                    <img 
                      src={"https://gamestar77.online/api/.."+item.img_url} 
                      className="w-full h-64 object-cover transition-transform duration-300 group-hover:scale-110" 
                      alt={item.title}
                    />
                    {/* Overlay */}
                    <div className="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  </div>

                  {/* Description */}
                  <div className="mb-6">
                    <p className="text-gray-300 leading-relaxed text-sm md:text-base">
                      {item.description}
                    </p>
                  </div>

                  {/* Action Button */}
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4 text-sm text-gray-400">
                      <div className="flex items-center gap-1">
                        <FaUsers className="text-yellow-400" />
                        <span>Active Users</span>
                      </div>
                      <div className="flex items-center gap-1">
                        <FaCalendarAlt className="text-green-400" />
                        <span>Valid Now</span>
                      </div>
                    </div>
                    
                    <button 
                      onClick={() => setSelectedPromotion(item)}
                      className="bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-black px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center gap-2"
                    >
                      <span>Learn More</span>
                      <FaArrowRight className="text-sm" />
                    </button>
                  </div>

                  {/* Hover Indicator */}
                  <div className="mt-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div className="w-full h-1 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full"></div>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Additional Info Section */}
        <div className="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
          <div className="bg-gradient-to-br from-[#181A29] to-[#1f2338] rounded-2xl p-6 text-center border border-gray-700/50 hover:border-yellow-400/30 transition-all duration-300">
            <div className="w-12 h-12 bg-yellow-400/20 rounded-full flex items-center justify-center mx-auto mb-4">
              <FaGift className="text-yellow-400 text-xl" />
            </div>
            <h3 className="text-white font-semibold mb-2">Exclusive Offers</h3>
            <p className="text-gray-400 text-sm">Get access to special promotions and bonuses</p>
          </div>
          
          <div className="bg-gradient-to-br from-[#181A29] to-[#1f2338] rounded-2xl p-6 text-center border border-gray-700/50 hover:border-yellow-400/30 transition-all duration-300">
            <div className="w-12 h-12 bg-green-400/20 rounded-full flex items-center justify-center mx-auto mb-4">
              <FaClock className="text-green-400 text-xl" />
            </div>
            <h3 className="text-white font-semibold mb-2">Limited Time</h3>
            <p className="text-gray-400 text-sm">Don't miss out on time-sensitive deals</p>
          </div>
          
          <div className="bg-gradient-to-br from-[#181A29] to-[#1f2338] rounded-2xl p-6 text-center border border-gray-700/50 hover:border-yellow-400/30 transition-all duration-300">
            <div className="w-12 h-12 bg-blue-400/20 rounded-full flex items-center justify-center mx-auto mb-4">
              <FaUsers className="text-blue-400 text-xl" />
            </div>
            <h3 className="text-white font-semibold mb-2">For Everyone</h3>
            <p className="text-gray-400 text-sm">Promotions available for all users</p>
          </div>
        </div>
      </div>

      {/* Modal for Promotion Details */}
      {selectedPromotion && (
        <div className="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4">
          <div className="bg-gradient-to-br from-[#181A29] to-[#1f2338] rounded-3xl p-8 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-2xl font-bold text-white">{selectedPromotion.title}</h2>
              <button 
                onClick={() => setSelectedPromotion(null)}
                className="text-gray-400 hover:text-white text-2xl"
              >
                ×
              </button>
            </div>
            
            <img 
              src={"https://gamestar77.online/api/.."+selectedPromotion.img_url} 
              className="w-full h-64 object-cover rounded-2xl mb-6" 
              alt={selectedPromotion.title}
            />
            
            <p className="text-gray-300 leading-relaxed mb-6">
              {selectedPromotion.description}
            </p>
            
            <div className="flex gap-4">
              <button 
                onClick={() => setSelectedPromotion(null)}
                className="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300"
              >
                Close
              </button>
              <button className="flex-1 bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-black px-6 py-3 rounded-xl font-semibold transition-all duration-300">
                Claim Offer
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}

export default Promotion
