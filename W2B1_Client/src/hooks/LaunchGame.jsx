import BASE_URL from "./baseUrl";

const launchGame = (type_id, provider_id, game_id) => async (e) => {
  e.preventDefault();
  const auth = localStorage.getItem("token");
  if(!auth) {
    window.location.href = "/login";
  }
  const inputData = {
    type_id,
    provider_id,
    game_id,
  };

  try {
    const response = await fetch(`${BASE_URL}/launch_game`, {
      method: "POST",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
      body: JSON.stringify(inputData),
    });

    if (!response.ok) {
      if(response.status === 401) {
        window.location.href = "/";
      }
      throw new Error("Launch Game failed");
    }

    const data = await response.json();
    // console.log(data);
    
    window.location.href = data.Url;
    // window.open(data.Url, "_blank");
    // console.log("Launch Game success");
  } catch (error) {
    // console.error("Launch Game error:", error);
  }
};

export default launchGame;
