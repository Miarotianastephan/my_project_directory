/*
    SCRIPT MILA ATAO
    ALTER TABLE utilisateur MODIFY DT_AJOUT DEFAULT SYSDATE;
    ALTER TABLE log_demande_type MODIFY LOG_DM_DATE DEFAULT SYSDATE;
    ALTER TABLE detail_demande_piece MODIFY det_dm_date DEFAULT SYSDATE;

*/

insert into groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, 'Admin', 0);
insert into groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, 'Responsable Commission', 10);
insert into groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, 'Secretaire Generale', 20);
insert into groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, 'Tresorier', 30);
insert into groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, 'Comptable', 40);
insert into groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, 'Commissaire aux Comptes', 50);

insert into demande (dm_id, libelle, dm_code) values (demande_seq.NEXTVAL, 'Decaissement', 10);
insert into demande (dm_id, libelle, dm_code) values (demande_seq.NEXTVAL, 'Approvisionnement' , 20);

insert into ce_budget_type (BUDGET_TYPE_ID, LIBELLE) values (budget_type_seq.NEXTVAL, 'Depense');
insert into ce_budget_type (BUDGET_TYPE_ID, LIBELLE) values (budget_type_seq.NEXTVAL, 'Investissement');
insert into ce_budget_type (BUDGET_TYPE_ID, LIBELLE) values (budget_type_seq.NEXTVAL, 'Fonctionnement');

INSERT INTO ce_banque (BANQUE_ID,NOM_BANQUE) VALUES (banque_seq.NEXTVAL,'BMOI');
insert into ce_banque (BANQUE_ID,NOM_BANQUE) VALUES (banque_seq.NEXTVAL,'BOA');
insert into ce_banque (BANQUE_ID,NOM_BANQUE) VALUES (banque_seq.NEXTVAL,'Baoba banque');
insert into ce_banque (BANQUE_ID,NOM_BANQUE) VALUES (banque_seq.NEXTVAL,'Societe generale');
insert into ce_banque (BANQUE_ID,NOM_BANQUE) VALUES (banque_seq.NEXTVAL,'BGFI Bank Madagascar');
insert into ce_banque (BANQUE_ID,NOM_BANQUE) VALUES (banque_seq.NEXTVAL,'MCB Madagascar');

-- Insertion de transaction 
insert into ce_transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-001','Encaissement Subvention BFM');

insert into ce_transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-002','Approvisionnement petite caissse Siège');

insert into ce_transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-003','Approvisionnement petite caissse RT');
-- Les paiements
insert into ce_transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-004','Paiement facture par chèque');
insert into ce_transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-005','Paiement facture en espèces sièges');
insert into ce_transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-006','Paiement facture en espèces RT');
insert into ce_transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-007','Dépense directe payée par BFM');

insert into ce_transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-010','Encaissement interêt opération');

insert into ce_transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-011','Comptabilisation des frais bancaires');

-- améliorations des états xxx
-- ETAT INITIEE 1xx
insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,100, 'Initié');
insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,101, 'Modifié');

-- ETAT ATTENTES 2xx
insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,200, 'Attente fonds');
insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,201, 'Attente modification');
insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,202, 'Attente versement');

-- ETAT REFUS 3xx
insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,300, 'Comptabilisé');
insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,301, 'Refusé');

-- ETAT AVANT FIN 4xx
insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,400, 'Justifié');
insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,401, 'Reversé');

-- alter table ce_detail_transaction_compte MODIFY is_trs_debit NUMBER(1) default 1;

commit; -- commit voalohany


-- AVANT INSERTION ANIRETO DETAIL TRANSACTION COMPTE 
-- IRETO DIA MI CONNECTE ADMIN @ LE APPLICATION ALOHA DIA MIDITRA
-- PAGE INSERTION PLAN COMPTE DIA MI INSERT PLAN COMPTE 

-- par défaut Débit
insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID) values 
(
        detail_trs_cpt_seq.NEXTVAL, 
        (select trs_id from ce_transaction_type where trs_code='CE-001'),
        (select cpt_id from ce_plan_compte where cpt_numero='520000')
);
insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID,IS_TRS_DEBIT) values 
(
        detail_trs_cpt_seq.NEXTVAL, 
        (select trs_id from ce_transaction_type where trs_code='CE-001'),
        (select cpt_id from ce_plan_compte where cpt_numero='701000'),
        0--Credit
);

insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID) values 
(
        detail_trs_cpt_seq.NEXTVAL, 
        (select trs_id from ce_transaction_type where trs_code='CE-002'),
        (select cpt_id from ce_plan_compte where cpt_numero='510001')
);
insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID,IS_TRS_DEBIT) values 
(
        detail_trs_cpt_seq.NEXTVAL, 
        (select trs_id from ce_transaction_type where trs_code='CE-002'),
        (select cpt_id from ce_plan_compte where cpt_numero='520000'),
        0--Credit
);

-- insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID) values 
-- (
--         detail_trs_cpt_seq.NEXTVAL, 
--         (select trs_id from transaction_type where trs_code='CE-003'),
--         (select cpt_id from plan_compte where cpt_numero='5100xx')
-- );
insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID,IS_TRS_DEBIT) values 
(
        detail_trs_cpt_seq.NEXTVAL, 
        (select trs_id from ce_transaction_type where trs_code='CE-003'),
        (select cpt_id from ce_plan_compte where cpt_numero='701000'),
        0--Credit
);

-- insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID) values 
-- (
--         detail_trs_cpt_seq.NEXTVAL, 
--         (select trs_id from transaction_type where trs_code='CE-004'),
--         (select cpt_id from plan_compte where cpt_numero='4xxxxx ou 6xxxxx'),
-- );
insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID,IS_TRS_DEBIT) values 
(
        detail_trs_cpt_seq.NEXTVAL, 
        (select trs_id from ce_transaction_type where trs_code='CE-004'),
        (select cpt_id from ce_plan_compte where cpt_numero='520000'),
        0--Credit
);

-- insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID) values 
-- (
--         detail_trs_cpt_seq.NEXTVAL, 
--         (select trs_id from transaction_type where trs_code='CE-005'),
--         (select cpt_id from plan_compte where cpt_numero='4xxxxx ou 6xxxxx'),
-- );
insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID,IS_TRS_DEBIT) values 
(
        detail_trs_cpt_seq.NEXTVAL, 
        (select trs_id from ce_transaction_type where trs_code='CE-005'),
        (select cpt_id from ce_plan_compte where cpt_numero='510001'),
        0--Credit
);

-- insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID) values 
-- (
--         detail_trs_cpt_seq.NEXTVAL, 
--         (select trs_id from transaction_type where trs_code='CE-006'),
--         (select cpt_id from plan_compte where cpt_numero='4xxxxx ou 6xxxxx'),
-- );
insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID,IS_TRS_DEBIT) values 
(
        detail_trs_cpt_seq.NEXTVAL, 
        (select trs_id from ce_transaction_type where trs_code='CE-006'),
        (select cpt_id from ce_plan_compte where cpt_numero='701000'),
        0--Credit
);

-- insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID) values 
-- (
--         detail_trs_cpt_seq.NEXTVAL, 
--         (select trs_id from transaction_type where trs_code='CE-007'),
--         (select cpt_id from plan_compte where cpt_numero='4xxxxx ou 6xxxxx'),
-- );
insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID,IS_TRS_DEBIT) values 
(
        detail_trs_cpt_seq.NEXTVAL, 
        (select trs_id from ce_transaction_type where trs_code='CE-007'),
        (select cpt_id from ce_plan_compte where cpt_numero='701000'),
        0--Credit
);

insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID) values 
(
        detail_trs_cpt_seq.NEXTVAL, 
        (select trs_id from ce_transaction_type where trs_code='CE-010'),
        (select cpt_id from ce_plan_compte where cpt_numero='520000')
);
insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID,IS_TRS_DEBIT) values 
(
        detail_trs_cpt_seq.NEXTVAL, 
        (select trs_id from ce_transaction_type where trs_code='CE-010'),
        (select cpt_id from ce_plan_compte where cpt_numero='702000'),
        0--Credit
);

-- insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID) values 
-- (
--         detail_trs_cpt_seq.NEXTVAL, 
--         (select trs_id from transaction_type where trs_code='CE-011'),
--         (select cpt_id from plan_compte where cpt_numero='67xxxx')
-- );
insert into ce_detail_transaction_compte(DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID,IS_TRS_DEBIT) values 
(
        detail_trs_cpt_seq.NEXTVAL, 
        (select trs_id from ce_transaction_type where trs_code='CE-011'),
        (select cpt_id from ce_plan_compte where cpt_numero='520000'),
        0--Credit
);

-- insertion des transaction détail fini :
--      22/09/24 à 17:44
--      lire les comptes commentée avant insertion des données et bien comprendre

-- DATA
delete from ce_mouvement;
delete from ce_evenement;
delete from ce_log_demande_type;
delete from ce_detail_demande_piece;
delete from ce_demande_type;
delete from ce_approvisionnement_piece;
delete from ce_detail_budget;
delete from ce_exercice;

-- INFORMATION
-- delete from ce_detail_transaction_compte;
-- delete from ce_transaction_type;
-- delete from ce_plan_compte;
-- delete from ce_compte_mere;

commit; -- commit faharoa