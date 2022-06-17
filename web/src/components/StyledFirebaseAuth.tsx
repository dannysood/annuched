import { useEffect, useRef, useState } from 'react';
import * as firebaseui from 'firebaseui';
import 'firebaseui/dist/firebaseui.css';
import { Auth } from 'firebase/auth';

interface Props {
    // The Firebase UI Web UI Config object.
    // See: https://github.com/firebase/firebaseui-web#configuration
    uiConfig: firebaseui.auth.Config;
    // Callback that will be passed the FirebaseUi instance before it is
    // started. This allows access to certain configuration options such as
    // disableAutoSignIn().
    uiCallback?(ui: firebaseui.auth.AuthUI): void;
    // The Firebase App auth instance to use.
    auth: Auth; // As firebaseui-web
    className?: string;
}


const StyledFirebaseAuth = ({ uiConfig, auth, className, uiCallback }: Props) => {
    const [userSignedIn, setUserSignedIn] = useState(false);
    const elementRef = useRef(null);

    useEffect(() => {

        const firebaseUiWidget = firebaseui.auth.AuthUI.getInstance() || new firebaseui.auth.AuthUI(auth);
        if (uiConfig.signInFlow === 'popup')
            firebaseUiWidget.reset();

        const unregisterAuthObserver = auth.onAuthStateChanged((user: any) => {
            if (!user && userSignedIn){
                firebaseUiWidget.reset();
            }

            setUserSignedIn(!!user);
        });


        if (uiCallback)
            uiCallback(firebaseUiWidget);

        // Render the firebaseUi Widget.
        // @ts-ignore
        firebaseUiWidget.start(elementRef.current, uiConfig);

        return () => {
            unregisterAuthObserver();
            firebaseUiWidget.reset();
        };
    }, [firebaseui, uiConfig]);

    return <div className={className} ref={elementRef} />;
};

export { StyledFirebaseAuth };