# 🛠️ Gestion des interventions — OCP Khouribga

Application web (PHP / MySQL) de gestion des demandes d'intervention informatique : un utilisateur crée une demande, le responsable la valide et la planifie, un technicien la traite, et l'administrateur supervise l'ensemble.

## ✨ Fonctionnalités par rôle

| Rôle | Fonctionnalités |
|---|---|
| **Utilisateur** | Créer une demande, suivre ses demandes, notifications |
| **Responsable** | Valider / rejeter les demandes, attribuer et planifier les interventions, suivi, statistiques |
| **Technicien** (niveaux 1 à 3) | Voir ses interventions, mettre à jour le statut, ajouter un rapport / commentaire, inventaire partiel |
| **Administrateur** | Gestion des utilisateurs et des équipements, supervision des interventions, statistiques et exports, configuration |

## 🧰 Technologies

PHP 8 (PDO) · MySQL / MariaDB · HTML / CSS / JavaScript · Chart.js

## 📁 Structure du projet

```
gestion_interventions_ocp/
├── index.php                 # Page d'accueil publique
├── login.php / logout.php    # Connexion / déconnexion
├── dashboard.php             # Point d'entrée : charge le dashboard du rôle connecté
├── fiche_intervention.php    # Fiche détaillée d'une intervention
├── dashboards/               # Un fichier par rôle (admin, responsable, technicien, utilisateur)
├── classes/                  # Classes métier (Utilisateur, Demande, Intervention, ...)
├── includes/                 # config.php, session.php, header/footer/sidebar, fonctions
├── scripts/seed_users.php    # Création des comptes de démonstration
├── database/
│   ├── schema.sql            # Création de la base et des tables
│   └── sample_data.sql       # Données d'exemple (optionnel)
├── css/  js/  img/           # Ressources statiques
└── archive/                  # Anciens fichiers non utilisés (voir archive/README.md)
```

## 🚀 Installation (XAMPP / WAMP)

1. **Copier** le dossier `gestion_interventions_ocp` dans `htdocs` (XAMPP) ou `www` (WAMP).
2. **Démarrer** Apache et MySQL.
3. **Créer la base** : importer `database/schema.sql` dans phpMyAdmin (onglet *Importer*), ou en ligne de commande :
   ```bash
   mysql -u root < database/schema.sql
   ```
4. **Vérifier la connexion** à la base dans `includes/config.php` (par défaut : `root` sans mot de passe).
5. **Créer les comptes de démonstration** :
   ```bash
   php scripts/seed_users.php
   ```
6. *(Optionnel)* **Charger les données d'exemple** — à faire **après** l'étape 5, une seule fois :
   ```bash
   mysql -u root < database/sample_data.sql
   ```
7. Ouvrir <http://localhost/gestion_interventions_ocp/>

> L'ordre est important : `schema.sql` → `seed_users.php` → `sample_data.sql`.

## 🔑 Comptes de démonstration

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur | admin@ocp.ma | adminpass |
| Responsable | responsable@ocp.ma | resppass |
| Technicien niveau 1 / 2 / 3 | tech1@ocp.ma / tech2@ocp.ma / tech3@ocp.ma | tech1pass / tech2pass / tech3pass |
| Utilisateur | user@ocp.ma (aussi user2@, user3@) | userpass (user2pass, user3pass) |

⚠️ Comptes de démonstration uniquement : à changer avant tout usage réel.

## 🔒 Notes de sécurité

- Les mots de passe sont stockés **hachés** (`password_hash`). Les anciens mots de passe en clair restent acceptés à la connexion pour ne pas bloquer une base existante.
- Les identifiants MySQL sont dans `includes/config.php` : ne publiez pas de vrais identifiants de production.

## 👥 Auteurs

- Hajar Bellafrouh 
- stage d'initiation chez OPC Groupes
