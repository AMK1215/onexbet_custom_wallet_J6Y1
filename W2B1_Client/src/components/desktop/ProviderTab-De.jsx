import React, { useEffect } from 'react'
import { useNavigate } from 'react-router-dom'

export default function ProviderTab({ providers, provider, type }) {
    const navigate = useNavigate();
    

    return (
        <>
            {providers && providers.map((p) => (
                <div
                    key={p.code}
                    onClick={() => navigate(`/?type=${p.pivot.game_type_id === 2 ? "slot" : p.pivot.game_type_id === 6 ? "casino" : ""}&provider=${p.code}`)}
                    className={`cursor-pointer fw-semibold py-1 px-1 gameProvider ${provider === p.code ? 'activeGameList' : ''}`}
                    role="button"
                    aria-selected={provider === p.code}
                    tabIndex={0}
                >
                    {p.provider_code}
                </div>
            ))}
        </>
    )
}
