import { useForm } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import { object, string, number, array, InferType } from "yup";
import { getUserToken } from "../services/auth";
import { createOnePost } from "../services/api/post";
import { useState } from "react";
import { useNavigate } from "react-router-dom";

const schema = object({
  title: string().required().min(30),
  description: string().required().min(100),
});



type Props = InferType<typeof schema>;

export const Create = () => {
  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<Props>({
    resolver: yupResolver(schema),
  });

  const [isLoading, setIsLoading] = useState(false);
  const navigate = useNavigate();
  const onSubmit = async (values: Props) => {
    setIsLoading(true);
    const token = await getUserToken(false);

    if(!token){
      setIsLoading(false);
      navigate("/login");
    }

    await createOnePost(token!,values.title, values.description);
    setIsLoading(false);
    navigate("/");

  }

  return (
    <form className="" onSubmit={handleSubmit(onSubmit)}>
      <div className="flex justify-center items-center">
                <div className="text-center text-8xl p-10">
                    Create Post
                </div>
            </div>
      <div className="mb-6 p-6 text-center w-full">
      <div className="p-6 pt-10">
        <label htmlFor="title" className="text-blue-300 text-2xl mt-10">Title</label>
        <input type="text" placeholder="Post Title" id="title" {...register("title")} className="block p-4 w-full text-gray-900 bg-gray-50 rounded-lg border border-gray-300 sm:text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 text-center gap-4"/>
        <span className="text-red-700">{errors?.title?.message}</span>
      </div>
      <div className="p-6 pt-10">
        <label htmlFor="description" className="text-blue-300 text-2xl pt-20">Description</label>
      <textarea placeholder="Post Description" id="description" {...register("description")} rows={6} className="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 text-center"></textarea>
      <span className="text-red-700">{errors?.description?.message}</span>
      </div>
      <button className="hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border hover:border-transparent rounded mr-2 m-6 mt-10 align-center" type="submit">
        Create
      </button>
        </div>
        </form>

        );
}
