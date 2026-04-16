<template>
  <div class="admin-view">
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
            <span class="user-name">{{ user.name || 'Administrateur' }}</span>
            <span class="last-login">{{ user.role === 1 ? 'Administrateur' : 'Utilisateur' }}</span>
          </div>
        </div>
        
        <button 
          class="Btn"
          :class="{ 'Btn-expanded': hovering }"
          @click="logout"
          @mouseenter="hovering = true"
          @mouseleave="hovering = false"
        >
          <div 
            class="sign"
            :class="{ 'sign-expanded': hovering }"
          >
            <svg viewBox="0 0 512 512"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path></svg>
          </div>
          <div 
            class="text"
            :class="{ 'text-expanded': hovering }"
          >
            Se déconnecter
          </div>
        </button>
      </div>
    </nav>
    
    <div class="admin-page">
      <div class="zellij-pattern"></div>
      <div class="content-overlay">
        <div class="statistics-container">
          <StatisticCard title="Demandes Validées" :count="validatedCount" />
          <StatisticCard title="Demandes Non Validées" :count="rejectedCount" />
          <StatisticCard title="Demandes En Attente" :count="pendingCount" />
        </div>
        
        <div class="actions-container">
          <div class="bulk-actions">
            <label class="bulk-select">
              <input type="checkbox" v-model="selectAll" @change="toggleSelectAll" />
              <span>Tout sélectionner</span>
            </label>
            <button class="btn-bulk-action" @click="bulkChangeStatus('validated')" :disabled="selectedDemands.length === 0">
              Valider
            </button>
            <button class="btn-bulk-action" @click="bulkChangeStatus('rejected')" :disabled="selectedDemands.length === 0">
              Rejeter
            </button>
          </div>
          
        </div>
        
        <div class="filters-container">
          <input type="date" v-model="dateFrom" class="filter-input" />
          <input type="date" v-model="dateTo" class="filter-input" />
          <input 
            v-model="search" 
            placeholder="Rechercher une demande..." 
            class="filter-input enhanced-search" 
          />
          <select v-model="filterStatus" class="filter-select">
            <option value="">Tous les Statuts</option>
            <option value="validated">Validées</option>
            <option value="pending">En Attente</option>
            <option value="rejected">Rejetées</option>
          </select>
        </div>
        
        <div class="requests-table-container">
          <div class="table-header">
            <h2>Liste des Demandes</h2>
            <div class="table-info">
              <span>{{ filteredDemands.length }} demande(s) trouvée(s)</span>
            </div>
          </div>
          
          <div class="table-wrapper">
            <table class="requests-table">
              <thead>
                <tr>
                  <th><input type="checkbox" v-model="selectAll" @change="toggleSelectAll" /></th>
                  <th @click="setSort('reference')">
                    Référence
                    <span v-if="sortKey==='reference'">{{ sortOrder==='asc'?'↑':'↓' }}</span>
                  </th>
                  <th @click="setSort('nom')">
                    Nom
                    <span v-if="sortKey==='nom'">{{ sortOrder==='asc'?'↑':'↓' }}</span>
                  </th>
                  <th @click="setSort('couleur')">
                    Couleur
                    <span v-if="sortKey==='couleur'">{{ sortOrder==='asc'?'↑':'↓' }}</span>
                  </th>
                  <th @click="setSort('demandeur')">
                    Demandeur
                    <span v-if="sortKey==='demandeur'">{{ sortOrder==='asc'?'↑':'↓' }}</span>
                  </th>
                  <th @click="setSort('type')">
                    Type de Demande
                    <span v-if="sortKey==='type'">{{ sortOrder==='asc'?'↑':'↓' }}</span>
                  </th>
                  <th @click="setSort('status')">
                    Statut
                    <span v-if="sortKey==='status'">{{ sortOrder==='asc'?'↑':'↓' }}</span>
                  </th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="demand in sortedDemands" :key="demand.id" class="table-row" >
                  <td><input type="checkbox" :value="demand.id" v-model="selectedDemands" /></td>
                  <td class="reference-cell">
                    <span class="reference-code">{{ demand.reference }}</span>
                  </td>
                  <td class="name-cell">
                    <span class="product-name">{{ demand.nom }}</span>
                  </td>
                  <td class="couleur-cell">
                    <span class="couleur-badge" :style="{ backgroundColor: demand.couleur }">
                      {{ demand.couleur }}
                    </span>
                  </td>
                  <td class="demandeur-cell">
                    <span class="demandeur-name">{{ demand.demandeur }}</span>
                  </td>
                  <td class="type-cell">
                    <span class="type-badge" :class="getTypeBadgeClass(demand.type)">
                      {{ demand.type }}
                    </span>
                  </td>
                  <td class="status-cell">
                    <span class="status-badge" :class="getStatusClass(demand.status)">
                      <span class="status-icon">{{ getStatusIcon(demand.status) }}</span>
                      {{ getStatusText(demand.status) }}
                    </span>
                  </td>
                  <td class="actions-cell">
                    <button class="action-btn validate-btn" @click="changeStatus(demand.id, 'validated')" title="Validé" :disabled="demand.status !== 'pending'">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </button>
                    <button class="action-btn reject-btn" @click="changeStatus(demand.id, 'rejected')" title="Rejeté" :disabled="demand.status !== 'pending'">
                      <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="20" height="20">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            
            <div v-if="filteredDemands.length === 0" class="empty-state">
              <div class="empty-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <h3>Aucune demande trouvée</h3>
              <p>Aucune demande ne correspond à vos critères de recherche.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import StatisticCard from '../components/StatisticCard.vue';
