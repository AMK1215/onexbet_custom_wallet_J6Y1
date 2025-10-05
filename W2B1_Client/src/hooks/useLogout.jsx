import { useContext, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import BASE_URL from './baseUrl';
import { message } from 'antd';
import { AuthContext } from '../contexts/AuthContext';

const useLogout = () => {
    const { logout: contextLogout } = useContext(AuthContext);
    const [error, setError] = useState();
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();

    const logout = async () => {
        setLoading(true);
        try {
            const response = await fetch(`${BASE_URL}/logout`, {
                method: "POST",
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    "Authorization": `Bearer ${localStorage.getItem('token')}`
                },
            });
            if (response.ok) {
                setLoading(false);
                contextLogout();
                message.success('Logout successfully.');
            } else {
                console.error("Logout failed:", response.statusText);
            }
        } catch (error) {
            console.error("Error during logout:", error);
        }
    }

    return { logout, error, loading };
};

export default useLogout;
