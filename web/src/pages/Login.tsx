import { GoogleAuthProvider } from "firebase/auth";
import React, { useEffect } from 'react';
import { useNavigate } from "react-router-dom";
import { StyledFirebaseAuth } from "../components/StyledFirebaseAuth";
import { createUserIfDoesntExist } from "../services/api/user";
import { auth, getUserToken } from "../services/auth";
// Configure FirebaseUI.
const uiConfig = {
  // Popup signin flow rather than redirect flow.
  signInFlow: 'redirect',
  // We will display Google and Facebook as auth providers.
  signInOptions: [
    // firebase.auth.EmailAuthProvider.PROVIDER_ID,
    GoogleAuthProvider.PROVIDER_ID,
  ],
  callbacks: {
    // Avoid redirects after sign-in.
    signInSuccessWithAuthResult: () => false,
  },
};
export const Login = () => {
  const navigate = useNavigate();
  useEffect(() => {
    const unregisterAuthObserver = auth.onAuthStateChanged(async (user) => {
      if (user) {
        const token = await getUserToken(false);
        if(token){
          await createUserIfDoesntExist(token);
          navigate("/");
        }
      }
    });

    return () => {
      unregisterAuthObserver();
    };
  }, []);
  return (
    <div className="flex h-screen bg-blue-600 text-white">
      <div className="m-auto">
         <div className="text-center text-8xl p-2 m-2">
           annuched
         </div>
         <div className="text-center text-2xl p-2 m-2">
           continue using google
         </div>
        <StyledFirebaseAuth uiConfig={uiConfig} auth={auth} />
      </div>
    </div>
  );
}