import ArchiveModal from '../components/ArchiveModal.vue';
import DuplicateModal from '../components/DuplicateModal.vue';
import api from '../services/api.js';

export default {
  name: 'AdminPage',
  components: {
    StatisticCard,
    ArchiveModal,
    DuplicateModal
  },
  async mounted() {
    const zellijElement = document.querySelector('.zellij-pattern');
    if (zellijElement) {
      zellijElement.style.backgroundImage = 'url("./assets/zelij.webp"), url("./assets/zellij.PNG")';
    }
    
    this.loadUserInfo();
    
    await this.loadDemandes();
  },
  data() {
    return {
      search: '',
      filterStatus: '',
      hovering: false,
      user: {},
      demands: [],
      loading: false
      ,showArchiveModal: false
      ,showDuplicateModal: false
      ,selectedDemand: null
     , selectedDemands: []
     , selectAll: false
     , sortKey: ''
     , sortOrder: 'asc'
     , dateFrom: ''
     , dateTo: ''
    };
  },
  computed: {
    validatedCount() {
      return this.demands.filter(d => d.status === 'validated').length;
    },
    rejectedCount() {
      return this.demands.filter(d => d.status === 'rejected').length;
    },
    pendingCount() {
      return this.demands.filter(d => d.status === 'pending').length;
    },
    filteredDemands() {
      return this.demands.filter(demand => {
        const okSearch = demand.nom.toLowerCase().includes(this.search.toLowerCase()) ||
                          demand.reference.toLowerCase().includes(this.search.toLowerCase());
        const okStatus = this.filterStatus ? demand.status===this.filterStatus : true;
        const date = new Date(demand.created_at);
        const fromOk = this.dateFrom ? date>=new Date(this.dateFrom) : true;
        const toOk = this.dateTo ? date<=new Date(this.dateTo) : true;
        return okSearch && okStatus && fromOk && toOk;
      });
    },
    sortedDemands() {
      const arr = [...this.filteredDemands];
      if (!this.sortKey) return arr;
      arr.sort((a,b) => {
        if (a[this.sortKey] < b[this.sortKey]) return this.sortOrder==='asc'? -1:1;
        if (a[this.sortKey] > b[this.sortKey]) return this.sortOrder==='asc'? 1:-1;
        return 0;
      });
      return arr;
    }
  },
  methods: {
   
    loadUserInfo() {
      const userData = localStorage.getItem('user')
      if (userData) {
        this.user = JSON.parse(userData)
      }
    },
    logout() {
      localStorage.removeItem('user');
      this.$router.push('/');
    },
    async loadDemandes() {
      try {
        const result = await api.getDemandes();
        this.demands = result.data;
      } catch (error) {
        console.error('Erreur chargement des demandes:', error);
        this.demands = [];
      }
    },
    // Retourne l'icône de statut
    getStatusIcon(status) {
      const icons = {
        validated: '●',
        rejected:  '●',
        pending:   '●'
      };
      return icons[status] || '●';
    },
    getTypeBadgeClass(type) {
      const classes = {
        'Archive': 'type-archive',
        'Duplication': 'type-duplication'
      };
      return classes[type] || 'type-default';
    },
    // Retourne la classe CSS pour le statut
    getStatusClass(status) {
      const classes = {
        'validated': 'status-validated',
        'rejected': 'status-rejected',
        'pending': 'status-pending'
      };
      return classes[status] || 'status-pending';
    },
    // Retourne le texte du statut
    getStatusText(status) {
      const texts = {
        'validated': 'Validé',
        'rejected': 'Rejeté',
        'pending': 'En attente'
      };
      return texts[status] || 'En attente';
    },
    // Open archive modal with selected demand
    openArchive(demand) {
      this.selectedDemand = demand;
      this.showArchiveModal = true;
    },
    // Open duplicate modal with selected demand
    openDuplicate(demand) {
      this.selectedDemand = demand;
      this.showDuplicateModal = true;
    },
    // Handle successful modal actions
    handleActionSuccess(payload) {
      this.showArchiveModal = false;
      this.showDuplicateModal = false;
      this.loadDemandes();
      // Optionally show notification
      // alert(payload.message);
    },
    // Change status of a demande
    async changeStatus(id, status) {
      // find the demand to check its type
      const demand = this.demands.find(d => d.id === id);

      // execute procedure for duplication requests when validating
      if (status === 'validated' && demand.type === 'Duplication') {
        const procResult = await api.executeDuplicationDemande(id);
        if (!procResult.success) {
          this.$emit('error', { title: 'Erreur', message: procResult.message });
          return;
        }
      }

      // update status as usual
      const result = await api.updateDemandeStatus(id, status);
      if (result.success) {
        this.loadDemandes();
      } else {
        this.$emit('error', { title: 'Erreur', message: result.message });
      }
    },
    setSort(key) {
      if (this.sortKey===key) this.sortOrder = this.sortOrder==='asc'?'desc':'asc';
      else { this.sortKey=key; this.sortOrder='asc'; }
    },
    toggleSelectAll() {
      if (this.selectAll) this.selectedDemands = this.filteredDemands.map(d=>d.id);
      else this.selectedDemands = [];
    },
    async bulkChangeStatus(status) {
      for (const id of this.selectedDemands) {
        await api.updateDemandeStatus(id, status);
      }
      this.loadDemandes();
      this.selectedDemands = [];
      this.selectAll = false;
    }
  }
};
</script>

