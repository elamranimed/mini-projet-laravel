# Copilot Instructions - Laravel SOAP Book Service

## Architecture SOAP (Non-REST)

Ce projet implémente un service web SOAP complet pour la gestion de livres. **N'utilisez JAMAIS de routes REST API** - toute communication se fait via SOAP/XML.

### Structure à Deux Contrôleurs

```
┌─────────────────────┐      ┌──────────────────────┐
│ SoapServerController│──────▶│ BookSoapController   │
│ (Infrastructure)    │      │ (Business Logic)     │
└─────────────────────┘      └──────────────────────┘
        │                              │
        ▼                              ▼
  - Génère WSDL                 - getAllBooks()
  - Gère SoapServer             - getBook($id)
  - Routes /soap                - createBook()
  - Route /soap/wsdl            - updateBook($id)
                                - deleteBook($id)
```

**[SoapServerController.php](app/Http/Controllers/SoapServerController.php)** :
- `handle()` : Instancie `\SoapServer` et délègue à `BookSoapController`
- `wsdl()` : Génère le WSDL inline (RPC/encoded style)
- WSDL embarqué directement dans le code (pas de fichier .wsdl séparé)

**[BookSoapController.php](app/Http/Controllers/BookSoapController.php)** :
- Méthodes SOAP publiques appelées par `SoapServer`
- Retourne du **JSON encodé en string** (pattern spécifique : `json_encode(['status' => ..., 'data' => ...])`)
- Le client JavaScript parse le XML SOAP puis le JSON interne

### Routes SOAP

```php
// routes/web.php
Route::post('/soap', [SoapServerController::class, 'handle']);
Route::get('/soap/wsdl', [SoapServerController::class, 'wsdl']);
```

**NE PAS créer** de routes `Route::get('/api/books')` ou similaires - ce projet n'utilise pas REST.

## Modèle et Base de Données

**SQLite par défaut** (config dans [.env.example](.env.example)) :
```env
DB_CONNECTION=sqlite
```

**[Book.php](app/Models/Book.php)** : Eloquent standard avec `$fillable` et `$casts`

Migration : [2026_01_12_165844_create_books_table.php](database/migrations/2026_01_12_165844_create_books_table.php)
- Colonnes : `id`, `title`, `author` (nullable), `published_year`, `genre`, `timestamps`

## Workflow de Développement

### Lancer le Projet (WAMP64 sur Windows)

```bash
# Setup initial
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

# Démarrer le serveur
php artisan serve
# Accessible sur http://localhost:8000
```

### Tester le Service SOAP

**Via le script diagnostique** : [test_soap.php](test_soap.php)
```bash
php test_soap.php
```
Ce script teste :
1. Connexion base de données
2. Appels directs au contrôleur SOAP
3. Routes disponibles
4. Existence du fichier HTML client

**Via le client HTML** : [public/books-crud.html](public/books-crud.html)
- Interface complète avec les 5 opérations CRUD
- Constructions d'enveloppes SOAP en JavaScript
- Accessible via `http://localhost:8000/books-crud.html`

### Exemple de Requête SOAP

```xml
POST /soap
Content-Type: text/xml; charset=utf-8
SOAPAction: urn:BookService#getAllBooks

<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="..." xmlns:ns1="urn:BookService">
    <SOAP-ENV:Body>
        <ns1:getAllBooks></ns1:getAllBooks>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

Réponse SOAP contenant du JSON :
```xml
<return>{"status":"success","data":[{"id":1,"title":"..."}]}</return>
```

## Conventions de Code

### Pattern de Réponse SOAP

**Toujours utiliser** la méthode `respond()` :
```php
private function respond(array $payload): string
{
    return json_encode($payload);
}

// Usage
return $this->respond(['status' => 'success', 'data' => $books]);
return $this->respond(['status' => 'error', 'message' => '...']);
```

### Gestion des Paramètres Optionnels

```php
public function updateBook($id, $title = null, $author = null, $published_year = null, $genre = null)
{
    $data = array_filter([...], fn($v) => $v !== null);
    $book->update($data);
}
```

### Style WSDL

- **RPC/encoded** (pas document/literal)
- Namespace : `urn:BookService`
- SOAPAction : `urn:BookService#methodName`
- Types personnalisés définis dans `<xsd:schema>`

## Points d'Attention

1. **Pas de validation Laravel Form Request** - validation manuelle dans les méthodes SOAP
2. **Pas de JSON API Resources** - réponses JSON brutes encodées en string
3. **Encoding UTF-8 critique** : `'encoding' => 'UTF-8'` dans SoapServer et headers
4. **Cache WSDL désactivé** : `'cache_wsdl' => WSDL_CACHE_NONE` (dev uniquement)
5. **Output buffering requis** : `ob_start(); $server->handle(); ob_get_clean()` pour capturer la sortie SOAP

## Ajouter une Nouvelle Opération SOAP

1. Ajouter la méthode dans `BookSoapController`
2. Définir les messages dans le WSDL (`SoapServerController::wsdl()`)
3. Ajouter l'opération dans `<portType>` et `<binding>`
4. Mettre à jour le client HTML si nécessaire

Exemple :
```php
// BookSoapController.php
public function countBooks() {
    return $this->respond(['status' => 'success', 'count' => Book::count()]);
}
```

## Composer Scripts Utiles

```bash
composer setup   # Installation complète
composer test    # Tests PHPUnit
php artisan pint # Formatage code (Laravel Pint)
```

## Client HTML JavaScript

[books-crud.html](public/books-crud.html) utilise :
- `fetch()` pour envoyer des requêtes SOAP
- `DOMParser` pour parser les réponses XML
- `JSON.parse()` sur l'élément `<return>` pour extraire les données
- Fonction `escapeXml()` pour sécuriser les paramètres

**Pattern client** :
```javascript
const soapEnvelope = `<?xml version="1.0"?>...`;
const response = await fetch('/soap', {
    method: 'POST',
    headers: { 'Content-Type': 'text/xml', 'SOAPAction': 'urn:BookService#...' },
    body: soapEnvelope
});
const xmlDoc = parser.parseFromString(await response.text(), 'text/xml');
const data = JSON.parse(xmlDoc.getElementsByTagName('return')[0].textContent);
```
