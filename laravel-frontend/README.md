# 📚 Frontend Vue.js - Gestion de Librairie SOAP

Interface utilisateur Vue.js pour le système SOAP de gestion de livres.

## ✨ Fonctionnalités

- **CREATE** - Ajouter un nouveau livre
- **READ ALL** - Charger tous les livres
- **READ ONE** - Rechercher un livre par ID
- **UPDATE** - Modifier un livre existant
- **DELETE** - Supprimer un livre

## 🚀 Installation

```bash
# Installer les dépendances
npm install

# Démarrer le serveur de développement
npm run dev

# Construire pour la production
npm build

# Prévisualiser la build
npm run preview
```

## 🌐 Configuration

Par défaut, le frontend communique avec le backend sur:
```
http://localhost:8000/soap
```

Pour modifier cette URL, éditez `src/App.vue`:
```javascript
soapUrl: 'http://localhost:XXXX/soap'
```

## 📁 Structure

```
src/
├── main.js          # Point d'entrée Vue.js
└── App.vue          # Component principal avec toutes les opérations CRUD

index.html           # Template HTML
vite.config.js       # Configuration Vite
package.json         # Dépendances Node.js
```

## 🛠️ Stack

- **Vue.js 3** - Framework réactif
- **Vite** - Build tool et dev server
- **JavaScript Vanilla** - Pas de dépendances supplémentaires

## 🔗 Communication SOAP

Le frontend envoie des requêtes SOAP XML au backend:

```xml
POST /soap
Content-Type: text/xml; charset=utf-8
SOAPAction: urn:BookService#getAllBooks

<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope 
    xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" 
    xmlns:ns1="urn:BookService">
    <SOAP-ENV:Body>
        <ns1:getAllBooks></ns1:getAllBooks>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

Les réponses contiennent du JSON encodé en string dans l'élément XML `<return>`:

```xml
<return>{"status":"success","data":[{"id":1,"title":"..."}]}</return>
```

## 📱 Interface Utilisateur

### Section 1: CREATE
- Formulaire pour créer un nouveau livre
- Champs: Titre (requis), Auteur, Année, Genre

### Section 2: READ ALL
- Bouton pour charger tous les livres
- Liste affichée en bas

### Section 3: READ ONE
- Recherche par ID
- Affiche un seul livre

### Section 4: UPDATE
- Formulaire pour modifier un livre (apparaît après sélection)
- Tous les champs modifiables

### Section 5: DELETE
- List des livres avec boutons MODIFIER et SUPPRIMER
- Confirmation avant suppression

## 🎨 Design

- Gradient violet/bleu en arrière-plan
- Cartes blanches avec ombres
- Badges colorés pour chaque opération
- Messages d'alerte contextualisés
- Design responsive

## 🔄 Workflow

1. Utilisateur interagit avec le formulaire
2. Frontend construit une enveloppe SOAP
3. Requête POST vers `/soap` du backend
4. Backend traite la requête et retourne une réponse SOAP
5. Frontend parse la réponse XML
6. Extrait le JSON de l'élément `<return>`
7. Affiche les données ou le message d'erreur

## 🐛 Débogage

Ouvrez la console du navigateur (F12) pour voir:
- Les requêtes SOAP complètes
- Les réponses XML
- Les messages d'alerte

## ⚙️ Configuration Vite

```javascript
{
  server: {
    port: 5173,
    host: 'localhost',
    open: true  // Ouvre automatiquement le navigateur
  }
}
```

## 🚀 Production

```bash
npm run build
```

Les fichiers optimisés sont générés dans `dist/`

---

**Prêt?** Lancez `npm run dev` et accédez à http://localhost:5173/
