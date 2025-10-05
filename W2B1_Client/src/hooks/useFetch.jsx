import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';

const useFetch = (url) => {
    let [data, setData] = useState([]);
    let [loading, setLoading] = useState(false);
    let [error, setError] = useState(null);
    let navigate = useNavigate();

    useEffect(() => {
        let abortController = new AbortController();
        let signal = abortController.signal;

        setLoading(true);
        // console.log('Fetching URL:', url);
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': "Bearer " + localStorage.getItem('token')
            },
            signal
        })
            .then(res => {
                // console.log('Response status:', res.status, 'for URL:', url);
                if(res.status === 401){
                    localStorage.removeItem('token');
                    navigate('/?type=all');
                }
                if (!res.ok) {
                    throw Error("Something Went Wrong!");
                }
                return res.json();
            })
            .then(data => {
                // console.log('API Response for', url, ':', data);
                setData(data.data);
                setLoading(false);
            })
            .catch(e => {
                // console.error('API Error for', url, ':', e.message);
                setError(e.message);
                setLoading(false);
            //   navigate('/');
            });

        // Cleanup function
        return () => {
            abortController.abort();
        };

    }, [url]);

    return { data, loading, error };
}

export default useFetch;