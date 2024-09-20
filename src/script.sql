insert into groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, 'Admin', 0);
insert into groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, 'Responsable Commission', 10);
insert into groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, 'Secretaire Generale', 20);
insert into groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, 'Tresorier', 30);
insert into groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, 'Comptable', 40);
insert into groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, 'Commissaire aux Comptes', 50);

insert into demande (dm_id, libelle, dm_code) values (demande_seq.NEXTVAL, 'Decaissement', 10);
insert into demande (dm_id, libelle, dm_code) values (demande_seq.NEXTVAL, 'Approvisionnement' , 20);

insert into exercice (exercice_id, exercice_date_debut) values (exercice_seq.NEXTVAL, TO_DATE('01-01-2024', 'DD-MM-YYYY'));
insert into exercice (exercice_id, exercice_date_debut) values (exercice_seq.NEXTVAL, TO_DATE('01-01-2025', 'DD-MM-YYYY'));

insert into budget_type (BUDGET_TYPE_ID, LIBELLE) values (budget_type_seq.NEXTVAL, 'Depense');
insert into budget_type (BUDGET_TYPE_ID, LIBELLE) values (budget_type_seq.NEXTVAL, 'Investissement');
insert into budget_type (BUDGET_TYPE_ID, LIBELLE) values (budget_type_seq.NEXTVAL, 'Fonctionnement');

insert into detail_budget (DETAIL_BUDGET_ID, BUDGET_MONTANT, BUDGET_DATE, BUDGET_TYPE_ID, EXERCICE_ID, COMPTE_MERE_ID)
values (detail_budget_seq.NEXTVAL,
        20000,
        TO_DATE('27-08-2024', 'DD-MM-YYYY'),
        (select b.BUDGET_TYPE_ID from BUDGET_TYPE b where b.LIBELLE = 'Depense'),
        (select e.exercice_id from exercice e where e.exercice_date_debut = TO_DATE('01-01-2004', 'DD-MM-YYYY')),
        (select cpt.ID from compte_mere cpt where cpt.CPT_NUMERO = 61));
/*
    SCRIPT MILA ATAO
    ALTER TABLE utilisateur MODIFY DT_AJOUT DEFAULT SYSDATE;
    ALTER TABLE log_demande_type MODIFY LOG_DM_DATE DEFAULT SYSDATE;
    ALTER TABLE detail_demande_piece MODIFY det_dm_date DEFAULT SYSDATE;

*/

INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),
        (select cpt_id from plan_compte where cpt_numero = '442750'));
INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),
        (select cpt_id from plan_compte where cpt_numero = '442710'));
INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),
        (select cpt_id from plan_compte where cpt_numero = '442720'));
INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),
        (select cpt_id from plan_compte where cpt_numero = '442730'));
INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),
        (select cpt_id from plan_compte where cpt_numero = '442740'));
INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),
        (select cpt_id from plan_compte where cpt_numero = '67'));
INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),
        (select cpt_id from plan_compte where cpt_numero = '670001'));
INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),
        (select cpt_id from plan_compte where cpt_numero = '670002'));
INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),
        (select cpt_id from plan_compte where cpt_numero = '670003'));
INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),
        (select cpt_id from plan_compte where cpt_numero = '670004'));
INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),
        (select cpt_id from plan_compte where cpt_numero = '670005'));

INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-011'),
        (select cpt_id from plan_compte where cpt_numero = '67'));
INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-011'),
        (select cpt_id from plan_compte where cpt_numero = '670001'));
INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-011'),
        (select cpt_id from plan_compte where cpt_numero = '670002'));
INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-011'),
        (select cpt_id from plan_compte where cpt_numero = '670003'));
INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-011'),
        (select cpt_id from plan_compte where cpt_numero = '670004'));
INSERT INTO DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID, TRANSACTION_TYPE_ID, PLAN_COMPTE_ID)
VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-011'),
        (select cpt_id from plan_compte where cpt_numero = '670005'));

INSERT INTO banque (BANQUE_ID,NOM_BANQUE) VALUES (banque_seq.NEXTVAL,'BMOI');
insert into banque (BANQUE_ID,NOM_BANQUE) VALUES (banque_seq.NEXTVAL,'BOA');
insert into banque (BANQUE_ID,NOM_BANQUE) VALUES (banque_seq.NEXTVAL,'Baoba banque');
insert into banque (BANQUE_ID,NOM_BANQUE) VALUES (banque_seq.NEXTVAL,'Societe generale');
insert into banque (BANQUE_ID,NOM_BANQUE) VALUES (banque_seq.NEXTVAL,'BGFI Bank Madagascar');
insert into banque (BANQUE_ID,NOM_BANQUE) VALUES (banque_seq.NEXTVAL,'MCB Madagascar');

-- MBOLA TSY METY
INSERT INTO chequier (CHEQUIER_ID,BANQUE_ID,CHEQUIER_NUMERO_DEBUT,CHEQUIER_NUMERO_FIN,CHEQUIER_DATE_ARRIVEE)
VALUES (
        chequier_seq.NEXTVAL,
        (select BANQUE_ID from banque where NOM_BANQUE = 'BMOI'),
        '1'
       );

--Get Exercice valide pour budget
select * from exercice where EXERCICE_DATE_DEBUT > TO_DATE('01-08-2024','DD-MM-YYYY') and EXERCICE_DATE_FIN is null ;


-- MILA ATAO
CREATE OR REPLACE TRIGGER trg_generate_reference
BEFORE INSERT ON demande_type
FOR EACH ROW
DECLARE
  v_year VARCHAR2(4);   
  v_sequence NUMBER;    
  v_prefix VARCHAR2(3); 
  v_code_dm NUMBER;   
BEGIN
 
  SELECT TO_CHAR(SYSDATE, 'YYYY') INTO v_year FROM DUAL;
  SELECT :NEW.dm_type_id INTO v_sequence FROM DUAL;

  SELECT dm_code INTO v_code_dm 
  FROM demande
  WHERE dm_id = :NEW.dm_id;

  IF v_code_dm = 10 THEN
    v_prefix := 'DEC';
  ELSE
    v_prefix := 'APR';
  END IF;

  :NEW.REF_DEMANDE := v_prefix || '/' || v_year || '/' || v_sequence;
END;
/

-- Insertion de transaction 
insert into transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-001','Encaissement Subvention BFM');

insert into transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-002','Approvisionnement petite caissse Siège');

insert into transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-003','Approvisionnement petite caissse RT');
-- Les paiements
insert into transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-004','Paiement facture par chèque');
insert into transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-005','Paiement facture en espèces sièges');
insert into transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-006','Paiement facture en espèces RT');

insert into transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-010','Encaissement interêt opération');

insert into transaction_type
(TRS_ID, TRS_CODE, TRS_LIBELLE)
VALUES (trs_seq.NEXTVAL,'CE-011','Comptabilisation des frais bancaires');

-- améliorations des états xxx
-- ETAT INITIEE 1xx
insert into etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,100, 'Initié');
insert into etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,101, 'Modifié');

-- ETAT ATTENTES 2xx
insert into etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,200, 'Attente fonds');
insert into etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,201, 'Attente modification');
insert into etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,202, 'Attente versement');

-- ETAT REFUS 3xx
insert into etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,300, 'Refusé');
insert into etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,301, 'Débloqué');

-- ETAT AVANT FIN 4xx
insert into etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,400, 'Justifié');
insert into etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,401, 'Reversé');

-- ETAT FIN 5xx
insert into etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,500, 'Comptabilisé');

-- FREE DEMANDE
-- delete from log_demande_type;
-- delete from demande_type;
-- commit;