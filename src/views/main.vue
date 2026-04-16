<template>
  <div class="main-view">
    <nav>
      <div class="logo">
        <img src="/assets/images.jpeg" alt="Atlas Logo">
      </div>
      <div class="nav-center">
        <h2 class="main-title">Gestionnaire de Produits Plastiques</h2>
      </div>
      <div class="nav-links">
        <div class="user-info">
          <div class="user-avatar">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20 21C20 16.5817 16.4183 13 12 13C7.58172 13 4 16.5817 4 21M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="user-details">
            <span class="user-name">{{ user.name || 'Utilisateur' }}</span>
            <span class="last-login">{{ user.role === 1 ? 'Administrateur' : 'Utilisateur' }}</span>
          </div>
        </div>
        <button class="Btn" @click="logout">
          <div class="sign"><svg viewBox="0 0 512 512"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path></svg></div>
          <div class="text">Se déconnecter</div>
        </button>
      </div>
    </nav>
    
    <section class="main"> 
      <div class="banner">
        <div class="carousel-container">
          <div class="carousel" :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
          <div class="carousel-slide">
              <img src="/assets/balls.PNG?url" alt="Slide 1" class="carousel-image">
            </div>
            <div class="carousel-slide">
              <img src="/assets/box.PNG?url" alt="Slide 2" class="carousel-image">
            </div>
            <div class="carousel-slide">
              <img src="/assets/lunch.PNG?url" alt="Slide 3" class="carousel-image">
            </div>
            <div class="carousel-slide">
              <img src="/assets/zellij.PNG?url" alt="Slide 4" class="carousel-image">
            </div>
           
          </div>
          <div class="carousel-controls">
            <button 
              v-for="(slide, index) in 4" 
              :key="index"
              @click="currentSlide = index"
              :class="{ active: currentSlide === index }"
              class="carousel-dot"
            ></button>
          </div>
        </div>
      </div>
      </section>
      
      <ActionCards 
        v-show="!showArchiveModal && !showDuplicateModal"
        @archive-click="handleArchive" 
        @duplicate-click="handleDuplicate" 
      />
      
      <!-- Modals -->
    <ArchiveModal 
      :show="showArchiveModal" 
      @close="showArchiveModal = false"
      @success="showSuccessAlert"
      @error="showErrorAlert"
    />
    
    <DuplicateModal 
      :show="showDuplicateModal" 
      @close="showDuplicateModal = false"
      @success="showSuccessAlert"
    />
    
    <!-- Alert -->
    <AlertModal
      :show="showAlert"
      :type="alertType"
      :title="alertTitle"
      :message="alertMessage"
      @close="showAlert = false"
    />
  </div>
</template>

<script>
import ActionCards from '@/components/ActionCards.vue'
import ArchiveModal from '@/components/ArchiveModal.vue'
import DuplicateModal from '@/components/DuplicateModal.vue'
import AlertModal from '@/components/AlertModal.vue'

export default {
  name: 'MainView',
  components: {
    ActionCards,
    ArchiveModal,
    DuplicateModal,
    AlertModal
  },
  data() {
    return {
      currentSlide: 0,
      showArchiveModal: false,
      showDuplicateModal: false,
      showAlert: false,
      alertType: 'success',
      alertTitle: '',
      alertMessage: '',
      user: {} // Informations utilisateur
    }
  },
  mounted() {
    // Récupérer les informations utilisateur
    this.loadUserInfo()
    
    // Auto-rotate carousel
    setInterval(() => {
      this.currentSlide = (this.currentSlide + 1) % 4
    }, 4000)
  },
  methods: {
    loadUserInfo() {
      // Récupérer les informations utilisateur du localStorage
      const userData = localStorage.getItem('user')
      if (userData) {
        this.user = JSON.parse(userData)
      }
    },
    logout() {
      // Supprimer les données utilisateur
      localStorage.removeItem('user')
      // Rediriger vers la page de login
      this.$router.push('/')
    },
    handleArchive() {
      this.showArchiveModal = true
    },
    handleDuplicate() {
      this.showDuplicateModal = true
    },
    showSuccessAlert(data) {
      this.alertType = 'success'
      this.alertTitle = data.title
      this.alertMessage = data.message
      this.showAlert = true
      

      setTimeout(() => {
        this.showAlert = false
      }, 3000)
    },
    showErrorAlert(data) {
      this.alertType = 'error'
      this.alertTitle = data.title
      this.alertMessage = data.message
      this.showAlert = true
      

      setTimeout(() => {
        this.showAlert = false
      }, 5000)
    }
  }
}
</script>

<style scoped>
:root {
  --space-cadet: #2b2d42;
  --cool-gray: #8d99ae;
  --antiflash-white: #edf2f4;
  --red-pantone: #ef233c;
  --fire-engine-red: #d90429;
}

.main-view {
  min-height: 100vh;
  background: linear-gradient(135deg, var(--antiflash-white) 0%, var(--cool-gray) 100%);
}

nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.2rem 2rem;
  background: rgba(255, 255, 255, 0.95);
  border-bottom: 1px solid rgba(43, 45, 66, 0.08);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  position: relative;
}

.logo {
  display: flex;
  align-items: center;
}

.logo img {
  height: 50px;
  width: auto;
  object-fit: contain;
  filter: brightness(1.1) contrast(1.1);
}

.nav-center {
  flex: 1;
  text-align: center;
}

.main-title {
  color: var(--space-cadet);
  margin: 0;
  font-size: 1.4rem;
  font-weight: 600;
  letter-spacing: -0.025em;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 1rem;
  background: rgba(43, 45, 66, 0.04);
  border-radius: 8px;
  border: 1px solid rgba(43, 45, 66, 0.08);
  transition: all 0.2s ease;
}

.user-info:hover {
  background: rgba(43, 45, 66, 0.06);
  border-color: rgba(43, 45, 66, 0.12);
}

.user-avatar {
  width: 32px;
  height: 32px;
  background: var(--space-cadet);
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.user-avatar svg {
  width: 16px;
  height: 16px;
}

.user-details {
  display: flex;
  flex-direction: column;
}

.user-name {
  color: var(--space-cadet);
  font-weight: 500;
  font-size: 0.875rem;
  line-height: 1.2;
}

.last-login {
  color: var(--cool-gray);
  font-size: 0.75rem;
  line-height: 1.2;
}

.main {
  padding: 2rem 0 0 0;
  position: relative;
  z-index: 1;
}

.banner {
  margin: 0 2rem;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(43, 45, 66, 0.2);
  height: 500px;
  position: relative;
}

.carousel-container {
  width: 100%;
  height: 100%;
  position: relative;
  overflow: hidden;
}

.carousel {
  display: flex;
  height: 100%;
  transition: transform 0.5s ease-in-out;
}

.carousel-slide {
  min-width: 100%;
  height: 100%;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.carousel-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  position: absolute;
  top: 0;
  left: 0;
  z-index: 1;
}
.carousel-controls {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 10px;
  z-index: 3;
}

.carousel-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  border: 2px solid var(--antiflash-white);
  background: transparent;
  cursor: pointer;
  transition: all 0.3s ease;
}

.carousel-dot.active {
  background: var(--antiflash-white);
}

.carousel-dot:hover {
  background: var(--antiflash-white);
  transform: scale(1.2);
}

.modification-options {
  display: flex;
  justify-content: center;
  gap: 2rem;
  padding: 2rem;
  flex-wrap: wrap;
}

.option {
  background: var(--space-cadet);
  border-radius: 15px;
  padding: 2rem;
  min-width: 250px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.option:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(43, 45, 66, 0.3);
}

.option a {
  color: var(--antiflash-white);
  text-decoration: none;
  font-size: 1.2rem;
  font-weight: 600;
}

@media (max-width: 768px) {
  nav {
    flex-direction: column;
    gap: 1.5rem;
    padding: 1.5rem;
  }
  
  .nav-center {
    order: -1;
    padding: 0.8rem 1.5rem;
  }
  
  .main-title {
    font-size: 1.3rem;
  }
  
  .nav-links {
    flex-direction: row;
    justify-content: space-between;
    width: 100%;
    gap: 1.5rem;
  }
  
  .user-info {
    padding: 0.6rem 1.2rem;
    gap: 1rem;
  }
  
  .user-avatar {
    width: 40px;
    height: 40px;
  }
  
  .user-avatar svg {
    width: 20px;
    height: 20px;
  }
  
  .user-name {
    font-size: 0.85rem;
  }
  
  .last-login {
    font-size: 0.7rem;
  }
  
  .carousel-content h2 {
    font-size: 1.8rem;
  }
  
  .carousel-content p {
    font-size: 1rem;
  }
  
  .carousel-content {
    padding: 1.5rem;
  }
  
  .main {
    padding: 1rem 0 0 0;
  }
  
  .banner {
    margin: 0 1rem;
    height: 350px;
  }
}

.Btn {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  
  width: 45px;
  height: 45px;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition-duration: .3s;
  box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.199);
  background-color: rgb(255, 65, 65);
  outline: none; /* Enlever l'outline du bouton */
}

.Btn:focus {
  outline: none; /* Enlever l'outline au focus */
}


.sign {
  width: 100%;
  transition-duration: .3s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.sign svg {
  width: 17px;
}

.sign svg path {
  fill: white;
}

.text {
  position: absolute;
  right: 0%;
  width: 0%;
  opacity: 0;
  color: white;
  font-size: 1.1em;
  font-weight: 550;
  transition-duration: .3s;
}

.Btn:hover {
  width: 130px;
  border-radius: 40px;
  transition-duration: .3s;
   background-color: var(--red-pantone);
}

.Btn:hover .sign {
  width: 30%;
  transition-duration: .3s;
  padding-left: 20px;
}

.Btn:hover .text {
  opacity: 1;
  width: 70%;
  transition-duration: .3s;
  padding-right: 15px;
}

.Btn:active {
  transform: translate(2px ,2px);
}
</style>
