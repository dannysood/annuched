import { initializeApp } from "firebase/app";
import { getAuth } from "firebase/auth";

const firebaseConfigString = process.env.REACT_APP_FIREBASE_CONFIG ? process.env.REACT_APP_FIREBASE_CONFIG : "";
if(firebaseConfigString === ""){
  throw new Error("firebase config empty!");
}

const firebaseConfig = JSON.parse(firebaseConfigString);

const firebaseApp = initializeApp(firebaseConfig);
export const auth = getAuth(firebaseApp);

export const getCurrentUser = () => auth.currentUser;
export const getUserToken = (isForceRefresh: boolean) => auth.currentUser?.getIdToken(isForceRefresh);
export const signOut = () => auth.signOut();