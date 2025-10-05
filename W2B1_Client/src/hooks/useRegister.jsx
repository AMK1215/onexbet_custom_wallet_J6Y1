import { useState } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';
import BASE_URL from './baseUrl';
import { message } from 'antd';

const useRegister = () => {
    const [error, setError] = useState();
    const [errMsg, setErrMsg] = useState();
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();

    const register = async (url, inputData) => {
        setLoading(true);
        try {
            const res = await axios.post(url, inputData);
            if (res.status === 200) {
                setError();
                setLoading(false);
                localStorage.setItem('token', res.data.data.token);
                navigate('/?type=all');
                message.success('Registerd Successfully.');
                return res.data.data.user;
            }
        } catch (e) {
            setLoading(false);
            setError(e.response.data.errors);
            setErrMsg(e.response.data.message);
            return;
        }
        return null;
    };

    return { register, error, errMsg, loading };
};

export default useRegister;
