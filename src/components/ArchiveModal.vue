<template>
  <div v-if="show" class="modal-overlay" @click="closeModal">
    <div class="modal-container" @click.stop>
      <div class="modal-hero">
        <img src="/assets/simec_groupe_richbond_logo.jpeg" alt="Simec Groupe Richbond" class="hero-logo">
        <h3>Archiver un Produit Plastique</h3>
        <button class="close-btn" @click="closeModal">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>
      <form @submit.prevent="submitForm" class="modal-form">
       
        <div class="form-group autocomplete-container">
          <label for="designation">Désignation Article</label>
          <input
            type="text"
            id="designation"
            v-model="form.designation"
            @input="fetchSuggestions"
            @focus="showSuggestions = true"
            placeholder="Tapez la désignation de l'article..."
            autocomplete="off"
            required
          />
          
          <!-- Select dropdown qui se met à jour -->
          <select 
            v-if="form.suggestions.length > 0" 
            @change="selectFromDropdown($event)"
            class="suggestions-select"
            size="5"
          >
            <option value="">-- Choisir un article --</option>
            <option 
              v-for="item in form.suggestions" 
              :key="item.ref" 
              :value="JSON.stringify(item)"
            >
              {{ item.designation }} ({{ item.ref }})
            </option>
          </select>
        </div>
      
      <div class="form-actions">
        <button type="button" class="btn-secondary" @click="closeModal">Annuler</button>
        <button type="button" class="btn-primary" @click="submitForm">Archiver</button>
      </div>
      </form>
    </div>
  </div>
</template>

<script>
import ApiService from '../services/api.js'

