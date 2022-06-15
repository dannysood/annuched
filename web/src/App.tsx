// Import FirebaseAuth and firebase.
import React, { useEffect, useState } from 'react';
import {initializeApp} from 'firebase/app';
import { getAuth, GoogleAuthProvider } from "firebase/auth";
import { StyledFirebaseAuth } from './StyledFirebaseAuth';

// Configure Firebase.
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyBYkiZM6J25QP6k5WLqTA5P4QwpEn2nePc",
  authDomain: "annuched.firebaseapp.com",
  projectId: "annuched",
  storageBucket: "annuched.appspot.com",
  messagingSenderId: "328450220499",
  appId: "1:328450220499:web:b6cb095f9477b106510f26",
  measurementId: "G-1FPS8KM9PE"
};

const firebaseApp = initializeApp(firebaseConfig);
const auth = getAuth(firebaseApp);
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



const SignInScreen = () => {
  const [isSignedIn, setIsSignedIn] = useState(false); // Local signed-in state.
  const [token, setToken] = useState(""); // Local signed-in state.

  if(auth.currentUser){
    auth.currentUser!.getIdToken(true).then(token => {
      setToken(token);
      console.log(token);
    })
  }
  // Listen to the Firebase Auth state and set the local state.
  useEffect(() => {
    const unregisterAuthObserver = auth.onAuthStateChanged(user => {
      setIsSignedIn(!!user);
    });

    return () => unregisterAuthObserver(); // Make sure we un-register Firebase observers when the component unmounts.
  }, []);

  if (!isSignedIn) {
    return (
      <div>
        <h1>My App</h1>
        <p>Please sign-in:</p>
        <StyledFirebaseAuth uiConfig={uiConfig} firebaseAuth={auth} />
      </div>
    );
  }
  return (
    <div>
      <h1>Annuched Blogging Platform</h1>
      <p>Welcome {auth.currentUser!.displayName}! You are now signed-in!</p>
      <a onClick={() => auth.signOut()}>Sign-out</a>
    </div>
  );
}

export default SignInScreen;