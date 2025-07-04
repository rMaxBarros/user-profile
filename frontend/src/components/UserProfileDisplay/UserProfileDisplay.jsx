import React from 'react';
import styles from './UserProfileDisplay.module.css';

function UserProfileDisplay({ user }) {

    // Se o usuário não existir, retorna null para não renderizar nada
  if (!user) {
    return null;
  }

  return (
    <div className={styles.container}>

      <div className={styles.profileDetails}>
        <h2 className={styles.profileName}>{user.nome}</h2>
        <p><strong>Idade:</strong> {user.idade}</p>
        <p><strong>Bio:</strong> {user.bio}</p>
        <p><strong>Endereço:</strong> {user.rua}, {user.numero}, {user.bairro}, {user.cidade}</p>
      </div>
    </div>
  );
}

export default UserProfileDisplay;