<!-- Include modals -->
<ArchiveModal :show="showArchiveModal" @close="showArchiveModal=false" @success="handleActionSuccess" />
<DuplicateModal :show="showDuplicateModal" @close="showDuplicateModal=false" @success="handleActionSuccess" />

<style>
/* Basic styles for logout button */
.admin-view .Btn {
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
  box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.199);
  background-color: rgb(255, 65, 65);
  outline: none;
  transition: all 0.3s ease !important;
  pointer-events: auto !important;
  z-index: 999 !important;
}

.admin-view .Btn.Btn-expanded {
  width: 130px !important;
  border-radius: 40px !important;
  background-color: #ef233c !important;
}

.admin-view .Btn .sign {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease !important;
}

.admin-view .Btn .sign.sign-expanded {
  width: 30% !important;
  padding-left: 20px !important;
}

.admin-view .Btn .sign svg {
  width: 17px;
  fill: white;
}

.admin-view .Btn .text {
  position: absolute;
  right: 0%;
  width: 0%;
  opacity: 0;
  color: white;
  font-size: 0.8em;
  font-weight: 550;
  white-space: nowrap;
  transition: all 0.3s ease !important;
}

.admin-view .Btn .text.text-expanded {
  opacity: 1 !important;
  width: 70% !important;
  padding-right: 15px !important;
}
</style>

<style scoped>
.admin-view {
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow:visible;
  background-color: #f0f2f5;
}




:root {
  --space-cadet: #2b2d42;
  --cool-gray: #8d99ae;
  --antiflash-white: #edf2f4;
  --red-pantone: #ef233c;
  --fire-engine-red: #d90429;
}

