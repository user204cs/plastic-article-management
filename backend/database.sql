-- Table des utilisateurs de test
CREATE TABLE users (
    id INT IDENTITY(1,1) PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    login VARCHAR(100) UNIQUE NOT NULL,
    mdp VARCHAR(512) NOT NULL,
    profil TINYINT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT GETDATE(),
    updated_at DATETIME NOT NULL DEFAULT GETDATE()
);
GO

-- Table des demandes 
CREATE TABLE demandes (
    id INT IDENTITY(1,1) PRIMARY KEY,
    AR_ref VARCHAR(100) NOT NULL,
    demandeur INT NOT NULL,
    code_racine VARCHAR(50) NOT NULL,
    type_demande TINYINT NOT NULL,
    famille VARCHAR(100) NULL,
    AR_Design VARCHAR(255) NOT NULL,
    AR_Design_Duplication VARCHAR(255) NULL,
    statut VARCHAR(20) NOT NULL DEFAULT 'en_attente',
    couleur VARCHAR(50) NOT NULL DEFAULT '',
    GenererBF TINYINT NOT NULL DEFAULT 2,
    created_at DATETIME NOT NULL DEFAULT GETDATE(),
    CONSTRAINT FK_demandes_users FOREIGN KEY (demandeur) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_statut CHECK (statut IN ('en_attente','valide','rejete'))
);
GO

-- Insertion d'utilisateurs de test
INSERT INTO users (nom, login, mdp, profil) VALUES
('Administrateur Test', 'admin', 'password', 1),
('Utilisateur Test', 'user1', 'password', 0),
('Mohamed Benali', 'mbenali', 'password', 0),
('Fatima Zahra', 'fzahra', 'password', 1);

INSERT INTO demandes (AR_ref, demandeur, code_racine, type_demande, famille, AR_Design, AR_Design_Duplication) VALUES
('REF-2025-001', 2, 'SP-30x40-001', 0, NULL, 'Sac Plastique Standard - 30x40cm', NULL),
('REF-2025-002', 3, 'FPB-500-001', 1, 'emballage', 'Film Plastique Bio - Rouleau 500m', 'Film Plastique Alimentaire - Rouleau 500m'),
('REF-2025-003', 2, 'CPH-2L-001', 0, NULL, 'Conteneur Plastique Hermétique - 2L', NULL);
GO