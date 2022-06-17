import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faEdit, faHome, faSignOut, faUser } from "@fortawesome/free-solid-svg-icons";
import { Link } from "react-router-dom";
import { auth, signOut } from "../services/auth";
import { useNavigate } from 'react-router-dom';
import { useEffect, useState } from "react";


const sidebarMenuItems = [
  {
    name: "Feed",
    path: "/",
    icon: faHome
  },
  {
    name: "Create",
    path: "/create",
    icon: faEdit
  },
  {
    name: "Profile",
    path: "/profile",
    icon: faUser
  },
  {
    name: "Logout",
    path: "/login",
    icon: faSignOut
  }
]


export const Sidebar = () => {
  const [name, setName] = useState("");
  const navigate = useNavigate();
  const triggerSignout = async () => {
    await signOut();
    navigate("/login");
  }

  useEffect(() => {
    const unregisterAuthObserver = auth.onAuthStateChanged($user => {
      if (!$user) {
        navigate("/login");
      } else {
        setName($user.displayName ? $user.displayName : "User");
      }

    });
    return () => unregisterAuthObserver(); // Make sure we un-register Firebase observers when the component unmounts.
  }, []);

  return (

    <div className="top-0 left-0 bg-blue-600 p-10 pl-20 text-white fixed h-full w-1/5 clearfix">
      <div className="mb-20 text-center decoration-8 text-2xl">
        Hi, {name}
      </div>
      <ul>
        {sidebarMenuItems.map((sidebarMenuItem) => {
          return (
            <Link to={sidebarMenuItem.path} onClick={sidebarMenuItem.path === "/login"? triggerSignout : undefined}>
            <li>
              <div className="p-2 flex mb-5">
                <div className="h-2 w-2 circle"><FontAwesomeIcon icon={sidebarMenuItem.icon} size="sm" /></div>
                <h5 className="uppercase flex-1 text-center">{sidebarMenuItem.name}</h5>
              </div>
            </li>
            </Link>
          )
        })}
      </ul>
    </div>
  )

}