.admin-view {
  min-height: 100vh;
  background: linear-gradient(135deg, var(--antiflash-white) 0%, var(--cool-gray) 100%);
}

.admin-page {
  padding: 2rem;
  min-height: 100vh;
  font-family: 'Inter','Segoe UI', Roboto, sans-serif;
  position: relative;
  overflow: visible;
}

.zellij-pattern {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
  opacity: 0.08;
  background-size: 300px 300px;
  background-repeat: repeat;
  background-position: center;
  background-attachment: fixed;
  background-color: #f8fafc;
  /* Fallback pattern if images don't load */
  background-image: 
    radial-gradient(circle at 25% 25%, rgba(43, 45, 66, 0.05) 2px, transparent 2px),
    radial-gradient(circle at 75% 75%, rgba(239, 35, 60, 0.05) 2px, transparent 2px),
    linear-gradient(45deg, transparent 40%, rgba(141, 153, 174, 0.03) 40%, rgba(141, 153, 174, 0.03) 60%, transparent 60%);
  background-size: 60px 60px, 60px 60px, 120px 120px;
}

.content-overlay {
  position: relative;
  z-index: 1;
  background: rgba(248, 250, 252, 0.92);
  backdrop-filter: blur(15px);
  border-radius: 16px;
  margin: 0.8rem;
  padding: 1.8rem;
  box-shadow: 
    0 4px 20px rgba(43, 45, 66, 0.08),
    inset 0 1px 0 rgba(255, 255, 255, 0.4);
  border: 1px solid rgba(255, 255, 255, 0.25);
}

.statistics-container {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 2.5rem;
  margin-bottom: 3rem;
  flex-wrap: wrap;
  padding: 1.5rem;
}

.actions-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.7);
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(43, 45, 66, 0.05);
}

.bulk-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.bulk-select {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 500;
  color: var(--space-cadet);
  cursor: pointer;
}

.btn-bulk-action {
  padding: 0.6rem 1.2rem;
  border: 2px solid var(--cool-gray);
  border-radius: 8px;
  background: transparent;
  color: var(--cool-gray);
  font-weight: 600;
  transition: all 0.2s ease;
}

.btn-bulk-action:hover:not(:disabled) {
  background: var(--cool-gray);
  color: white;
  transform: translateY(-1px);
}

.btn-bulk-action:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-clear-all {
  padding: 0.6rem 1.2rem;
  border: none;
  border-radius: 8px;
  background: var(--red-pantone);
  color: white;
  font-weight: 600;
  transition: all 0.2s ease;
}

.btn-clear-all:hover {
  background: var(--fire-engine-red);
  box-shadow: 0 4px 12px rgba(239, 35, 60, 0.3);
  transform: translateY(-1px);
}

