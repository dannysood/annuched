import axios from "axios";
import { IPost } from "../../types";
import { buildAxiosConfig, getBaseUrl } from "./util";


export const getAllPosts: (token: string, uid:string, cursor?: string) => Promise<{posts: IPost[], nextCursor?: string, prevCursor?: string}> = async (token: string, uid: string, cursor?: string) => {
    let config = buildAxiosConfig(token, uid);
    if(cursor) {
        config = {params: {cursor: cursor}, ...config};
    }
    const result = await axios.get(`${getBaseUrl()}/post`,config);

    const posts = result.data.data.map((item: any) => {
        const post:IPost = {
            id: item["id"],
            title: item["title"],
            description: item["description"],
            ownerName: item["owner_name"],
            createdAt: item["created_at"],
        }
        return post;
    })
    return {
        posts: posts,
        nextCursor: result.data.meta.next_cursor,
        prevCursor: result.data.meta.prev_cursor,
    }

}

export const getOnePost = async (token: string, uid: string, id: string) => {
    return axios.get(`${getBaseUrl()}/post/${id}`,buildAxiosConfig(token, uid));
}

export const createOnePost = async (token: string, uid: string, title: string, description: string) => {
    return axios.post(`${getBaseUrl()}/post`,{title,description},buildAxiosConfig(token, uid));
}