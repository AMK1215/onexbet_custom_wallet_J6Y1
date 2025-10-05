import React, { useContext } from 'react'
import { Spinner, Table } from 'react-bootstrap';
import useFetch from '../../hooks/useFetch';
import BASE_URL from '../../hooks/baseUrl';
import { LanguageContext } from '../../contexts/LanguageContext';

export default function WithdrawLog() {
  const { content } = useContext(LanguageContext);
  const { data: logs, loading } = useFetch(BASE_URL + "/withdrawlogfinicial");

  return (
    <>
        <div className="max-w-2xl mx-auto my-4 mb-5 pb-5 px-2">
            <h2 className="text-xl font-bold text-yellow-400 mb-4 text-center">Withdraw Log</h2>
            {loading && <div className="text-center py-8 text-gray-400 font-semibold">Loading...</div>}
            {logs && logs.length === 0 && (
                <div className="text-center py-8 text-gray-400 font-semibold">
                    <h5>{content?.no_data}</h5>
                </div>
            )}
            <div className="overflow-x-auto rounded-xl border border-gray-700 bg-white/5 shadow-lg">

                <table className="min-w-full text-sm text-left">
                    <thead>
                    <tr className="bg-[#181A29] text-gray-300">
                        <th className="px-3 py-2 font-semibold whitespace-nowrap text-center">{content?.log?.date}</th>
                        <th className="px-3 py-2 font-semibold whitespace-nowrap text-center">{content?.wallet?.account_name}</th>
                        <th className="px-3 py-2 font-semibold whitespace-nowrap text-center">{content?.wallet?.account}</th>
                        <th className="px-3 py-2 font-semibold whitespace-nowrap text-center">{content?.log?.amount}</th>
                        <th className="px-3 py-2 font-semibold whitespace-nowrap text-center">{content?.log?.status || 'Status'}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {logs && logs.map((log, index) => (
                        <tr key={index} className="border-t border-gray-800 hover:bg-[#23243a] transition">
                            <td className="px-3 py-2 whitespace-nowrap text-white text-center">{log.datetime}</td>
                            <td className="px-3 py-2 whitespace-nowrap text-white text-center">{log.account_name}</td>
                            <td className="px-3 py-2 whitespace-nowrap text-white text-center">{log.account_number}</td>
                            <td className="px-3 py-2 whitespace-nowrap text-white text-center">{Number(log.amount).toLocaleString()} Ks</td>
                            <td className="px-3 py-2 whitespace-nowrap text-white text-center">
          <span className={`px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1 shadow
            ${log.status === 'Pending' ? 'bg-yellow-300 text-yellow-900' :
              log.status === 'Success' ? 'bg-green-300 text-green-900' :
                  'bg-red-300 text-red-900'}`}
          >
            {log.status === 'Pending' && (
                <svg className="w-3 h-3" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 6v6l4 2" />
                </svg>
            )}
              {log.status === 'Success' && (
                  <svg className="w-3 h-3" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                      <path d="M5 13l4 4L19 7" />
                  </svg>
              )}
              {log.status === 'Failed' && (
                  <svg className="w-3 h-3" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                      <path d="M6 18L18 6M6 6l12 12" />
                  </svg>
              )}
              {log.status}
          </span>
                            </td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            </div>
        </div>
    </>
  )
}