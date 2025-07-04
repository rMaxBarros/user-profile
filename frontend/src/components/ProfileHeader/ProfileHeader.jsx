import styles from './ProfileHeader.module.css';

const COVER_IMAGE_URL = "https://images.unsplash.com/photo-1542831371-29b0f74f9713?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D";

function ProfileHeader({ photoUrl }) {
  return (
    <div className={styles.headerContainer}>

      <div
        className={styles.coverImage}
        style={{ backgroundImage: `url(${COVER_IMAGE_URL})` }}
      ></div>

      <img src={photoUrl} alt="Foto de Perfil" className={styles.profilePhoto} />

    </div>
  );
}

export default ProfileHeader;
