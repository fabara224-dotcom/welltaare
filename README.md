# COMPLEXE WELLTAARÉ COLLECTION — site web (Laravel 12)

Univers dédié à la mode, à la créativité et au design : robes élégantes, tenues de soirée,
mode africaine homme et femme, créations sur mesure, studio d'infographie et de design graphique, paiement **Orange Money** (+ paiement à la livraison en option) et espace d'administration privé.

## Installation (ordinateur ou serveur)

Prérequis : PHP 8.2+ (extensions pdo_mysql ou pdo_sqlite, mbstring, gd, curl, fileinfo), Composer.

```bash
composer install
cp .env.example .env
php artisan key:generate
# Base de données : SQLite par défaut (database/database.sqlite) — ou MySQL, voir plus bas
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
php artisan serve          # http://127.0.0.1:8000
```

`php artisan migrate --seed` crée le compte admin et les catégories. **Le catalogue est vide** :
c'est l'admin qui ajoute tous les produits (Admin → + Ajouter un produit).
(`SEED_DEMO=true` dans `.env` ajoute des produits d'exemple, supprimables en un clic dans l'espace développeur.)

### MySQL (hébergement)
Dans `.env` :
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=welltaare
DB_USERNAME=...
DB_PASSWORD=...
```
Sur l'hébergement, le dossier public du domaine doit pointer vers `public/`.
Mettez `APP_ENV=production`, `APP_DEBUG=false` et `APP_URL=https://votre-domaine.com`.

## Connexion unique (clients + administration)

Une seule page de connexion pour tout le monde : **`/connexion`** (lien « Connexion » du site).
- Un **client** crée son compte (`/inscription`), se connecte et arrive sur **Mon compte** : ses commandes,
  ses demandes sur mesure / design, ses coordonnées (préremplies à la commande) et son mot de passe.
- Un **administrateur** ou **développeur** se connecte sur la même page et arrive directement sur l'espace de gestion.
- L'espace de gestion est **invisible** : aucun lien sur le site, et `/gestion` renvoie une page « introuvable » (404)
  à toute personne qui n'est pas administrateur (visiteur ou client connecté).
- Les clients peuvent toujours commander sans compte.

## WhatsApp après commande
Après une commande, une demande sur mesure ou une demande de design, WhatsApp s'ouvre automatiquement
avec un message déjà rédigé (référence, articles, total, adresse, paiement) adressé à la boutique.
Le client n'a plus qu'à appuyer sur « Envoyer ». Numéro par défaut : **+221 77 127 25 67**, modifiable dans
Admin → Paramètres → WhatsApp.

## Vos vraies photos
- **Admin → Apparence & photos** : logo, grandes photos de l'accueil (Femme / Homme), les 4 univers, l'atelier,
  le studio graphique, la page Le Complexe, les pages connexion et contact.
- **Admin → Catégories** : cliquez sur la vignette pour ajouter la photo de chaque catégorie.
- **Admin → Produits** : plusieurs photos par produit (réduites automatiquement pour le téléphone).
Sans photo, un motif noir & or s'affiche : le site reste toujours présentable.

## Espace admin (caché)

Aucun bouton ni lien vers l'admin n'apparaît sur le site. L'adresse est :

**`https://votre-site.com/connexion`** (la même page que les clients)

Après connexion, l'admin arrive directement sur son tableau de bord (`/gestion`).

- Identifiants initiaux : `ADMIN_EMAIL` / `ADMIN_PASSWORD` du `.env` (par défaut `admin@welltaare.com` / `ChangezMoi2026!`).
  **Changez-les avant `migrate --seed`, puis modifiez le mot de passe dans Admin → Paramètres.**
- Pour rendre l'adresse de gestion plus discrète, changez `ADMIN_PATH=gestion` (ex. `ADMIN_PATH=espace-wt-2026`).
- 5 essais de connexion par minute maximum ; les pages admin ne sont pas indexées par Google.

## Faire évoluer le site
Le code est organisé par domaine (boutique, sur mesure, design graphique, comptes, paiement, admin, développeur) :
un nouveau service = un modèle + un contrôleur + des vues, sans toucher au reste. Les textes, prix de livraison,
contacts et photos se changent depuis l'admin, sans code.

L'admin permet de :
- voir les ventes (jour, mois, encaissé Orange Money), meilleures ventes, stocks faibles ;
- gérer les **produits** : ajout / modification / suppression, **plusieurs photos** (glisser-déposer), photo principale,
  prix, prix barré (promo), stock, tailles, couleurs, vedette, nouveau, visible/masqué ;
- gérer les **catégories** ;
- traiter les **commandes** : statut (Nouvelle → Confirmée → En préparation → En livraison → Livrée / Annulée),
  statut du paiement, note interne, vérification du paiement Orange Money, WhatsApp / appel du client.
  L'annulation remet les articles en stock ;
- traiter les **demandes sur mesure** : mesures, photo d'inspiration, devis, suivi de la couture ;
- traiter les **demandes d'infographie** (logo, flyers, cartes de visite, bâches, visuels réseaux sociaux…) : fichier joint, devis, suivi ;
- régler les **paramètres** : téléphone, WhatsApp, adresse, horaires, frais de livraison Conakry / régions,
  livraison offerte, paiement à la livraison, réseaux sociaux, bandeau d'annonce.

