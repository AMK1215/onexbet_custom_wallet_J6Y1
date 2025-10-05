"use client";
import BASE_URL from '../../hooks/baseUrl';
import useFetch from "../../hooks/useFetch";
import styles from "./RelatedProviderLists.module.css";
import React from "react";
import {useNavigate} from "react-router-dom";


function RelatedProviderItem({item}) {

    const navigate = useNavigate();
    return (<>
        {/*<div>{item.provider}</div>*/}
        <div>

                <button key={item.id} className={`${styles.tabBtn}`}   onClick={() => navigate(`/?type=1&provider=${item.id}`)}>
                    <span className="gameTabFontSize">{item.provider}</span>
                </button>

        </div>
    </>)
}

export default function RelatedProviderLists({customGameType}) {

            const {data: providersData} = useFetch(`${BASE_URL}/providers/${customGameType}`);

            return (<>
                {providersData.map(item => <RelatedProviderItem key={item.id} item={item}/>)}
            </>)
    }