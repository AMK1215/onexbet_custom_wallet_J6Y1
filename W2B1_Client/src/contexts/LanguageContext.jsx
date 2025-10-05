import { createContext, useEffect, useState, useMemo } from "react";
import en_data from "../lang/en";
import mm_data from "../lang/mm";
import zh_data from "../lang/zh";
import th_data from "../lang/th";


const LanguageContext = createContext({
    updateLanguage: () => { },
    content: null,
    lan: null,
});

const LanguageContextProvider = ({ children }) => {
    const [language, setLanguage] = useState("en");
    const [content, setContent] = useState(en_data);

    useEffect(() => {
        const lan = localStorage.getItem("lan");
        if (lan) {
            setLanguage(lan);
        } else {
            setLanguage("en");
        }
    }, []);

    const updateLanguage = (newLanguage) => {
        if (newLanguage !== language) {
            localStorage.setItem("lan", newLanguage);
            setLanguage(newLanguage);
        }
    };

    useEffect(() => {
     const langMap = {
            en: en_data,
            mm: mm_data,
            zh: zh_data,
            th: th_data,
        };
        setContent(langMap[language] || en_data);
    }, [language]);

    const value = useMemo(
        () => ({
            lan: language,
            content: content,
            updateLanguage,
        }),
        [language, content]
    );

    return (
        <LanguageContext.Provider value={value}>
            {children}
        </LanguageContext.Provider>
    );
};

export { LanguageContext, LanguageContextProvider };
