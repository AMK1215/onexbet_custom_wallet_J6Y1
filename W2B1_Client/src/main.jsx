import React from 'react'
import ReactDOM from 'react-dom/client'
import { RouterProvider } from 'react-router-dom'
import { router } from './routes/index.jsx'
//import 'bootstrap/dist/css/bootstrap.min.css'
import './index.css'
import './tailwind_css.css'
import 'react-toastify/dist/ReactToastify.css';
import { LanguageContextProvider } from './contexts/LanguageContext.jsx'

ReactDOM.createRoot(document.getElementById('root')).render(
  <LanguageContextProvider>
    <RouterProvider router={router} />
  </LanguageContextProvider>
)
