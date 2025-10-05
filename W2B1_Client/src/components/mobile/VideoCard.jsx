 import { forwardRef } from "react";

const VideoCard = forwardRef(({ video }, ref) => {

  return (
    <div className="videocard" style={{ height: "80vh", display: "flex", justifyContent: "center", alignItems: "center" }}>
      <video
        ref={ref}
        className="videocard__video"
        src={'https://ag.onexbetmm.site/api/..'+video}
        loop
         style={{ width: "100%", height: "100%" }}
      />
    </div>
  );
});

export default VideoCard;
