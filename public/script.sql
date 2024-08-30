--- script de création de table ---

CREATE TABLE groupe_utilisateur (
    grp_id INTEGER NOT NULL,
    grp_libelle VARCHAR2(255) NOT NULL,
    grp_niveau INTEGER NOT NULL,
    PRIMARY KEY (grp_id)
);
alter table UTILISATEUR MODIFY DT_AJOUT DEFAULT SYSDATE;
alter table UTILISATEUR ADD COLUMN ROLES NOT NULL CLOB;
    CREATE SEQUENCE grp_seq START WITH 1 INCREMENT BY 1;
    CREATE OR REPLACE TRIGGER grp_trig
    BEFORE INSERT ON groupe_utilisateur 
    FOR EACH ROW
    BEGIN
    SELECT grp_seq.NEXTVAL
    INTO   :new.grp_id
    FROM   dual;
    END;
    /

CREATE TABLE utilisateur (
    user_id INTEGER NOT NULL,
    user_matricule VARCHAR2(255) UNIQUE NOT NULL,
    dt_ajout DATE DEFAULT SYSDATE,
    grp_id INTEGER NOT NULL,
    PRIMARY KEY (user_id),
    FOREIGN KEY (grp_id) REFERENCES groupe_utilisateur(grp_id)
);
    CREATE SEQUENCE user_seq START WITH 1 INCREMENT BY 1;
    CREATE OR REPLACE TRIGGER user_trig
    BEFORE INSERT ON utilisateur 
    FOR EACH ROW
    BEGIN
    SELECT user_seq.NEXTVAL
    INTO   :new.user_id
    FROM   dual;
    END;
    /

CREATE TABLE compte_class(
   cpt_class_id INTEGER,
   cpt_class_classe VARCHAR2(255) ,
   PRIMARY KEY(cpt_class_id)
);
    CREATE SEQUENCE compte_class_seq START WITH 1 INCREMENT BY 1;
    CREATE OR REPLACE TRIGGER compte_class_trig
    BEFORE INSERT ON compte_class
    FOR EACH ROW
    BEGIN
    SELECT compte_class_seq.NEXTVAL
    INTO   :new.cpt_class_id
    FROM   dual;
    END;
    /

CREATE TABLE plan_compte (
    cpt_id INTEGER NOT NULL,
    cpt_class_id INTEGER NOT NULL,
    cpt_numero VARCHAR2(255) UNIQUE NOT NULL,
    cpt_libelle VARCHAR2(255) UNIQUE NOT NULL,
    PRIMARY KEY (cpt_id),
    FOREIGN KEY(cpt_class_id) REFERENCES compte_class(cpt_class_id)
);
    CREATE SEQUENCE plan_compte_seq START WITH 1 INCREMENT BY 1;
    CREATE OR REPLACE TRIGGER plan_compte_trig
    BEFORE INSERT ON plan_compte
    FOR EACH ROW
    BEGIN
    SELECT plan_compte_seq.NEXTVAL
    INTO   :new.cpt_id
    FROM   dual;
    END;
    /

CREATE TABLE demande_type (
    dm_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    cpt_id INTEGER NOT NULL,                    -- id du compte pour le motif du demande
    dm_type VARCHAR2(255) NOT NULL,             -- dm_0:decaissement, dm_1:approvisionnement
    dm_date DATE DEFAULT SYSDATE,
    dm_montant FLOAT NOT NULL,
    entity_code VARCHAR2(255) NOT NULL,               -- les code des comptes 51 Caisse
    dm_mode_paiement VARCHAR2(255) NOT NULL,    -- p_0:espece, p_1:RT
    ref_demande VARCHAR2(255) NOT NULL,         -- DM_(dm_id)
    dm_etat INTEGER NOT NULL,-- Etat initié:10, Etat refusé:20, Autorisé:30, Fonds Débloquée == RECU:40, Justifié(PJ Factures ok): 50, 60    
    PRIMARY KEY (dm_id),
    FOREIGN KEY (user_id) REFERENCES utilisateur(user_id),
    FOREIGN KEY (cpt_id) REFERENCES plan_compte(cpt_id)
);
    CREATE SEQUENCE dm_seq START WITH 1 INCREMENT BY 1;
    CREATE OR REPLACE TRIGGER dm_trig
    BEFORE INSERT ON demande_type
    FOR EACH ROW
    BEGIN
    SELECT dm_seq.NEXTVAL
    INTO   :new.dm_id
    FROM   dual;
    END;
    /

