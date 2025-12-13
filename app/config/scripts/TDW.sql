-- ============================================================
-- Base de données : TDW
-- Projet : Application Web de Gestion d'un Laboratoire Informatique Universitaire
-- Auteur : Melliti Abdelmalek
-- Date de création : 2025-12-04
-- ============================================================

-- 1. Database
CREATE DATABASE IF NOT EXISTS TDW CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE TDW;

-- ============================================================
--  2. users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    photo VARCHAR(255),
    grade VARCHAR(100) NOT NULL,
    poste VARCHAR(100),
    domaine_recherche VARCHAR(255),
    biographie TEXT,
    role ENUM('admin','enseignant-chercheur','doctorant','etudiant','invite') DEFAULT 'enseignant-chercheur',
    statut ENUM('actif','suspendu','inactif') DEFAULT 'actif',
    is_deleted BOOLEAN DEFAULT FALSE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion TIMESTAMP NULL
);

-- ============================================================
--  3. teams
-- ============================================================
CREATE TABLE IF NOT EXISTS teams (
    id_team INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) UNIQUE NOT NULL,
    description TEXT,
    thematique VARCHAR(255),
    chef_id INT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_deleted BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (chef_id) REFERENCES users(id_user) ON DELETE SET NULL
);

-- ============================================================
--  4. team_members
-- ============================================================
CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    usr_id INT NOT NULL,
    role_dans_equipe VARCHAR(100),
    date_adhesion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_deleted BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (team_id) REFERENCES teams(id_team) ON DELETE CASCADE,
    FOREIGN KEY (usr_id) REFERENCES users(id_user) ON DELETE CASCADE,
    UNIQUE KEY unique_team_member (team_id, usr_id)
);

-- ============================================================
--  5. projets
-- ============================================================
CREATE TABLE IF NOT EXISTS projets (
    id_projet INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    thematique VARCHAR(150),
    type_financement VARCHAR(100),
    statut ENUM('en_cours','termine','soumis') DEFAULT 'en_cours',
    responsable_id INT NOT NULL,
    date_debut DATE,
    date_fin DATE,
    fiche_detaillee VARCHAR(255),
    budget DECIMAL(15,2),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_deleted BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (responsable_id) REFERENCES users(id_user) ON DELETE CASCADE
);

-- ============================================================
--  6. projet_membres
-- ============================================================
CREATE TABLE IF NOT EXISTS projet_membres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projet_id INT NOT NULL,
    usr_id INT NOT NULL,
    role_projet VARCHAR(100),
    is_deleted BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (projet_id) REFERENCES projets(id_projet) ON DELETE CASCADE,
    FOREIGN KEY (usr_id) REFERENCES users(id_user) ON DELETE CASCADE,
    UNIQUE KEY unique_projet_membre (projet_id, usr_id)
);

-- ============================================================
--  7. partenaires
-- ============================================================
CREATE TABLE IF NOT EXISTS partenaires (
    id_partenaire INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(200) NOT NULL,
    type ENUM('universite','entreprise','organisme') NOT NULL,
    logo VARCHAR(255),
    site_web VARCHAR(255),
    description TEXT,
    pays VARCHAR(100),
    date_partenariat DATE,
    is_deleted BOOLEAN DEFAULT FALSE
);

-- ============================================================
--  8. projet_partenaires
-- ============================================================
CREATE TABLE IF NOT EXISTS projet_partenaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projet_id INT NOT NULL,
    partenaire_id INT NOT NULL,
    is_deleted BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (projet_id) REFERENCES projets(id_projet) ON DELETE CASCADE,
    FOREIGN KEY (partenaire_id) REFERENCES partenaires(id_partenaire) ON DELETE CASCADE,
    UNIQUE KEY unique_projet_partenaire (projet_id, partenaire_id)
);

-- ============================================================
--  9. types_publications
-- ============================================================
CREATE TABLE IF NOT EXISTS types_publications (
    id_type INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(150) UNIQUE NOT NULL
);