export default {
  name: 'ArchiveModal',
  props: {
    show: {
      type: Boolean,
      default: false
    }
  },
data() {
    return {
      form: {
        designation: '',      // Ce que l'utilisateur tape
        referenceCode: '',    // Référence sélectionnée pour submission
        suggestions: []       // Liste des suggestions d'autocomplete
      },
      showSuggestions: false,
      isLoading: false
    }
  },
  methods: {
    closeModal() {
      this.$emit('close')
      this.resetForm()
    },
    async fetchSuggestions() {
      console.log('Recherche pour:', this.form.designation); // Debug
      
      if (!this.form.designation || this.form.designation.length < 2) {
        this.form.suggestions = [];
        return;
      }
      
      try {
        const res = await ApiService.request(
          `/articles/search.php?q=${encodeURIComponent(this.form.designation)}`,
          { headers: {} }
        );
        console.log('Réponse API:', res); // Debug
        if (res.ok && res.data.success) {
          this.form.suggestions = res.data.data;
          console.log('Suggestions:', this.form.suggestions); // Debug
        } else {
          this.form.suggestions = [];
        }
      } catch (error) {
        console.error('Erreur API:', error); // Debug
        this.form.suggestions = [];
      }
    },
    selectFromDropdown(event) {
      if (event.target.value) {
        const item = JSON.parse(event.target.value);
        this.selectArticle(item);
        event.target.value = ''; // Reset select
      }
    },
    selectArticle(item) {
      this.form.designation = item.designation;
      this.form.referenceCode = item.ref;
      this.form.suggestions = [];
      console.log('Article sélectionné:', item); // Debug
    },
    async submitForm() {
      if (this.isLoading) return
      this.isLoading = true
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}')
        const demandeurName = user.name || user.login || ''
  const result = await ApiService.createDemande({
          type: 0,
          reference: this.form.referenceCode,
          demandeur: demandeurName
        })
        
  console.log('createDemande result:', result);
  if (result.success) {
          this.$emit('success', {
            title: 'Demande d\'Archivage Envoyée!',
            message: `La demande d'archivage pour référence "${this.form.referenceCode}" a été envoyée avec succès.`
          })
          this.closeModal()
        } else {
          this.$emit('error', {
            title: 'Erreur',
            message: result.message || 'Erreur lors de la création de la demande'
          })
        }
      } catch (error) {
        this.$emit('error', {
          title: 'Erreur de connexion',
          message: 'Impossible de se connecter au serveur: ' + error.message
        })
      } finally {
        this.isLoading = false
      }
    },
    resetForm() {
      this.form = { designation: '', referenceCode: '', suggestions: [] }
      this.showSuggestions = false
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
/* Styles pour autocomplete */
.autocomplete-container {
  position: relative;
}
.suggestions-select {
  width: 100%;
  margin-top: 0.5rem;
  padding: 0.5rem;
  border: 2px solid var(--space-cadet);
  border-radius: 8px;
  background: white;
  font-size: 0.9rem;
  max-height: 150px;
  overflow-y: auto;
}
.suggestions-select option {
  padding: 0.5rem;
}
.suggestions-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #ccc;
  border-top: none;
  max-height: 200px;
  overflow-y: auto;
  z-index: 1000;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.suggestion-item {
  padding: 0.5rem 1rem;
  cursor: pointer;
}
.suggestion-item:hover {
  background-color: #f5f5f5;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  backdrop-filter: blur(8px);
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.modal-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow: hidden;
  animation: slideUp 0.3s ease-out;
  display: flex;
  flex-direction: column;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #f3f4f6;
  background: #fafafa;
}

.modal-hero {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1.5rem 2rem;
  background: linear-gradient(135deg, var(--space-cadet) 0%, #3a3d5a 100%);
  position: relative;
  border-radius: 12px 12px 0 0;
}

.hero-logo {
  width: 80px;
  height: auto;
  object-fit: contain;
  margin-bottom: 0.75rem;
  filter: brightness(1.2) contrast(1.1);
  background: rgba(255, 255, 255, 0.95);
  padding: 0.4rem;
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.modal-hero h3 {
  color: white;
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
  text-align: center;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.close-btn {
  background: rgba(255, 255, 255, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: white;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 6px;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  position: absolute;
  top: 1rem;
  right: 1rem;
  backdrop-filter: blur(10px);
}

.close-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: rgba(255, 255, 255, 0.5);
  transform: scale(1.05);
}

.close-btn svg {
  width: 20px;
  height: 20px;
}

.modal-form {
  padding: 2.5rem 2rem 1.5rem;
  background: white;
  overflow-y: auto;
  flex: 1;
  max-height: calc(90vh - 200px);
}

.modal-form::-webkit-scrollbar {
  width: 6px;
}

.modal-form::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

.modal-form::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.modal-form::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

.form-group {
  margin-bottom: 2rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.75rem;
  color: var(--space-cadet);
  font-weight: 600;
  font-size: 0.95rem;
  letter-spacing: 0.025em;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 1rem 1.25rem;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 0.95rem;
  transition: all 0.2s ease;
  background: white;
  color: var(--space-cadet);
  box-sizing: border-box;
  font-weight: 500;
}

.form-group input::placeholder,
.form-group textarea::placeholder {
  color: #9ca3af;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--space-cadet);
  box-shadow: 0 0 0 3px rgba(43, 45, 66, 0.1);
  transform: translateY(-1px);
}

.autocomplete-container {
  position: relative;
}

.suggestions-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 2px solid var(--space-cadet);
  border-top: none;
  border-radius: 0 0 10px 10px;
  max-height: 200px;
  overflow-y: auto;
  z-index: 1000;
  box-shadow: 0 4px 12px rgba(43, 45, 66, 0.15);
}

.suggestion-item {
  padding: 0.75rem 1.25rem;
  cursor: pointer;
  transition: background-color 0.2s ease;
  font-size: 0.95rem;
  font-weight: 500;
  color: var(--space-cadet);
}

.suggestion-item:hover {
  background-color: rgba(43, 45, 66, 0.1);
}

.suggestion-item:last-child {
  border-radius: 0 0 8px 8px;
}

.form-group textarea {
  resize: vertical;
  min-height: 100px;
  font-family: inherit;
}

.form-actions {
  display: flex !important;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 0;
  padding: 1.5rem 2rem;
  border-top: 1px solid #f3f4f6;
  background: white;
  position: relative;
  z-index: 10;
  flex-shrink: 0;
}

.btn-primary,
.btn-secondary {
  padding: 1rem 2rem !important;
  border: 2px solid;
  border-radius: 8px;
  font-weight: 700 !important;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 1rem !important;
  min-width: 130px;
  text-align: center;
  line-height: 1.25;
  display: inline-block !important;
  position: relative;
  z-index: 11;
}

.btn-primary {
  background: #ef233c !important;
  border-color: #ef233c !important;
  color: white !important;
  box-shadow: 0 4px 8px 0 rgba(239, 35, 60, 0.3) !important;
}

.btn-primary:hover {
  background: #d90429 !important;
  border-color: #d90429 !important;
  box-shadow: 0 6px 12px 0 rgba(239, 35, 60, 0.4) !important;
  transform: translateY(-2px);
}

.btn-secondary {
  background: #6b7280 !important;
  border-color: #6b7280 !important;
  color: white !important;
  box-shadow: 0 4px 8px 0 rgba(107, 114, 128, 0.3) !important;
}

.btn-secondary:hover {
  background: #4b5563 !important;
  border-color: #4b5563 !important;
  box-shadow: 0 6px 12px 0 rgba(107, 114, 128, 0.4) !important;
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .modal-container {
    width: 95%;
    margin: 1rem;
  }
  
  .modal-form {
    padding: 1.5rem;
  }
  
  .modal-header {
    padding: 1rem 1.5rem;
  }
  
  .form-actions {
    flex-direction: column;
  }
}
</style>
