import { AxiosRequestConfig } from "axios"

const BASE_URL = process.env.REACT_APP_BASE_URL ? process.env.REACT_APP_BASE_URL : "";

export const getBaseUrl = () => {
    if (BASE_URL === "") {
        throw new Error("BASE_URL")
    }
    return BASE_URL;
}

export const buildAxiosConfig: (token: string) => AxiosRequestConfig = (token: string) => {
    return {
        headers: {
            "Authorization": `Bearer ${token}`,
            "Accept": "application/json",
            "Content-Type": "application/json",
            // "X-Requested-With": "XMLHttpRequest"

        }
    }
}