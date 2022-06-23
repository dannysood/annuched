import axios, { AxiosError } from "axios";
import { buildAxiosConfig, getBaseUrl } from "./util";

export const createUserIfDoesntExist = async (token: string, uid: string) => {
    try {
        axios.post(`${getBaseUrl()}/auth/create`, {}, buildAxiosConfig(token,uid));
    } catch (error: any) {
        if (error.response?.data.message == "The firebase uid has already been taken.") {
            // dont throw error as this api is used as if user exists check also
        } else {
            throw error;
        }
    }
};