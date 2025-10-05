import { createContext, useEffect, useState, useMemo } from "react";
import { useNavigate } from "react-router";

const AuthContext = createContext({
    auth: null,
    user: null,
    updateProfile: () => {},
    logout: () => {},
});

const AuthContextProvider = ({ children }) => {
    const [token, setToken] = useState(localStorage.getItem("token"));
    const [profile, setProfile] = useState(() => {
        const savedProfile = localStorage.getItem("userProfile");
        return savedProfile ? JSON.parse(savedProfile) : null;
    });
    const navigate = useNavigate();

    const logout = () => {
        setToken(null);
        setProfile(null);
        navigate('/?type=all');
    }

    useEffect(() => {
        if (token) {
            localStorage.setItem("token", token);
        } else {
            localStorage.removeItem("token");
            localStorage.removeItem("userProfile");
        }
    }, [token]);

    useEffect(() => {
        if (token) {
            const interval = setInterval(() => {
                fetch('https://ag.onexbetmm.site/api/user', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                    }
                })
                .then(res => {
                    if (res.status === 401) {
                        logout();
                        return Promise.reject('Unauthorized');
                    }
                    if (!res.ok) {
                        return Promise.reject('Failed to fetch balance');
                    }
                    return res.json();
                })
                .then(data => {
                    if (data && data.data) {
                        setProfile(currentProfile => {
                            if (
                                !currentProfile ||
                                currentProfile.balance !== data.data.balance ||
                                currentProfile.main_balance !== data.data.main_balance
                            ) {
                                const updatedProfile = { ...currentProfile, ...data.data };
                                localStorage.setItem("userProfile", JSON.stringify(updatedProfile));
                                return updatedProfile;
                            }
                            return currentProfile;
                        });
                    }
                })
                .catch(error => {
                    if (error !== 'Unauthorized') {
                        // console.error("Error fetching real-time balance:", error);
                    }
                });
            }, 5000); // Fetch every 5 seconds

            return () => clearInterval(interval);
        }
    }, [token]);

    const updateProfile = (newProfile) => {
        setProfile(newProfile);
        if (newProfile) {
            localStorage.setItem("userProfile", JSON.stringify(newProfile));
        } else {
            localStorage.removeItem("userProfile");
        }
    };

    const value = useMemo(() => ({
        auth: token,
        user: profile,
        updateProfile,
        logout,
    }), [token, profile]);

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    );
};

export { AuthContext, AuthContextProvider };
