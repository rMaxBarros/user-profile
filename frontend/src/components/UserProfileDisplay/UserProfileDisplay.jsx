import React from 'react';
import { useNavigate } from 'react-router-dom'; // Importe useNavigate
import styles from './UserProfileDisplay.module.css';

function UserProfileDisplay({ user }) {
  const navigate = useNavigate();

  if (!user) {
    return null;
  }

  const handleEditClick = () => {
    navigate('/perfil/editar');
  };

  return (
    <div className={styles.container}>
      <div className={styles.profileDetails}>
        <div className={styles.nameAndEdit}>
          <h2 className={styles.profileName}>{user.nome}</h2>
          <button className={styles.editButton} onClick={handleEditClick}>
            Editar
          </button>
        </div>
        <p><strong>Idade:</strong> {user.idade}</p>
        <p><strong>Bio:</strong> {user.bio}</p>
        <p><strong>Endereço:</strong> {user.rua}, {user.numero}, {user.bairro}, {user.cidade}</p>
      </div>
    </div>
  );
}

export default UserProfileDisplay;