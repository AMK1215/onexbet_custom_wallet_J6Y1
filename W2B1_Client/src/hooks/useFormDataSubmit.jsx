import { message } from 'antd';
import { useState } from 'react';
import { useNavigate } from 'react-router';

const useFormDataSubmit = () => {
  const [error, setError] = useState(null);
  const [errMsg, setErrMsg] = useState(null);
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();

  const formSubmit = async (url, inputData, method, redirect, msg) => {
    setLoading(true);
    setError(null);
    setErrMsg(null);

    try {
      const res = await fetch(url, {
        method,
        headers: {
          "Accept": "application/json",
        //   "Content-Type": "application/json",
          // 'Content-Type': 'multipart/form-data',
          "Authorization": `Bearer ${localStorage.getItem("token")}`,
        },
        body: inputData,
      });

      const data = await res.json();
      // console.log(data);
      

      if (!res.ok) {
        if (res.status === 422) {
          setError(data.errors);
        }else {
          setError("An unexpected error occurred.");
        }
        return; // Early return on error
      }

      navigate(redirect);
      message.success(msg);
      return data; // Return the successful response data

    } catch (error) {
      // console.error("Error during fetch:", error);
      setError("An error occurred during the deposit process. Please try again.");
    } finally {
      setLoading(false); // Ensure loading state is reset
    }
  };

  return { formSubmit, error, loading };
};

export default useFormDataSubmit;