CREATE TABLE detail_demande_piece(
   det_dm_id INTEGER,
   det_dm_piece_url CLOB NOT NULL,
   det_dm_type_url INTEGER NOT NULL,
   det_dm_date DATE NOT NULL,
   dm_id INTEGER NOT NULL,
   PRIMARY KEY(det_dm_id),
   UNIQUE(det_dm_piece_url),
   FOREIGN KEY(dm_id) REFERENCES demande_type(dm_id)
);
    CREATE SEQUENCE det_dm_seq START WITH 1 INCREMENT BY 1;
    CREATE OR REPLACE TRIGGER det_dm_trig
    BEFORE INSERT ON detail_demande_piece
    FOR EACH ROW
    BEGIN
    SELECT det_dm_seq.NEXTVAL
    INTO   :new.det_dm_id
    FROM   dual;
    END;
    / 

CREATE TABLE log_etat_demande (
    log_dm_id INTEGER NOT NULL,
    dm_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    log_dm_date DATE DEFAULT SYSDATE,
    log_dm_observation CLOB DEFAULT '',
    dm_etat INTEGER NOT NULL,-- Etat initié:10, Etat refusé:20, Autorisé:30, Fonds Débloquée:40, Justifié(PJ Factures ok): 50
    PRIMARY KEY (log_dm_id),
    FOREIGN KEY (dm_id) REFERENCES demande_type(dm_id),
    FOREIGN KEY (user_id) REFERENCES utilisateur(user_id)
);
    CREATE SEQUENCE log_dm_seq START WITH 1 INCREMENT BY 1;
    CREATE OR REPLACE TRIGGER log_dm_trig
    BEFORE INSERT ON log_etat_demande
    FOR EACH ROW
    BEGIN
    SELECT log_dm_seq.NEXTVAL
    INTO   :new.log_dm_id
    FROM   dual;
    END;
    /
    
CREATE TABLE usage_cheque (
    chq_id INTEGER NOT NULL,
    dm_id INTEGER NOT NULL,
    cpt_id INTEGER NOT NULL,
    chq_montant FLOAT NOT NULL,
    chq_observation INTEGER NOT NULL, -- etat validé:1, etat annulé:0 (Ny annulé tsy azo validé)
    chq_beneficiaire VARCHAR2(255) NOT NULL,
    chq_date_usage DATE DEFAULT SYSDATE,
    PRIMARY KEY (chq_id),
    FOREIGN KEY (dm_id) REFERENCES demande_type(dm_id),
    FOREIGN KEY (cpt_id) REFERENCES plan_compte(cpt_id)
);
    CREATE SEQUENCE chq_seq START WITH 1 INCREMENT BY 1;
    CREATE OR REPLACE TRIGGER chq_trig
    BEFORE INSERT ON usage_cheque
    FOR EACH ROW
    BEGIN
    SELECT chq_seq.NEXTVAL
    INTO   :new.chq_id
    FROM   dual;
    END;
    /

CREATE TABLE chequier (
    chequier_id INTEGER NOT NULL,
    chequier_numero_debut VARCHAR2(255) UNIQUE NOT NULL,
    chequier_date_arrive DATE DEFAULT SYSDATE,
    chequier_nombre INTEGER NOT NULL,
    PRIMARY KEY (chequier_id),
);
    CREATE SEQUENCE chequier_seq START WITH 1 INCREMENT BY 1;
    CREATE OR REPLACE TRIGGER chequier_trig
    BEFORE INSERT ON usage_cheque
    FOR EACH ROW
    BEGIN
    SELECT chequier_seq.NEXTVAL
    INTO   :new.chequier_id
    FROM   dual;
    END;
    /

CREATE TABLE versement (
    versm_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    cpt_id INTEGER NOT NULL,
    versm_nom_remettant VARCHAR2(255) UNIQUE NOT NULL,
    versm_num_cin VARCHAR2(255) UNIQUE NOT NULL,
    adresse VARCHAR2(255) UNIQUE NOT NULL,
    versm_montant_total FLOAT NOT NULL,
    versm_date DATE DEFAULT SYSDATE,
    versm_piece_joint VARCHAR2(255) UNIQUE NOT NULL,
    ref_versm VARCHAR2(255) NOT NULL,         -- VR_(versm_id)
    PRIMARY KEY (versm_id),
    FOREIGN KEY (user_id) REFERENCES demande_type(user_id),
    FOREIGN KEY (cpt_id) REFERENCES plan_compte(cpt_id)
);
    CREATE SEQUENCE versm_seq START WITH 1 INCREMENT BY 1;
    CREATE OR REPLACE TRIGGER versm_trig
    BEFORE INSERT ON versement
    FOR EACH ROW
    BEGIN
    SELECT versm_seq.NEXTVAL
    INTO   :new.versm_id
    FROM   dual;
    END;
    /