-- ============================================================
--  10. publications
-- ============================================================
CREATE TABLE IF NOT EXISTS publications (
    id_publication INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    type_publication_id INT,
    resume TEXT,
    annee INT NOT NULL,
    doi VARCHAR(150),
    lien_telechargement VARCHAR(255),
    fichier_pdf VARCHAR(255),
    projet_id INT,
    domaine VARCHAR(150),
    statut ENUM('publie','en_attente','rejete') DEFAULT 'en_attente',
    date_publication DATE,
    date_soumission TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_deleted BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (projet_id) REFERENCES projets(id_projet) ON DELETE SET NULL,
    FOREIGN KEY (type_publication_id) REFERENCES types_publications(id_type)
);

-- ============================================================
--  11. publication_auteurs
-- ============================================================
CREATE TABLE IF NOT EXISTS publication_auteurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    publication_id INT NOT NULL,
    usr_id INT NOT NULL,
    ordre_auteur INT DEFAULT 1,
    is_deleted BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (publication_id) REFERENCES publications(id_publication) ON DELETE CASCADE,
    FOREIGN KEY (usr_id) REFERENCES users(id_user) ON DELETE CASCADE,
    UNIQUE KEY unique_publication_auteur (publication_id, usr_id)
);

-- ============================================================
--  12. types_equipements
-- ============================================================
CREATE TABLE IF NOT EXISTS types_equipements (
    id_type INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(150) UNIQUE NOT NULL
);

-- ============================================================
--  13. equipements
-- ============================================================
CREATE TABLE IF NOT EXISTS equipements (
    id_equipement INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(200) NOT NULL,
    type_equipement_id INT,
    description TEXT,
    numero_serie VARCHAR(100),
    localisation VARCHAR(200),
    etat ENUM('libre','reserve','maintenance','hors_service') DEFAULT 'libre',
    capacite INT,
    date_acquisition DATE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_deleted BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (type_equipement_id) REFERENCES types_equipements(id_type)
);

-- ============================================================
--  14. reservations
-- ============================================================
CREATE TABLE IF NOT EXISTS reservations (
    id_reservation INT AUTO_INCREMENT PRIMARY KEY,
    equipement_id INT NOT NULL,
    usr_id INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    motif TEXT,
    statut ENUM('confirmee','annulee','terminee') DEFAULT 'confirmee',
    date_reservation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipement_id) REFERENCES equipements(id_equipement) ON DELETE CASCADE,
    FOREIGN KEY (usr_id) REFERENCES users(id_user) ON DELETE CASCADE
);

-- ============================================================
--  15. types_evenements
-- ============================================================
CREATE TABLE IF NOT EXISTS types_evenements (
    id_type INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(150) UNIQUE NOT NULL
);

-- ============================================================
--  16. evenements
-- ============================================================
CREATE TABLE IF NOT EXISTS evenements (
    id_evenement INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    type_evenement_id INT,
    description TEXT,
    lieu VARCHAR(200),
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    organisateur_id INT,
    capacite_max INT,
    externe BOOLEAN DEFAULT FALSE,
    statut ENUM('a_venir','en_cours','termine','annule') DEFAULT 'a_venir',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organisateur_id) REFERENCES users(id_user) ON DELETE SET NULL,
    FOREIGN KEY (type_evenement_id) REFERENCES types_evenements(id_type)
);

-- ============================================================
--  17. inscriptions_evenements
-- ============================================================
CREATE TABLE IF NOT EXISTS inscriptions_evenements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evenement_id INT NOT NULL,
    usr_id INT,
    nom VARCHAR(100),
    email VARCHAR(150),
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('inscrit','confirmee','annulee') DEFAULT 'inscrit',
    FOREIGN KEY (evenement_id) REFERENCES evenements(id_evenement) ON DELETE CASCADE,
    FOREIGN KEY (usr_id) REFERENCES users(id_user) ON DELETE SET NULL
);

-- ============================================================
--  18. types_actualites
-- ============================================================
CREATE TABLE IF NOT EXISTS types_actualites (
    id_type INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(150) UNIQUE NOT NULL
);

