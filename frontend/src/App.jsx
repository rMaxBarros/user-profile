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

  const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

    const fetchUserProfile = async () => {
      setLoading(true);
      setError(null); // Reseta o erro antes de buscar o perfil
      try {
        const response = await fetch(`${API_BASE_URL}/usuario`);
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

  const handleSaveUser = async (updatedUserFormData) => {
    try {
      const response = await fetch(`${API_BASE_URL}/usuario`, {
        method: 'POST',
        body: updatedUserFormData,
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
      }

      // Se a atualização foi bem-sucedida...
      await fetchUserProfile(); 
      return true; // Indica sucesso
    } catch (error) {
      // Se não for bem sucedida...
      console.error("Erro ao salvar o perfil (App.jsx):", error);
      throw error; // Propaga o erro para o EditProfilePage lidar com ele
    }
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
