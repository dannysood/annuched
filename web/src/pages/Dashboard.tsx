import React, { useEffect, useState } from 'react';
import { DateTime } from "luxon";
import { getCurrentUser, getUserToken } from '../services/auth';
import { useNavigate } from 'react-router-dom';
import { IPost } from '../types';
import { getAllPosts } from '../services/api/post';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faClock, faPencilRuler, faUser } from '@fortawesome/free-solid-svg-icons';

export const Dashboard = () => {
    const navigate = useNavigate()
    const [posts, setPosts] = useState<IPost[]>([]);
    const [paginationCursors, setPaginationCursors] = useState<{ nextCursor?: string, prevCursor?: string }>({});
    const [currentCursor, setCurrentCursor] = useState<string>("");
    const [isDataFetched, setIsDataFetched] = useState<boolean>(false);

    const getPostsFromAPI = async () => {
        const $user = getCurrentUser();
        if (!$user) {
            navigate("/login");
        }
        const token = await getUserToken(false)
        if (token) {
            const postsFromAPI = await getAllPosts(token, currentCursor == "" ? undefined : currentCursor);
            setPosts(postsFromAPI.posts);
            setPaginationCursors({ nextCursor: postsFromAPI.nextCursor, prevCursor: postsFromAPI.prevCursor });
            setIsDataFetched(true);
        }
    }
    useEffect(() => { getPostsFromAPI() }, [currentCursor]);
    // @ts-ignore
    if (!isDataFetched) {
        return (<div className="flex justify-center items-center m-20">
            <svg role="status" className="w-8 h-8 mr-2 text-white animate-spin dark:text-white fill-blue-300" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"></path>
                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"></path>
            </svg>
            Loading

        </div>)
    }

    return (
        <div>
            <div className="flex justify-center items-center">
                <div className="text-center text-8xl p-2 m-2">
                    Feed
                </div>
            </div>
            <div className="grid grid-cols-2 gap-4 w-full pl-4 pr-4">
                {posts.map((post) => {
                    return (
                        <div className="container rounded-xl shadow p-8">
                            <FontAwesomeIcon icon={faPencilRuler} className="text-blue-500" size="lg" />
                            <p className="text-blue-300 font-bold mb-5 text-lg break-words">
                                {post.title}
                            </p>
                            <p className="text-lg break-words">
                                {/* {`${post.description.substring(0, 200)}...`} */}
                                {`${post.description}`}
                            </p>
                            <p className="text-sm text-right mt-6">
                                <FontAwesomeIcon icon={faUser} className="text-blue-500 mr-2" size="lg" />
                                <span className="mr-10">{post.ownerName}</span>
                                <FontAwesomeIcon icon={faClock} className="text-blue-500 mr-2" size="lg" />
                                <span className="mr-2">{DateTime.fromISO(post.createdAt).toLocaleString(DateTime.DATETIME_SHORT)}</span>
                            </p>
                        </div>
                    );
                })}
            </div>
            <div className='flex items-center justify-center m-5 mb-20'>
                {paginationCursors.prevCursor ? (<button className="hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border hover:border-transparent rounded mr-2" onClick={() => setCurrentCursor(paginationCursors.prevCursor!)}>
                    Last
                </button>) : (<></>)}
                {paginationCursors.nextCursor ? (<button className="hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border hover:border-transparent rounded ml-2" onClick={() => setCurrentCursor(paginationCursors.nextCursor!)}>
                    Next
                </button>) : (<></>)}

            </div>
        </div>


    );
}
