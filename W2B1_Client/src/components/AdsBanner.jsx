import React, { useContext, useEffect } from 'react'
import Swal from 'sweetalert2'
import withReactContent from 'sweetalert2-react-content'
import { GeneralContext } from '../contexts/GeneralContext';

const AdsBanner = () => {
  const {ads_banner} = useContext(GeneralContext);
  const MySwal = withReactContent(Swal);
  const adsFire = () => {
    MySwal.fire({
      imageUrl: "https://ag.onexbetmm.site/api/.."+ads_banner[0].img_url,
      imageHeight: 150,
      width: '100%',
      text: ads_banner[0]?.text || '',
      confirmButtonText: 'OK',
      customClass: {
        popup: 'my-swal-popup',
        htmlContainer: 'my-swal-text'
      }
    })
  }
  useEffect(() => {
    if (ads_banner?.length > 0 && ads_banner[0]?.img_url) {
      adsFire();
    }
  }, [ads_banner]);

  return null;
}

export default AdsBanner