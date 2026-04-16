class ApiService {
  constructor() {
    // Utilize environment variables from Vite, falback to localhost for dev just in case
    this.baseURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8080/simec-app/backend/api';
  }

  /**
   * Méthode de requête HTTP avec gestion d'erreurs
   */
  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    const config = { 
      mode: 'cors', 
      ...options, 
      headers: { 
        'Content-Type': 'application/json',
        ...options.headers 
      } 
    };

    try {
      const response = await fetch(url, config);
      const text = await response.text();

      if (!text.trim()) {
        return { 
          ok: response.ok, 
          status: response.status, 
          data: { records: [] }
        };
      }

      let data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        throw new Error(`Réponse JSON invalide: ${e.message}`);
      }

      return { 
        ok: response.ok, 
        status: response.status, 
        data 
      };

    } catch (error) {
      console.error('Erreur de requête:', error);
      throw error;
    }
  }

  /**
   * Récupération des demandes depuis l'API
   */
  async getDemandes() {
    try {
      const result = await this.request('/demandes/list.php', { method: 'GET' });

      if (!result.ok) {
        throw new Error(`Erreur HTTP ${result.status}: ${result.data?.error || 'Erreur inconnue'}`);
      }

      const data = result.data;
      let records = [];
      
      if (data && data.success && Array.isArray(data.records)) {
        records = data.records;
      }

      return {
        success: true,
        data: records,
        message: 'Demandes récupérées avec succès',
        count: records.length
      };

    } catch (error) {
      console.error('Erreur getDemandes:', error);
      return {
        success: false,
        data: [],
        message: `Erreur: ${error.message}`,
        error: error.message
      };
    }
  }

  /**
   * Récupération des couleurs
   */
  async getCouleurs() {
    try {
      const result = await this.request('/couleurs/list.php', { method: 'GET' });
      if (result.ok) {
        return { success: true, data: result.data.data || [] };
      } else {
        return { success: false, message: result.data.message || 'Échec récupération couleurs' };
      }
    } catch (error) {
      console.error('Erreur getCouleurs:', error);
      return { success: false, message: error.message };
    }
  }

  /**
   * Test de connectivité de l'API
   */
  /*
  async testConnection() {
    try {
      const response = await fetch(`${this.baseURL}/test.php`, {
        method: 'GET',
        mode: 'cors'
      });
      
      return {
        success: response.ok,
        status: response.status,
        message: response.ok ? 'Connexion OK' : 'Connexion échouée'
      };
      
    } catch (error) {
      console.error('Erreur test connexion:', error);
      return {
        success: false,
        error: error.message,
        message: 'Impossible de se connecter au serveur'
      };
    }
  } */

  /**
   * Authentification utilisateur
   */
  async login(username, password) {
    try {
      const result = await this.request('/auth/login.php', {
        method: 'POST',
        body: JSON.stringify({ username, password }),
      });
      
      if (result.ok) {
        return {
          success: true,
          user: result.data.user,
          message: result.data.message || 'Connexion réussie'
        };
      } else {
        return {
          success: false,
          message: result.data.message || 'Identifiants invalides',
          error: result.data.error || null
        };
      }
      
    } catch (error) {
      return {
        success: false,
        message: 'Erreur de connexion au serveur',
        error: error.message
      };
    }
  }

  /**
   * Mise à jour du statut d'une demande
   */
  async updateDemandeStatus(id, status) {
    try {
      const result = await this.request('/demandes/update_status.php', {
        method: 'POST',
        body: JSON.stringify({ id, status }),
      });
      
      if (result.ok) {
        return {
          success: true,
          data: result.data,
          message: 'Statut mis à jour avec succès'
        };
      } else {
        return {
          success: false,
          message: result.data.message || 'Échec mise à jour statut'
        };
      }
      
    } catch (error) {
      console.error('Échec mise à jour statut:', error);
      return {
        success: false,
        message: 'Échec mise à jour statut',
        error: error.message
      };
    }
  }


  /**
   * Exécution de la procédure de duplication pour une demande
   */
  async executeDuplicationProcedure(demandeId) {
    try {
      const result = await this.request('/demandes/duplication.php', {
        method: 'POST',
        body: JSON.stringify({ demande_id: demandeId }),
      });
      
      if (result.ok) {
        return {
          success: true,
          data: result.data,
          message: 'Procédure de duplication exécutée avec succès'
        };
      } else {
        return {
          success: false,
          message: result.data.message || 'Échec de l\'exécution de la procédure'
        };
      }
      
    } catch (error) {
      console.error('Erreur exécution procédure duplication:', error);
      return {
        success: false,
        message: 'Erreur lors de l\'exécution de la procédure',
        error: error.message
      };
    }
  }

  /**
   * Wrapper: exécute la procédure de duplication pour une demande
   */
  async executeDuplicationDemande(demandeId) {
    return this.executeDuplicationProcedure(demandeId);
  }

  /**
   * Création d'une nouvelle demande (archive ou duplication)
   */
  async createDemande(demandeData) {
    try {
      const result = await this.request('/demandes/create.php', {
        method: 'POST',
        body: JSON.stringify(demandeData)
      });
      const dbError = result.data.db_error || null;
      if (result.ok && result.data.success) {
        return { success: true, data: result.data, message: result.data.message || 'Demande créée avec succès' };
      } else {
        return { success: false, message: result.data.error || result.data.message || 'Échec création demande', db_error: dbError };
      }
    } catch (error) {
      console.error('Erreur createDemande:', error);
      return { success: false, message: error.message };
    }
  }

  /**
   * Recherche de familles (autocomplete)
   */
  async searchFamilles(query) {
    try {
      const result = await this.request(`/familles/search.php?q=${encodeURIComponent(query)}`, { method: 'GET' });
      if (result.ok && result.data.success) {
        return { success: true, data: result.data.data };
      } else {
        return { success: false, data: [] };
      }
    } catch (error) {
      console.error('Erreur searchFamilles:', error);
      return { success: false, data: [] };
    }
  }

  /**
   * Recherche d'articles par famille (autocomplete)
   */
  async searchArticlesByFamille(query, familleCode) {
    try {
      const result = await this.request(`/articles/search_by_famille.php?q=${encodeURIComponent(query)}&famille=${encodeURIComponent(familleCode)}`, { method: 'GET' });
      if (result.ok && result.data.success) {
        return { success: true, data: result.data.data };
      } else {
        return { success: false, data: [] };
      }
    } catch (error) {
      console.error('Erreur searchArticlesByFamille:', error);
      return { success: false, data: [] };
    }
  }
}

export default new ApiService();