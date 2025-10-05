import React, { useMemo } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";
import "swiper/css/scrollbar";
import VideoCard from "../components/mobile/VideoCard";
import useVideoAutoplay from "../hooks/useVideoAutoplay";
import useFetch from "../hooks/useFetch";
import BASE_URL from "../hooks/baseUrl";

const ReelsPage = () => {
  const { data: videos } = useFetch(BASE_URL + "/videoads");

  // Ensure refs persist and do not break Hooks rules
  const videoRefs = useMemo(() => videos?.map(() => React.createRef()), [videos]);

  // Apply autoplay logic
  useVideoAutoplay(videoRefs);

  return (
    <div className="pt-lg-5 reelsContainer" style={{ overflow: "hidden" }}>
      <Swiper
        direction="vertical"
        slidesPerView={1}
        spaceBetween={0}
        onSlideChange={() => console.log("Slide changed")}
        style={{ height: "80vh" }}
      >
        {videos &&
          videos.map((video, index) => (
            <SwiperSlide key={index}>
              <VideoCard video={video.video_url} ref={videoRefs[index]} />
            </SwiperSlide>
          ))}
      </Swiper>
    </div>
  );
};

export default ReelsPage;
