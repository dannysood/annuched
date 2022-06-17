import { Navigate, Route, Routes } from "react-router-dom";
import { Dashboard } from "../pages/Dashboard";
import { Sidebar } from "../components/Sidebar";
import { Login } from "../pages/Login";
import { getCurrentUser } from "../services/auth";
import { Profile } from "../pages/Profile";
import { Create } from "../pages/Create";
export const MainRouter = () => {


  const ProtectedRoute = ({ children }: { children: any }) => {
    if (getCurrentUser()) {
      return children;
    }
    return <Navigate to="/login" />;

  };

  const attachSidebar = (page: JSX.Element) => {
    return (
      <div className="flow-root">
        <p className="float-left w-1/5"><Sidebar /></p>
        <p className="float-right w-4/5">{page}</p>
      </div>
    )

  }

  return (
    <>
      <Routes>
        <Route
          index
          element={
            <ProtectedRoute>
              {attachSidebar(<Dashboard />)}
            </ProtectedRoute>
          }
        />

        <Route
          path="/create"
          element={
            <ProtectedRoute>
              {attachSidebar(<Create />)}
            </ProtectedRoute>
          }
        />

        <Route
          path="/profile"
          element={
            <ProtectedRoute>
              {attachSidebar(<Profile />)}
            </ProtectedRoute>
          }
        />
        <Route path="login" element={<Login />} />
        <Route path="*" element={<Login />} />
      </Routes>

    </>


  );
};