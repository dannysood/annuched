import { AxiosRequestConfig } from "axios"

const BASE_URL = process.env.REACT_APP_BASE_URL ? process.env.REACT_APP_BASE_URL : "";

export const getBaseUrl = () => {
    if (BASE_URL === "") {
        throw new Error("BASE_URL")
    }
    return BASE_URL;
}

// TODO fix axios typing issue for headers by extending AxiosRequestHeaders Type
// @ts-ignore
export const buildAxiosConfig: (token: string, uid: string) => AxiosRequestConfig = (token: string, uid: string) => {
    const authHeaders = process.env.REACT_APP_IS_FIREBASE_VERIFY == "true" ? {"Authorization": `Bearer ${token}`}:{'firebase-uid-for-testing':uid};
    return {
        headers: {
            "Accept": "application/json",
            "Content-Type": "application/json",
            ...authHeaders

        },
    }
}