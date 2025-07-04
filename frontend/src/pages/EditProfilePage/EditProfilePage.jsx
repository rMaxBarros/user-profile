import React from 'react';
import { Link } from 'react-router-dom';
import styles from './EditProfilePage.module.css';

function EditProfilePage({ user, onSave }) {


  if (!user) {
    return (
      <div className={styles.container}>
        <p>Carregando dados para edição...</p>
      </div>
    );
  }

  return (
    <div className={styles.container}>
      <h2 className={styles.title}>Editar Informações do Usuário</h2>
      <p>Formulário de edição para: {user.nome}</p>

      <div className={styles.buttonsContainer}>
        <Link to="/perfil" className={styles.backButton}>Voltar</Link>
        <button className={styles.saveButton} onClick={() => alert('Função Salvar ainda não implementada!')}>Salvar</button>
      </div>
    </div>
  );
}

export default EditProfilePage;
