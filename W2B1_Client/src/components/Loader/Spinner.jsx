import React from 'react'

export default function Spinner() {
  return (
    <div className='my-3'>
        <div className="spinner-border text-light" role="status">
            <span className="visually-hidden">Loading...</span>
        </div>
    </div>
  )
}