-- ============================================================
--  19. actualites
-- ============================================================
CREATE TABLE IF NOT EXISTS actualites (
    id_actualite INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    type_actualite_id INT,
    image VARCHAR(255),
    detail VARCHAR(255),
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    afficher_diaporama BOOLEAN DEFAULT FALSE,
    ordre_diaporama INT DEFAULT 0,
    FOREIGN KEY (type_actualite_id) REFERENCES types_actualites(id_type)
);

-- ============================================================
--  20. messages_contact
-- ============================================================
CREATE TABLE IF NOT EXISTS messages_contact (
    id_message INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    sujet VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    lu BOOLEAN DEFAULT FALSE,
    repondu BOOLEAN DEFAULT FALSE
);

-- ============================================================
--  21. offres
-- ============================================================
CREATE TABLE IF NOT EXISTS offres (
    id_offre INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    type ENUM('stage','these','bourse','collaboration','emploi','autre') NOT NULL,
    description TEXT NOT NULL,
    responsable_id INT,
    date_limite DATE,
    statut ENUM('ouverte','fermee','pourvue') DEFAULT 'ouverte',
    fichier_pdf VARCHAR(255),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_deleted BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (responsable_id) REFERENCES users(id_user) ON DELETE SET NULL
);

-- ============================================================
--  22. documents_personnels
-- ============================================================
CREATE TABLE IF NOT EXISTS documents_personnels (
    id_document INT AUTO_INCREMENT PRIMARY KEY,
    usr_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    type VARCHAR(100),
    fichier VARCHAR(255) NOT NULL,
    taille_fichier INT,
    date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usr_id) REFERENCES users(id_user) ON DELETE CASCADE
);

-- ============================================================
--  23. organigramme
-- ============================================================
CREATE TABLE IF NOT EXISTS organigramme (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usr_id INT NOT NULL,
    poste_hierarchique VARCHAR(150) NOT NULL,
    niveau INT DEFAULT 1,
    superieur_id INT,
    FOREIGN KEY (usr_id) REFERENCES users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (superieur_id) REFERENCES users(id_user) ON DELETE SET NULL
);

-- ============================================================
--  24. maintenances
-- ============================================================
CREATE TABLE IF NOT EXISTS maintenances (
    id_maintenance INT AUTO_INCREMENT PRIMARY KEY,
    equipement_id INT NOT NULL,
    type ENUM('preventive','corrective','reparation') NOT NULL,
    description TEXT,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME,
    technicien_id INT,
    cout DECIMAL(10,2),
    statut ENUM('planifiee','en_cours','terminee') DEFAULT 'planifiee',
    FOREIGN KEY (equipement_id) REFERENCES equipements(id_equipement) ON DELETE CASCADE,
    FOREIGN KEY (technicien_id) REFERENCES users(id_user) ON DELETE SET NULL
);

-- ============================================================
--  25. historique_equipements
-- ============================================================
CREATE TABLE IF NOT EXISTS historique_equipements (
    id_historique INT AUTO_INCREMENT PRIMARY KEY,
    equipement_id INT NOT NULL,
    usr_id INT NULL,
    action ENUM('reservation','annulation','debut_utilisation','fin_utilisation','maintenance','etat_change') NOT NULL,
    date_action DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipement_id) REFERENCES equipements(id_equipement) ON DELETE CASCADE,
    FOREIGN KEY (usr_id) REFERENCES users(id_user) ON DELETE SET NULL
);

-- ============================================================
-- Insertion de données initiales
-- ============================================================

INSERT INTO types_actualites (libelle)
VALUES ('Projet'),
       ('Publication'),
       ('Événement'),
       ('Soutenance'),
       ('Collaboration');

INSERT INTO types_evenements (libelle)
VALUES ('atelier'),
       ('seminaire'),
       ('conference'),
       ('soutenance');

INSERT INTO types_publications (libelle)
VALUES ('article'),
       ('rapport'),
       ('these'), 
       ('communication'),
       ('poster');

INSERT INTO types_equipements (libelle)
VALUES ('salles'), 
       ('serveurs'), 
       ('PC'),
       ('robots'), 
       ('imprimantes'), 
       ('capteurs')