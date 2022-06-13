// Import FirebaseAuth and firebase.
import React, { useEffect, useState } from 'react';
import StyledFirebaseAuth from 'react-firebaseui/StyledFirebaseAuth';
import firebase from 'firebase/compat/app';
import 'firebase/compat/auth';

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
firebase.initializeApp(firebaseConfig);

// Configure FirebaseUI.
const uiConfig = {
  // Popup signin flow rather than redirect flow.
  signInFlow: 'redirect',
  // We will display Google and Facebook as auth providers.
  signInOptions: [
    // firebase.auth.EmailAuthProvider.PROVIDER_ID,
    firebase.auth.GoogleAuthProvider.PROVIDER_ID,
  ],
  callbacks: {
    // Avoid redirects after sign-in.
    signInSuccessWithAuthResult: () => false,
  },
};

const SignInScreen = () => {
  const [isSignedIn, setIsSignedIn] = useState(false); // Local signed-in state.
  const [token, setToken] = useState(""); // Local signed-in state.
  if(firebase.auth().currentUser){
    firebase.auth().currentUser!.getIdToken(true).then(token => {
      setToken(token);
      console.log(token);
    })
  }
  // Listen to the Firebase Auth state and set the local state.
  useEffect(() => {
    const unregisterAuthObserver = firebase.auth().onAuthStateChanged(user => {
      setIsSignedIn(!!user);
    });

    return () => unregisterAuthObserver(); // Make sure we un-register Firebase observers when the component unmounts.
  }, []);

  if (!isSignedIn) {
    return (
      <div>
        <h1>My App</h1>
        <p>Please sign-in:</p>
        <StyledFirebaseAuth uiConfig={uiConfig} firebaseAuth={firebase.auth()} />
      </div>
    );
  }
  return (
    <div>
      <h1>Annuched Blogging Platform</h1>
      <p>Welcome {firebase.auth().currentUser!.displayName}! You are now signed-in!</p>
      <a onClick={() => firebase.auth().signOut()}>Sign-out</a>
    </div>
  );
}

export default SignInScreen;