import { useContext, useState } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';
import { message } from 'antd';
import { AuthContext } from '../contexts/AuthContext';

const useLogin = () => {
    const { updateProfile } = useContext(AuthContext);
    const [error, setError] = useState();
    const [errMsg, setErrMsg] = useState();
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();

    const login = async (url, inputData) => {
        setLoading(true);
        try {
            const res = await axios.post(url, inputData);
            if (res.status === 200) {
                setError(null);
                setErrMsg(null);
                setLoading(false);
                const { token, user } = res.data.data;
                localStorage.setItem('token', token);
                updateProfile(user);

                navigate('/?type=all');
                window.location.reload();
                message.success('Logged In Successfully.');
                return user;
            }
        } catch (e) {
            setLoading(false);
            if (e.response && e.response.data) {
                setError(e.response.data.errors);
                setErrMsg(e.response.data.message);
            } else {
                setErrMsg("An unexpected error occurred.");
            }
        }
        return null;
    };

    return { login, error, errMsg, loading };
};

export default useLogin;
