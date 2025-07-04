import React, { useState, useEffect } from 'react';
import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import UserProfileDisplay from './components/UserProfileDisplay/UserProfileDisplay';
import ProfileHeader from './components/ProfileHeader/ProfileHeader';
import EditProfilePage from './pages/EditProfilePage/EditProfilePage';
import './App.css';
import './index.css';

function App() {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

    const fetchUserProfile = async () => {
      setLoading(true);
      setError(null); // Reseta o erro antes de buscar o perfil
      try {
        const response = await fetch('http://localhost/sync-360-api/usuario');
        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        setUser(data);
      } catch (e) {
        console.error("Erro ao buscar perfil do usuário:", e);
        setError(e.message || "Não foi possível carregar o perfil do usuário.");
      } finally {
        setLoading(false);
      }
    };

    useEffect(() => {
      fetchUserProfile(); // Chama a função na montagem do componente
  }, []);

  const handleSaveUser = async (updatedUser) => {
    console.log("Usuário a ser salvo:", updatedUser);
  };

  if (loading) {
    return <div className="app-container">Carregando perfil...</div>;
  }

  if (error) {
    return <div className="app-container error-message">{error}</div>;
  }

  if (!user) {
    return <div className="app-container">Nenhum perfil encontrado.</div>;
  }

  return (
    <Router>
      <div className="App">

        <ProfileHeader photoUrl={user.url_foto} userName={user.nome} />

        <Routes>
          <Route path="/perfil" element={<UserProfileDisplay user={user} />} />
          <Route 
            path="/perfil/editar" 
            element={<EditProfilePage user={user} onSave={handleSaveUser} />} 
          />
          <Route path="/" element={<UserProfileDisplay user={user} />} />
        </Routes>
      </div>
    </Router>
  );
}

export default App;
