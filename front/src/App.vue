<template>
  <div class="container">
    <h1>📚 Système CRUD Complet - Gestion de Librairie</h1>
    <p class="subtitle">✨ 5 Opérations CRUD avec Backend SOAP Laravel ✨</p>
    
    <div id="alerts">
      <div v-for="(alert, index) in alerts" :key="index" :class="['alert', `alert-${alert.type}`]">
        {{ alert.message }}
      </div>
    </div>

    <!-- OPÉRATION 1: CREATE -->
    <div class="crud-section">
      <h2><span class="operation-badge badge-create">1️⃣ CREATE</span> Ajouter un Nouveau Livre</h2>
      <form @submit.prevent="createBook">
        <div class="form-grid">
          <div class="form-group">
            <label for="title">📖 Titre *</label>
            <input type="text" id="title" v-model="form.title" required placeholder="Ex: Le Petit Prince">
          </div>
          <div class="form-group">
            <label for="author">✍️ Auteur</label>
            <input type="text" id="author" v-model="form.author" placeholder="Ex: Antoine de Saint-Exupéry">
          </div>
          <div class="form-group">
            <label for="year">📅 Année</label>
            <input type="number" id="year" v-model="form.published_year" min="1000" max="2100" placeholder="Ex: 1943">
          </div>
          <div class="form-group">
            <label for="genre">🎭 Genre</label>
            <input type="text" id="genre" v-model="form.genre" placeholder="Ex: Fiction">
          </div>
        </div>
        <button type="submit" class="btn-create">✅ CRÉER LE LIVRE</button>
      </form>
    </div>

    <!-- OPÉRATION 2: READ ALL -->
    <div class="crud-section">
      <h2><span class="operation-badge badge-read">2️⃣ READ ALL</span> Afficher Tous les Livres</h2>
      <button @click="loadAllBooks" class="btn-read">📚 CHARGER TOUS LES LIVRES</button>
    </div>

    <!-- OPÉRATION 3: READ ONE -->
    <div class="crud-section">
      <h2><span class="operation-badge badge-search">3️⃣ READ ONE</span> Rechercher un Livre par ID</h2>
      <div style="display: flex; gap: 10px; align-items: end;">
        <div class="form-group" style="flex: 1;">
          <label for="searchId">🔍 ID du Livre</label>
          <input type="number" id="searchId" v-model="searchId" min="1" placeholder="Ex: 1">
        </div>
        <button @click="searchBook" class="btn-search">🔎 RECHERCHER</button>
      </div>
    </div>

    <!-- OPÉRATION 4: UPDATE -->
    <div class="crud-section" v-if="showUpdateForm" id="updateSection">
      <h2><span class="operation-badge badge-update">4️⃣ UPDATE</span> Modifier un Livre</h2>
      <form @submit.prevent="updateBook">
        <div class="form-grid">
          <div class="form-group">
            <label for="updateTitle">📖 Titre *</label>
            <input type="text" id="updateTitle" v-model="updateFormData.title" required>
          </div>
          <div class="form-group">
            <label for="updateAuthor">✍️ Auteur</label>
            <input type="text" id="updateAuthor" v-model="updateFormData.author">
          </div>
          <div class="form-group">
            <label for="updateYear">📅 Année</label>
            <input type="number" id="updateYear" v-model="updateFormData.published_year" min="1000" max="2100">
          </div>
          <div class="form-group">
            <label for="updateGenre">🎭 Genre</label>
            <input type="text" id="updateGenre" v-model="updateFormData.genre">
          </div>
        </div>
        <div style="display: flex; gap: 10px;">
          <button type="submit" class="btn-update">✅ SAUVEGARDER</button>
          <button type="button" @click="cancelUpdate" class="btn-delete">❌ ANNULER</button>
        </div>
      </form>
    </div>

    <!-- OPÉRATION 5: DELETE -->
    <div class="crud-section">
      <h2><span class="operation-badge badge-delete">5️⃣ DELETE</span> Liste des Livres</h2>
      <p style="color: #666; margin-bottom: 15px;">
        ℹ️ Les boutons MODIFIER et SUPPRIMER apparaissent sur chaque livre ci-dessous
      </p>
      <div class="books-list">
        <p v-if="books.length === 0" style="color: #999; text-align: center;">Aucun livre trouvé</p>
        <div v-for="book in books" :key="book.id" class="book-item">
          <div class="book-info">
            <div class="book-title">{{ book.title }}</div>
            <div class="book-details">
              👤 {{ book.author || 'N/A' }} | 
              📅 {{ book.published_year || 'N/A' }} | 
              🎭 {{ book.genre || 'N/A' }} | 
              🆔 {{ book.id }}
            </div>
          </div>
          <div class="book-actions">
            <button @click="showUpdateForm = true; updateFormData = {...book}" class="btn-update">✏️ MODIFIER</button>
            <button @click="deleteBook(book.id)" class="btn-delete">🗑️ SUPPRIMER</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BooksApp',
  data() {
    return {
      baseUrl: 'http://localhost:8000',
      soapUrl: 'http://localhost:8000/soap',
      form: {
        title: '',
        author: '',
        published_year: '',
        genre: ''
      },
      updateFormData: {
        id: null,
        title: '',
        author: '',
        published_year: '',
        genre: ''
      },
      showUpdateForm: false,
      searchId: '',
      books: [],
      alerts: []
    }
  },
  methods: {
    showAlert(message, type = 'success') {
      this.alerts.push({ message, type });
      console.log(`[${type.toUpperCase()}] ${message}`);
      setTimeout(() => {
        this.alerts.shift();
      }, 5000);
    },
    
    escapeXml(str) {
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&apos;');
    },

    async soapRequest(method, params = {}) {
      console.log(`[SOAP] ${method}`, params);
      
      const paramsXml = Object.entries(params)
        .filter(([key, value]) => value !== '' && value !== null && value !== undefined)
        .map(([key, value]) => `<${key}>${this.escapeXml(value)}</${key}>`)
        .join('');

      const soapEnvelope = `<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope 
    xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" 
    xmlns:ns1="urn:BookService"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
    SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
    <SOAP-ENV:Body>
        <ns1:${method}>${paramsXml}</ns1:${method}>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>`;

      try {
        const response = await fetch(this.soapUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'text/xml; charset=utf-8',
            'SOAPAction': `urn:BookService#${method}`
          },
          body: soapEnvelope
        });

        const text = await response.text();
        console.log('[SOAP Response]', text);

        if (!response.ok) {
          throw new Error(`HTTP ${response.status}`);
        }

        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(text, 'text/xml');
        const returnElement = xmlDoc.getElementsByTagName('return')[0];
        
        if (!returnElement) {
          throw new Error('Pas de réponse valide');
        }

        return JSON.parse(returnElement.textContent);
      } catch (error) {
        console.error('[SOAP ERROR]', error);
        this.showAlert(`Erreur: ${error.message}`, 'error');
        throw error;
      }
    },

    async createBook() {
      const params = {
        title: this.form.title,
        author: this.form.author || '',
        published_year: this.form.published_year || '',
        genre: this.form.genre || ''
      };

      try {
        const response = await this.soapRequest('createBook', params);
        
        if (response.status === 'success') {
          this.showAlert(`✅ CREATE: Livre créé avec succès! (ID: ${response.data.id})`, 'success');
          this.form = { title: '', author: '', published_year: '', genre: '' };
          await this.loadAllBooks();
        } else {
          this.showAlert(`❌ CREATE: ${response.message}`, 'error');
        }
      } catch (error) {
        this.showAlert(`❌ CREATE: Erreur de création`, 'error');
      }
    },

    async loadAllBooks() {
      try {
        this.showAlert('📚 READ ALL: Chargement...', 'info');
        const response = await this.soapRequest('getAllBooks');
        
        if (response.status !== 'success') {
          throw new Error(response.message || 'Erreur');
        }

        this.books = response.data || [];
        this.showAlert(`✅ READ ALL: ${this.books.length} livre(s) chargé(s)`, 'success');
      } catch (error) {
        this.showAlert('❌ READ ALL: Erreur de chargement', 'error');
      }
    },

    async searchBook() {
      if (!this.searchId) {
        this.showAlert('⚠️ SEARCH: Entrez un ID', 'error');
        return;
      }

      try {
        this.showAlert(`🔍 SEARCH: Recherche du livre ID ${this.searchId}...`, 'info');
        const response = await this.soapRequest('getBook', { id: this.searchId });
        
        if (response.status === 'success') {
          this.books = [response.data];
          this.showAlert(`✅ SEARCH: Livre trouvé!`, 'success');
        } else {
          this.showAlert(`❌ SEARCH: ${response.message}`, 'error');
        }
      } catch (error) {
        this.showAlert('❌ SEARCH: Erreur de recherche', 'error');
      }
    },

    async updateBook() {
      const params = {
        id: this.updateFormData.id,
        title: this.updateFormData.title,
        author: this.updateFormData.author || '',
        published_year: this.updateFormData.published_year || '',
        genre: this.updateFormData.genre || ''
      };

      try {
        const response = await this.soapRequest('updateBook', params);
        
        if (response.status === 'success') {
          this.showAlert(`✅ UPDATE: Livre ID ${this.updateFormData.id} modifié!`, 'success');
          this.cancelUpdate();
          await this.loadAllBooks();
        } else {
          this.showAlert(`❌ UPDATE: ${response.message}`, 'error');
        }
      } catch (error) {
        this.showAlert('❌ UPDATE: Erreur de modification', 'error');
      }
    },

    cancelUpdate() {
      this.showUpdateForm = false;
      this.updateFormData = { id: null, title: '', author: '', published_year: '', genre: '' };
    },

    async deleteBook(id) {
      if (!confirm(`🗑️ Êtes-vous sûr de vouloir supprimer le livre ID ${id}?`)) return;

      try {
        const response = await this.soapRequest('deleteBook', { id: id });
        
        if (response.status === 'success') {
          this.showAlert(`✅ DELETE: Livre ID ${id} supprimé!`, 'success');
          await this.loadAllBooks();
        } else {
          this.showAlert(`❌ DELETE: ${response.message}`, 'error');
        }
      } catch (error) {
        this.showAlert('❌ DELETE: Erreur de suppression', 'error');
      }
    }
  }
}
</script>
