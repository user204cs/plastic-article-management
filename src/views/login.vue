<template>
  <div class="login-container">
    <div class="login-slider">
      <!-- Section gauche avec logo SIMEC -->
      <div class="logo-section">
        <div class="logo-container">
          <div class="simple-wrapper">
            <img src="/assets/simec_groupe_richbond_logo.jpeg" alt="SIMEC Logo" class="clean-logo">
          </div>
          
          <div class="brand-info">
            <p class="brand-subtitle">Groupe Richbond</p>
            <div class="simple-tagline">Innovation • Excellence • Performance</div>
          </div>
          
          <div class="decorative-elements">
          </div>
        </div>
      </div>
      
      <!-- Section droite avec formulaire -->
      <div class="form-section">
        <form class="form" @submit.prevent="handleLogin">
          <p id="heading">Se connecter</p>
          
          <div class="field">
            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 2.57-3.643zm-7.177.704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914z"></path>
            </svg>
            <input 
              v-model="username"
              autocomplete="off" 
              placeholder="Username" 
              class="input-field" 
              type="text"
              required
            >
          </div>
          
          <div class="field">
            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
            </svg>
            <input 
              v-model="password"
              placeholder="Password" 
              class="input-field" 
              type="password"
              required
            >
          </div>
          
          <!-- Message d'erreur -->
          <div v-if="errorMessage" class="error-message">
            {{ errorMessage }}
          </div>
          
          <div class="btn">
            <button type="submit" class="button1" :disabled="isLoading">
              <span v-if="isLoading">Connexion...</span>
              <span v-else>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;se connecter&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import ApiService from '../services/api.js'

export default {
  name: 'LoginView',
  data() {
    return {
      username: '',
      password: '',
      isLoading: false,
      errorMessage: ''
    }
  },
  methods: {
    async handleLogin() {
      if (!this.username || !this.password) {
        this.errorMessage = 'Veuillez remplir tous les champs'
        return
      }
      
      this.isLoading = true
      this.errorMessage = ''
      
      try {
        const result = await ApiService.login(this.username, this.password)
        
        if (result.success) {
          // Stocker les informations utilisateur
          localStorage.setItem('user', JSON.stringify(result.user))
          
          // Redirection basée sur le rôle
          if (parseInt(result.user.role) === 1) {
            // Admin (profil = 1) → vue admin
            this.$router.push('/admin')
          } else {
            // Utilisateur normal (profil = 0) → vue principale
            this.$router.push('/main')
          }
        } else {
          this.errorMessage = result.message || 'Erreur de connexion'
        }
      } catch (error) {
        console.error('Erreur de connexion:', error)
        this.errorMessage = 'Erreur de connexion au serveur'
      } finally {
        this.isLoading = false
      }
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

.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, var(--antiflash-white) 0%, var(--cool-gray) 100%);
  padding: 20px;
}

.login-slider {
  display: flex;
  width: 100%;
  max-width: 900px;
  min-height: 500px;
  border-radius: 25px;
  overflow: hidden;
  box-shadow: 0 20px 40px rgba(43, 45, 66, 0.4);
}

/* Section gauche - Logo SIMEC */
.logo-section {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, var(--space-cadet) 0%, #1e2036 100%);
  padding: 2rem;
  gap: 1.5rem;
  position: relative;
  overflow: hidden;
}

.logo-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: transparent;
}

.logo-container {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2rem;
}

.simple-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
}

.clean-logo {
  width: 160px;
  height: 120px;
  object-fit: contain;
  background: white;
  padding: 24px;
  border-radius: 12px;
}

.brand-info {
  text-align: center;
  color: var(--antiflash-white);
  z-index: 2;
}

.simple-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin: 0 0 0.5rem 0;
  letter-spacing: 0.1em;
  color: var(--antiflash-white);
}

.simple-tagline {
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.7);
  font-weight: 300;
  letter-spacing: 0.02em;
  margin-top: 1rem;
}

.brand-subtitle {
  font-size: 1.1rem;
  margin: 0 0 1rem 0;
  color: var(--cool-gray);
  font-weight: 300;
  letter-spacing: 0.05em;
}

/* Section droite - Formulaire */
.form-section {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, var(--antiflash-white) 0%, var(--cool-gray) 100%);
  padding: 2rem;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding-left: 2em;
  padding-right: 2em;
  padding-bottom: 0.4em;
  background-color: var(--space-cadet);
  border-radius: 25px;
}

#heading {
  text-align: center;
  margin: 2em;
  color: var(--antiflash-white);
  font-size: 1.2em;
  font-weight: 600;
}

.field {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5em;
  border-radius: 25px;
  padding: 0.6em;
  border: none;
  outline: none;
  color: var(--antiflash-white);
  background-color: var(--space-cadet);
  box-shadow: inset 2px 5px 10px rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(141, 153, 174, 0.2);
}

.field:focus-within {
  border-color: var(--cool-gray);
  box-shadow: inset 2px 5px 10px rgba(0, 0, 0, 0.3), 0 0 0 2px rgba(141, 153, 174, 0.3);
}

.input-icon {
  height: 1.3em;
  width: 1.3em;
  fill: var(--cool-gray);
}

.input-field {
  background: none;
  border: none;
  outline: none;
  width: 100%;
  color: var(--antiflash-white);
}

.input-field::placeholder {
  color: var(--cool-gray);
}

.form .btn {
  display: flex;
  justify-content: center;
  flex-direction: row;
  margin-top: 2.5em;
  gap: 0.5em;
}

.button1 {
  padding: 0.5em;
  padding-left: 1.1em;
  padding-right: 1.1em;
  border-radius: 5px;
  border: none;
  outline: none;
  background-color: var(--red-pantone);
  color: var(--antiflash-white);
  font-weight: 600;
  cursor: pointer;
  margin-bottom:15px;
}

.button1:hover {
  background-color: var(--fire-engine-red);
}


@media (max-width: 768px) {
  .login-slider {
    flex-direction: column;
    max-width: 100%;
    min-height: 100vh;
    border-radius: 0;
  }
  
  .logo-section {
    min-height: 35vh;
    padding: 1.5rem;
  }
  
  .simple-logo {
    width: 80px;
    height: 80px;
    padding: 10px;
  }
  
  .rotating-ring {
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
  }
  
  .logo-glow {
    top: -5px;
    left: -5px;
    right: -5px;
    bottom: -5px;
  }
  
  .brand-title {
    font-size: 1.8rem;
  }
  
  .brand-subtitle {
    font-size: 0.9rem;
  }
  
  .brand-tagline {
    font-size: 0.8rem;
  }
  
  .geometric-shape {
    display: none; /* Masquer sur mobile pour éviter l'encombrement */
  }
  
  .form-section {
    min-height: 65vh;
    padding: 1.5rem;
  }
  
  .form {
    padding-left: 1.5em;
    padding-right: 1.5em;
  }
  
  .btn {
    flex-direction: column;
    gap: 10px;
  }
  
  .button2 {
    padding-left: 1.1em;
    padding-right: 1.1em;
  }
}

.error-message {
  color: var(--fire-engine-red);
  background-color: rgba(217, 4, 41, 0.1);
  border: 1px solid var(--fire-engine-red);
  padding: 0.5rem;
  border-radius: 8px;
  text-align: center;
  font-size: 0.9rem;
  margin: 0.5rem 0;
}

.button1:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  background-color: var(--cool-gray);
}

.button1:disabled:hover {
  background-color: var(--cool-gray);
  box-shadow: none;
  transform: none;
}
</style>