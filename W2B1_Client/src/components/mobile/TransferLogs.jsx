import React, { useEffect, useState } from 'react'
import { Spinner, Table } from 'react-bootstrap'
import BASE_URL from '../../hooks/baseUrl';

export default function TransferLogs() {
    const [selectedDate, setSelectedDate] = useState('today');
    return (
        <>
            <div className="logsContainer  container my-4 mb-5 pt-4 px-sm-4 rounded-3 pb-5">
                <div className="d-flex flex-wrap justify-content-center mb-4 gap-1 gap-sm-3">
                    <button className={`btn btn-sm btn-${selectedDate === "today" ? "light" : "outline-light"}`} onClick={() => setSelectedDate("today")}>
                        Today
                    </button>
                    <button className={`btn btn-sm btn-${selectedDate === "yesterday" ? "light" : "outline-light"}`} onClick={() => setSelectedDate("yesterday")}>
                        Yesterday
                    </button>
                    <button className={`btn btn-sm btn-${selectedDate === "this_week" ? "light" : "outline-light"}`} onClick={() => setSelectedDate("this_week")}>
                        This Week
                    </button>
                    <button className={`btn btn-sm btn-${selectedDate === "last_week" ? "light" : "outline-light"}`} onClick={() => setSelectedDate("last_week")}>
                        Last Week
                    </button>
                </div>
                <div className="table-responsive ">
                    <table className='text-center w-full'>
                        <thead className='border-light  border-bottom'>
                            <tr >
                                <th>
                                    <small>Date</small>
                                </th>
                                <th>
                                    <small>Game Name
                                    </small>
                                </th>
                                <th>
                                    <small>Type</small>
                                </th>
                                <th>
                                    <small>Win/Lose
                                    </small>
                                </th>
                                <th>
                                    <small>Amount
                                    </small>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr >
                                <td>{new Date().toLocaleDateString()}</td>
                                <td>Arcade Game</td>
                                <td>
                                    Slots
                                </td>
                                <td >
                                    Win
                                </td>
                                <td >
                                    10000
                                </td>
                            </tr>

                        </tbody>
                    </table>

                </div>

            </div>
        </>
    )

}