## Rôles : administrateur et développeur

| Rôle | Accès |
|---|---|
| **Administrateur** | Boutique : produits et photos, catégories, commandes, sur mesure, infographie, paramètres |
| **Développeur** | Tout ce que fait l'admin **+ l'espace développeur** |

Créer votre compte développeur :
```bash
php artisan welltaare:developpeur votre@email.com --name="Votre nom"
```
(le mot de passe est demandé). Ou bien renseignez `DEV_EMAIL` / `DEV_PASSWORD` dans `.env` avant `php artisan db:seed`.

Connexion sur `/connexion` comme tout le monde ; le menu **Développeur** apparaît dans la barre latérale.

### Espace développeur (`/gestion/dev`)
- **Diagnostic** : base de données, lien des photos, dossiers, clé APP_KEY, mode debug, limite d'envoi PHP,
  Orange Money, migrations en attente — avec bouton « Corriger » quand c'est possible ;
- **Journal des erreurs** : erreurs du site (date, niveau, message, détail technique), filtres, recherche, vidage ;
- **Outils** : vider les caches, optimiser, réparer les photos, recréer les dossiers, mettre à jour la base,
  tester Orange Money, supprimer les produits de démo, **mode maintenance** (les visiteurs voient « Site en maintenance »,
  vous gardez l'accès) ;
- **Comptes & accès** : créer des comptes admin pour votre équipe, changer un rôle, réinitialiser un mot de passe,
  bloquer ou supprimer un compte ;
- **Base de données** : consultation en lecture seule de toutes les tables (mots de passe et jetons masqués).

En cas de bug : ouvrez **Journal des erreurs**, lisez le message et le détail, corrigez le fichier indiqué,
puis **Vider les caches**. Pour une grosse correction, activez le **mode maintenance** pendant le travail.

## Mettre à jour un site déjà installé
Remplacez les fichiers du projet par la nouvelle version **en gardant** `.env`, `vendor/`, `database/database.sqlite`
et `storage/app/public/` (les photos), puis :
```bash
composer install
php artisan migrate
php artisan optimize:clear
```

## Paiement Orange Money

Le site utilise l'API **Orange Money Web Payment** : le client valide sa commande, il est redirigé vers la page
sécurisée Orange Money, puis revient sur le site. Le paiement est **toujours revérifié auprès d'Orange**
(API `transactionstatus`) avant que la commande passe à « Payé ».

1. Ouvrez un compte marchand Orange Money Guinée et un compte sur developer.orange.com (API « Orange Money Web Pay »).
2. Récupérez l'en-tête d'autorisation (`Basic ...`) et la `merchant_key`.
3. Renseignez dans `.env` :
```
ORANGE_MONEY_ENABLED=true
ORANGE_MONEY_MODE=sandbox            # live en production
ORANGE_MONEY_AUTH_HEADER="Basic ..."
ORANGE_MONEY_MERCHANT_KEY=...
ORANGE_MONEY_TOKEN_URL=https://api.orange.com/oauth/v3/token
ORANGE_MONEY_BASE_URL=https://api.orange.com/orange-money-webpay/dev/v1   # URL de production fournie par Orange
ORANGE_MONEY_CURRENCY=OUV            # devise de test ; GNF en production
```
4. Adresse de notification à déclarer chez Orange : `https://votre-site.com/paiement/orange-money/notification`
   (aussi affichée dans Admin → Paramètres).

Tant que les identifiants ne sont pas renseignés, les commandes Orange Money sont enregistrées avec
« paiement en attente » et le client est prévenu que la boutique le rappelle : vous pouvez encaisser manuellement.

Code concerné : `app/Services/OrangeMoney.php`, `app/Http/Controllers/PaymentController.php`.

## Côté client
- Accueil, collections Femme / Homme, catégories, recherche, tri, nouveautés, promotions
- Fiche produit : galerie photos, tailles, couleurs, quantité, « Acheter maintenant »
- Panier, commande (frais de livraison automatiques Conakry / régions), Orange Money ou espèces
- Page de confirmation avec suivi de l'avancement
- Formulaire **Création sur mesure** (mesures, tissu, budget, date, photo d'inspiration)
- Page **Le Complexe** (présentation et vision : élégance, identité, innovation)
- Page **Infographie & design graphique** avec demande de devis et envoi de fichier
- **Suivi** par numéro (commande WT-, sur mesure SM-, design GD-) + téléphone
- Charte noir & or, bouton WhatsApp flottant, page contact, site adapté au mobile

## Structure
```
app/Http/Controllers/        boutique, panier, commande, paiement, sur mesure, suivi
app/Http/Controllers/Admin/  connexion, tableau de bord, produits, catégories, commandes, sur mesure, paramètres
app/Models/                  Product, ProductImage, Category, Order, OrderItem, DesignRequest, Setting, User
app/Services/                Cart (panier en session), OrangeMoney
resources/views/shop/        pages publiques     resources/views/admin/  pages admin
public/css/shop.css, admin.css, public/js/shop.js
```
# welltaare
