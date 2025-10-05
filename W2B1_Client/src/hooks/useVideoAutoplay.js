
import { useEffect } from "react";

function useVideoAutoplay(videoRefs) {
  useEffect(() => {
    const handleIntersection = (entries) => {
      entries.forEach((entry) => {
        const video = entry.target;
        if (entry.isIntersecting) {
          video.play();
        } else {
          video.pause();
        }
      });
    };

    const observer = new IntersectionObserver(handleIntersection, {
      root: null,
      threshold: 0.9, // Ensure video is mostly visible before autoplaying
    });

    videoRefs.forEach((ref) => observer.observe(ref.current));

    // return () => {
    //   videoRefs.forEach((ref) => observer.unobserve(ref.current));
    // };
  }, [videoRefs]);
}

export default useVideoAutoplay;
