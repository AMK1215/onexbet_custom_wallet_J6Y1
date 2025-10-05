import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';

const useFetchHot = (url) => {
    let [data, setData] = useState([]);
    let [loading, setLoading] = useState(false);
    let [error, setError] = useState(null);
    let navigate = useNavigate();

    useEffect(() => {
        let abortController = new AbortController();
        let signal = abortController.signal;

        setLoading(true);
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
                setData(data);
                setLoading(false);
            })
            .catch(e => {
                setError(e.message);
                setLoading(false);
            });

        // Cleanup function
        return () => {
            abortController.abort();
        };

    }, [url]);

    return { data, loading, error };
}

export default useFetchHot; 