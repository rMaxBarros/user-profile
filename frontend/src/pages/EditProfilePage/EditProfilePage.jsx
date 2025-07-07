import React, { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import styles from './EditProfilePage.module.css';

const DEFAULT_PROFILE_IMAGE = 'https://t3.ftcdn.net/jpg/05/16/27/58/360_F_516275801_f3Fsp17x6HQK0xQgDQEELoTuERO4SsWV.jpg';

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
  });
  const [profileImageFile, setProfileImageFile] = useState(null);
  const [profileImagePreview, setProfileImagePreview] = useState(null);

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
      });
      // setProfileImageFile(user.url_foto || '');
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

  // Função para lidar com a seleção do arquivo de imagem
  const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      setProfileImageFile(file);
      setProfileImagePreview(URL.createObjectURL(file));
    } else {
      setProfileImageFile(null);
      setProfileImagePreview(null);
    }
  };

  // Função para lidar com o envio do formulário
  const handleSubmit = async (e) => {
    e.preventDefault();

    const dataToSave = new FormData();
    dataToSave.append('id', user.id);
    dataToSave.append('nome', formData.nome);
    dataToSave.append('idade', formData.idade);
    dataToSave.append('bio', formData.bio);
    dataToSave.append('rua', formData.rua);
    dataToSave.append('numero', formData.numero);
    dataToSave.append('bairro', formData.bairro);
    dataToSave.append('cidade', formData.cidade);

    // Adiciona o arquivo de imagem se houver um selecionado
    if (profileImageFile) {
      dataToSave.append('profile_image', profileImageFile);
    } else {
      // Se nenhum novo arquivo for selecionado, mas havia uma URL existente, envie-a
      // Importante para que o backend saiba que a foto não mudou ou para manter a antiga
      dataToSave.append('url_foto_existente', user.url_foto);
    }


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

        {/* Campo de pré-visualização da Imagem de Perfil */}
        <div className={styles.profileImageUpload}>
          <label htmlFor="profile_image_upload" className={styles.imageUploadLabel}>
            {/* Condicional para exibir o preview da nova imagem */}
            {profileImagePreview && (
              <img
                src={profileImagePreview}
                alt="Pré-visualização da Foto de Perfil"
                className={styles.profileImagePreview}
              />
            )}
            <span className={styles.editIcon}>
                {profileImagePreview ? 'Mudar foto do perfil' : 'Clique para selecionar uma foto'}
                &#x270E;
            </span>
          </label>
          <input
            type="file"
            id="profile_image_upload"
            name="profile_image_upload"
            accept="image/*"
            onChange={handleFileChange}
            className={styles.fileInput}
          />
        </div>

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
