import React, { useState, useEffect } from 'react';
import UserProfileDisplay from './components/UserProfileDisplay/UserProfileDisplay';
import ProfileHeader from './components/ProfileHeader/ProfileHeader'; //
import './App.css';
import './index.css';

function App() {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchUserProfile = async () => {
      try {
        const response = await fetch('http://localhost/sync-360-api/usuario');
        if (!response.ok) {
          // Se o usuário não existir (ID 1 não encontrado no backend), o backend tenta criar um padrão
          // Se mesmo assim falhar, ou se a resposta não for OK por outro motivo, lança o erro
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

    fetchUserProfile();
  }, []);

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
    <div className="App">
      <ProfileHeader photoUrl={user.url_foto} userName={user.nome} />
      <UserProfileDisplay user={user} />
    </div>
  );
}

export default App;
