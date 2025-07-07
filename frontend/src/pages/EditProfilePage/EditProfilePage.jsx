import React, { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import styles from './EditProfilePage.module.css';

function EditProfilePage({ user, onSave }) {

  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    nome: '',
    idade: '',
    bio: '',
    rua: '',
    numero: '',
    bairro: '',
    cidade: '',
    url_foto: '',
  });

  // Efeito para preencher o formulário com os dados do usuário
  // quando o componente é montado ou quando o usuário é atualizado
  useEffect(() => {
    if (user) {
      setFormData({
        nome: user.nome || '',
        idade: user.idade || '',
        bio: user.bio || '',
        rua: user.rua || '',
        numero: user.numero || '',
        bairro: user.bairro || '',
        cidade: user.cidade || '',
        url_foto: user.url_foto || '',
      });
    }
  }, [user]);

  // Função para lidar com mudanças nos campos do formulário
  // Atualiza o estado do formData com os valores dos inputs
  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prevData) => ({
      ...prevData,
      [name]: value,
    }));
  };

  // Função para lidar com o envio do formulário
  const handleSubmit = async (e) => {
    e.preventDefault();
    console.log("Dados a serem salvos:", formData);

    const dataToSave = {
      ...formData,
      id: user.id,
    };

    try {
      await onSave(dataToSave);
      alert('Perfil atualizado com sucesso!');
      navigate('/perfil'); // Redireciona para a tela de perfil após o salvamento
    } catch (error) {
      console.error("Erro ao salvar o perfil:", error);
      alert(`Erro ao salvar perfil: ${error.message || 'Verifique o console para mais detalhes.'}`);
    }
  };

  if (!user) {
    return (
      <div className={styles.container}>
        <p>Carregando dados para edição...</p>
      </div>
    );
  }

  return (
    <div className={styles.container}>
      <h2 className={styles.title}>Editar Informações</h2>
      <form onSubmit={handleSubmit} className={styles.editForm}>

        {/* Campo Nome */}
        <div className={styles.formGroup}>
          <label htmlFor="nome">Nome</label>
          <input
            type="text"
            id="nome"
            name="nome"
            value={formData.nome}
            onChange={handleChange}
            className={styles.inputField}
          />
        </div>

        {/* Campo Idade */}
        <div className={styles.formGroup}>
          <label htmlFor="idade">Idade</label>
          <input
            type="number"
            id="idade"
            name="idade"
            value={formData.idade}
            onChange={handleChange}
            className={styles.inputField}
          />
        </div>

        {/* Campo Biografia */}
        <div className={styles.formGroup}>
          <label htmlFor="bio">Biografia</label>
          <textarea
            id="bio"
            name="bio"
            value={formData.bio}
            onChange={handleChange}
            className={`${styles.inputField} ${styles.textareaField}`}
            rows="5"
          ></textarea>
        </div>

        {/* Informações Pessoais - Endereço */}
        <h3 className={styles.sectionTitle}>Informações Pessoais</h3>
        <h4 className={styles.subtitle}>Endereço</h4>

        <div className={styles.addressFields}>
          {/* Campo Rua */}
          <div className={styles.formGroup}>
            <label htmlFor="rua">Rua</label>
            <input
              type="text"
              id="rua"
              name="rua"
              value={formData.rua}
              onChange={handleChange}
              className={styles.inputField}
            />
          </div>

          {/* Campo Número */}
          <div className={`${styles.formGroup} ${styles.numField}`}>
            <label htmlFor="numero">Num</label>
            <input
              type="text"
              id="numero"
              name="numero"
              value={formData.numero}
              onChange={handleChange}
              className={styles.inputField}
            />
          </div>
        </div>

        <div className={styles.addressFields}>
          {/* Campo Bairro */}
          <div className={styles.formGroup}>
            <label htmlFor="bairro">Bairro</label>
            <input
              type="text"
              id="bairro"
              name="bairro"
              value={formData.bairro}
              onChange={handleChange}
              className={styles.inputField}
            />
          </div>

          {/* Campo Cidade */}
          <div className={styles.formGroup}>
            <label htmlFor="cidade">Cidade</label>
            <input
              type="text"
              id="cidade"
              name="cidade"
              value={formData.cidade}
              onChange={handleChange}
              className={styles.inputField}
            />
          </div>
        </div>

        {/* Campo URL da Foto (por enquanto em forma de URL) */}
        <div className={styles.formGroup}>
          <label htmlFor="url_foto">URL da Foto</label>
          <input
            type="text"
            id="url_foto"
            name="url_foto"
            value={formData.url_foto}
            onChange={handleChange}
            className={styles.inputField}
          />
        </div>

        {/* Botões de Ação */}
        <div className={styles.buttonsContainer}>
          <Link to="/perfil" className={styles.backButton}>Voltar</Link>
          <button type="submit" className={styles.saveButton}>Salvar</button> 
        </div>
      </form>
    </div>
  );
}

export default EditProfilePage;
