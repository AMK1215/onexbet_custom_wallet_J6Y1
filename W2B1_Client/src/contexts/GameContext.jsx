import { createContext, useEffect, useMemo, useState } from "react";
import BASE_URL from "../hooks/baseUrl";
import useFetch from "../hooks/useFetch";
import { useSearchParams } from "react-router-dom";

const GameContext = createContext({
    types: null,
    current_type: null,
    current_provider: null,
    updateType: () => {},
    updateProvider: () => {},
    providers: null,
    provider_name: null,
    type_name: null,
    game_lists: null,
    hot_games: null,
    loading: false
});

const GameContextProvider = ({ children }) => {
    const [searchParams] = useSearchParams();
    const getType = searchParams.get("type");
    const getProvider = searchParams.get("provider");

    const [type, setType] = useState(getType || "");
    const [provider, setProvider] = useState(getProvider || "");

    useEffect(() => {
        if (getType !== type) {
            setType(getType || "");
        }
        if (getProvider !== provider) {
            setProvider(getProvider || "");
        }
    }, [getType, getProvider, type, provider]);

    // Fetch only when values are valid
    const { data: types } = useFetch(`${BASE_URL}/game_types`);
    
    // Find the selected game type to get its code
    const selectedGameType = types?.find((t) => t?.id === parseInt(type));
    const gameTypeCode = selectedGameType?.code || null;
    
    // Fetch providers using game type code
    const { data: providersData } = useFetch(gameTypeCode ? `${BASE_URL}/providers/${gameTypeCode}` : null);
    
    // Find the provider ID from the providers data
    const selectedProvider = providersData?.find((p) => p?.id === parseInt(provider));
    const providerName = selectedProvider?.product_name || null;
    const typeName = selectedGameType?.name || null;
    
    // Only fetch game lists when both type and provider are valid
    const { data: game_lists, loading } = useFetch(
        type && selectedProvider ? `${BASE_URL}/game_lists/${type}/${selectedProvider.id}` : null
    );
    const { data: hot_games } = useFetch(`${BASE_URL}/hot_game_lists`);
   
    const updateType = (newType) => setType(newType);
    const updateProvider = (newProvider) => setProvider(newProvider);

    const value = useMemo(
        () => ({
            types: types || null,
            current_type: type,
            current_provider: provider,
            updateType,
            updateProvider,
            provider_name: providerName,
            type_name: typeName,
            providers: providersData || null,
            game_lists: game_lists || null,
            hot_games: hot_games || null,
            loading,
        }),
        [types, providersData, game_lists, hot_games, loading, type, provider, providerName, typeName]
    );
    // console.log('hot_games',hot_games);
    return <GameContext.Provider value={value}>{children}</GameContext.Provider>;
};

export { GameContext, GameContextProvider };