CREATE TABLE transaction_type (
    trs_id INTEGER NOT NULL,
    trs_code VARCHAR2(255) UNIQUE NOT NULL, -- CE-001
    trs_definition CLOB NOT NULL,
    trs_desc CLOB DEFAULT '',
    PRIMARY KEY (trs_id),
);
    CREATE SEQUENCE trs_seq START WITH 1 INCREMENT BY 1;
    CREATE OR REPLACE TRIGGER trs_trig
    BEFORE INSERT ON transaction_type
    FOR EACH ROW
    BEGIN
    SELECT trs_seq.NEXTVAL
    INTO   :new.trs_id
    FROM   dual;
    END;
    /

CREATE TABLE detail_transaction_compte ( -- un type de transaction peut avoir plusieurs comptes d'ecritures 
    det_trs_cpt_id INTEGER NOT NULL,
    trs_id INTEGER NOT NULL,
    cpt_id INTEGER NOT NULL,
    PRIMARY KEY(det_trs_cpt_id),
    FOREIGN KEY(trs_id) REFERENCES transaction_type(trs_id),
    FOREIGN KEY(cpt_id) REFERENCES plan_compte(cpt_id)
);
    CREATE SEQUENCE trs_cpt_seq START WITH 1 INCREMENT BY 1;
    CREATE OR REPLACE TRIGGER trs_cpt_trig
    BEFORE INSERT ON detail_transaction_compte
    FOR EACH ROW
    BEGIN
    SELECT trs_cpt_seq.NEXTVAL
    INTO   :new.det_trs_cpt_id
    FROM   dual;
    END;
    /

CREATE TABLE evenement(
    evn_id INTEGER,
    evn_date TIMESTAMP,
    evn_observation CLOB,
    entity_code VARCHAR2(255) NOT NULL,               -- les code des comptes 51 Caisse
    evn_montant_dep FLOAT NOT NULL,
    evn_montant_pre FLOAT NOT NULL,
    nom_prestataire VARCHAR2(255) DEFAULT '',         -- pouvant être vide
    ref_operation VARCHAR2(50) ,                      -- dénormalisation provenant de versement ou demande ou autre opération
    trs_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    PRIMARY KEY(evn_id),
    UNIQUE(ref_operation),
    FOREIGN KEY(trs_id) REFERENCES transaction_type(trs_id),
    FOREIGN KEY(user_id) REFERENCES utilisateur(user_id)
);
    CREATE SEQUENCE evn_seq START WITH 1 INCREMENT BY 1;
    CREATE OR REPLACE TRIGGER evn_trig
    BEFORE INSERT ON evenement
    FOR EACH ROW
    BEGIN
    SELECT evn_seq.NEXTVAL
    INTO   :new.evn_id
    FROM   dual;
    END;
    /

CREATE TABLE mouvement(
   mvt_id INTEGER,
   mvt_montant FLOAT NOT NULL,
   mvt_nature INTEGER NOT NULL, -- debit:0, credit:1
   evn_id INTEGER NOT NULL,
   cpt_id INTEGER NOT NULL,
   PRIMARY KEY(mvt_id),
   FOREIGN KEY(evn_id) REFERENCES evenement(evn_id),
   FOREIGN KEY(cpt_id) REFERENCES plan_compte(cpt_id)
);
    CREATE SEQUENCE mvt_seq START WITH 1 INCREMENT BY 1;
    CREATE OR REPLACE TRIGGER mvt_trig
    BEFORE INSERT ON evenement
    FOR EACH ROW
    BEGIN
    SELECT mvt_seq.NEXTVAL
    INTO   :new.mvt_id
    FROM   dual;
    END;
    /

CREATE TABLE budget(
    budget_id INTEGER,
    budget_montant_total FLOAT NOT NULL,
    budget_annee INTEGER, -- 4 chiffres
    budget_type VARCHAR2(255) , -- Dépense, Fonctionnement, Investissement
    PRIMARY KEY(budget_id),
    UNIQUE(budget_annee)
);

CREATE TABLE detail_budget(
    det_budget_id INTEGER,
    det_budget_montant FLOAT NOT NULL,
    det_budget_cpt VARCHAR2(255)  NOT NULL,
    budget_id INTEGER NOT NULL,
    PRIMARY KEY(det_budget_id),
    FOREIGN KEY(budget_id) REFERENCES budget(budget_id)
);