.action-btn {
  border: none;
  padding: 2px;
  width: 24px;
  height: 24px;
  border-radius: 4px;
  cursor: pointer;
  margin: 0 2px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.action-btn svg {
  width: 16px;
  height: 16px;
}

.validate-btn {
  background: #2b8a3e;
  color: white;
}

.reject-btn {
  background: #ef233c;
  color: white;
}

.filters-container {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.filter-input, .filter-select {
  padding: 0.8rem 1.2rem;
  border: 2px solid rgba(43, 45, 66, 0.1);
  border-radius: 10px;
  font-size: 0.9rem;
  background: white;
  transition: all 0.25s ease;
  min-width: 200px;
}

.filter-input:focus, .filter-select:focus {
  outline: none;
  border-color: var(--space-cadet);
  box-shadow: 0 0 0 3px rgba(43, 45, 66, 0.1);
}

.enhanced-search {
  background: white url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%23666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"%3E%3Ccircle cx="11" cy="11" r="8"/%3E%3Cpath d="m21 21-4.35-4.35"/%3E%3C/svg%3E') no-repeat right 12px center;
  background-size: 18px;
  padding-right: 3rem;
}

.requests-table-container {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(43, 45, 66, 0.08);
  overflow: hidden;
  border: 1px solid rgba(43, 45, 66, 0.05);
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  background: linear-gradient(135deg, var(--space-cadet) 0%, #34394f 100%);
  color: white;
}

.table-header h2 {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 600;
}

.table-info {
  font-size: 0.9rem;
  opacity: 0.9;
}

.table-wrapper {
  overflow-x: auto;
}

.requests-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9rem;
}

.requests-table th {
  background: rgba(43, 45, 66, 0.04);
  padding: 1.2rem 1rem;
  text-align: left;
  font-weight: 600;
  color: var(--space-cadet);
  border-bottom: 2px solid rgba(43, 45, 66, 0.1);
  position: sticky;
  top: 0;
}

.requests-table td {
  padding: 1.2rem 1rem;
  border-bottom: 1px solid rgba(43, 45, 66, 0.05);
  vertical-align: middle;
}

.table-row {
  transition: background-color 0.2s ease;
}

.table-row:hover {
  background-color: rgba(43, 45, 66, 0.02);
}

.reference-code {
  font-family: 'Courier New', monospace;
  font-weight: 600;
  color: var(--space-cadet);
  background: rgba(43, 45, 66, 0.08);
  padding: 0.3rem 0.6rem;
  border-radius: 6px;
  font-size: 0.8rem;
}

.product-name {
  font-weight: 500;
  color: var(--space-cadet);
}

.demandeur-name {
  color: var(--cool-gray);
}

.type-badge {
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.type-archive {
  background: rgba(239, 35, 60, 0.1);
  color: var(--red-pantone);
}

.type-duplication {
  background: rgba(43, 45, 66, 0.1);
  color: var(--space-cadet);
}

.status-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-validated {
  background: rgba(34, 197, 94, 0.1);
  color: #059669;
}

.status-rejected {
  background: rgba(239, 35, 60, 0.1);
  color: var(--red-pantone);
}

.status-pending {
  background: rgba(245, 158, 11, 0.1);
  color: #d97706;
}

.status-icon {
  font-size: 0.6rem;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.action-button {
  padding: 0.5rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-button svg {
  width: 16px;
  height: 16px;
}

.validate-btn {
  background: rgba(34, 197, 94, 0.1);
  color: #059669;
}

.validate-btn:hover:not(:disabled) {
  background: rgba(34, 197, 94, 0.2);
  transform: scale(1.1);
}

.reject-btn {
  background: rgba(239, 35, 60, 0.1);
  color: var(--red-pantone);
}

.reject-btn:hover:not(:disabled) {
  background: rgba(239, 35, 60, 0.2);
  transform: scale(1.1);
}

.action-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: var(--cool-gray);
}

.empty-icon {
  margin: 0 auto 1.5rem;
  width: 64px;
  height: 64px;
  color: rgba(43, 45, 66, 0.3);
}

.empty-icon svg {
  width: 100%;
  height: 100%;
}

.empty-state h3 {
  margin: 0 0 0.5rem;
  color: var(--space-cadet);
  font-size: 1.2rem;
}

.empty-state p {
  margin: 0;
  font-size: 0.9rem;
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

.bulk-select {
  display: inline-block;
  margin-right: 1rem;
  color: var(--space-cadet);
  font-weight: 500;
}

/* Bulk actions container styling */
.actions-container {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

/* Bulk select checkbox label */
.bulk-select input {
  margin-right: 0.5rem;
}
.bulk-select {
  font-size: 0.95rem;
  color: var(--space-cadet);
  user-select: none;
}

/* Ensure buttons match UI size */
.actions-container .Btn {
  font-size: 0.95rem;
  padding: 0.6rem 1.2rem;
}
.couleur-badge {
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 400;
  color:rgb(0,0,0,0.9);
  text-shadow: 1px 1px 1px rgba(0,0,0,0.2);
}

.couleur-cell {
  text-align: left;
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
  
  .admin-page {
    padding: 1rem;
  }
  
  .content-overlay {
    margin: 0;
    padding: 1rem;
  }
  
  .statistics-container {
    gap: 1rem;
    padding: 1rem;
  }
  
  .filters-container {
    flex-direction: column;
    align-items: center;
  }
  
  .filter-input, .filter-select {
    min-width: 100%;
    max-width: 300px;
  }
  
  .table-header {
    flex-direction: column;
    gap: 0.5rem;
    text-align: center;
  }
  
  .requests-table {
    font-size: 0.8rem;
  }
  
  .requests-table th,
  .requests-table td {
    padding: 0.8rem 0.5rem;
  }
}
</style>
