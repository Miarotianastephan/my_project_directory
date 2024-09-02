-- insert into groupe_utilisateur (grp_id,grp_libelle,grp_niveau) values (grp_seq.NEXTVAL,'Tsotra',10);
-- insert into groupe_utilisateur (grp_id,grp_libelle,grp_niveau) values (grp_seq.NEXTVAL,'SG',20);
-- insert into groupe_utilisateur (grp_id,grp_libelle,grp_niveau) values (grp_seq.NEXTVAL,'Tresorier',30);


-- insert into demande (dm_id,libelle) values (demande_seq.NEXTVAL,'Decaissement');
-- insert into plan_compte (cpt_id,cpt_numero,cpt_libelle) values (cpt_seq.NEXTVAL,'51','Caisse');
-- insert into plan_compte (cpt_id,cpt_numero,cpt_libelle) values (cpt_seq.NEXTVAL,'510001','Caisse Siege');
-- insert into plan_compte (cpt_id,cpt_numero,cpt_libelle) values (cpt_seq.NEXTVAL,'611000','Social (Prime diverse)');	
-- insert into exercice (exercice_id,exercice_date_debut) values (exercice_seq.NEXTVAL,TO_DATE('01-01-2004','DD-MM-YYYY'));


-- insert into utilisateur (user_id,user_matricule,dt_ajout,grp_id) values 
-- (user_seq.NEXTVAL,'1989',(select current_date from dual),(select gp.grp_id from groupe_utilisateur gp where gp.grp_libelle='Tresorier'));

-- insert into utilisateur (user_id,user_matricule,dt_ajout,grp_id) values 
-- (user_seq.NEXTVAL,'1846',(select current_date from dual),(select gp.grp_id from groupe_utilisateur gp where gp.grp_libelle='SG'));

-- insert into utilisateur (user_id,user_matricule,dt_ajout,grp_id) values 
-- (user_seq.NEXTVAL,'2060',(select current_date from dual),(select gp.grp_id from groupe_utilisateur gp where gp.grp_libelle='SG'));

-- insert into demande_type 
-- (DM_TYPE_ID,ENTITY_CODE_ID,UTILISATEUR_ID,PLAN_COMPTE_ID,EXERCICE_ID,DM_ID,DM_DATE,DM_MONTANT,DM_MODE_PAIEMENT,REF_DEMANDE,DM_ETAT) 
-- values (
--     demande_type_seq.NEXTVAL,
--     (select ENTITY.cpt_id from plan_compte ENTITY where ENTITY.cpt_libelle='Caisse Siege'),
--     (select u.user_id from utilisateur u where u.user_matricule='1989'),
--     (select PLAN_COMPTE.cpt_id from plan_compte PLAN_COMPTE where PLAN_COMPTE.cpt_libelle='Social (Prime diverse)'),
--     (select e.exercice_id from exercice e where e.exercice_date_debut=TO_DATE('01-01-2004','DD-MM-YYYY')),
--     (select d.dm_id from demande d where d.libelle='Decaissement'),
--     TO_DATE('27-08-2024','DD-MM-YYYY'),20000000,'Cheque','REF_001',10)
--     ;

-- insert into demande_type 
-- (DM_TYPE_ID,ENTITY_CODE_ID,UTILISATEUR_ID,PLAN_COMPTE_ID,EXERCICE_ID,DM_ID,DM_DATE,DM_MONTANT,DM_MODE_PAIEMENT,REF_DEMANDE,DM_ETAT) 
-- values (
--     demande_type_seq.NEXTVAL,
--     (select ENTITY.cpt_id from plan_compte ENTITY where ENTITY.cpt_libelle='Caisse Siege'),
--     (select u.user_id from utilisateur u where u.user_matricule='1989'),
--     (select PLAN_COMPTE.cpt_id from plan_compte PLAN_COMPTE where PLAN_COMPTE.cpt_libelle='Social (Prime diverse)'),
--     (select e.exercice_id from exercice e where e.exercice_date_debut=TO_DATE('01-01-2004','DD-MM-YYYY')),
--     (select d.dm_id from demande d where d.libelle='Decaissement'),
--     TO_DATE('27-08-2024','DD-MM-YYYY'),900000,'Cheque','REF_001',10)
--     ;



--INSERT INTO log_demande_type (LOG_DM_ID, DEMANDE_TYPE_ID, LOG_DM_DATE, DM_ETAT, USER_MATRICULE) VALUES (log_etat_demande_seq.NEXTVAL,9,DEFAULT,10,24);
--INSERT INTO log_demande_type (LOG_DM_ID, DEMANDE_TYPE_ID, LOG_DM_DATE, DM_ETAT, USER_MATRICULE) VALUES (log_etat_demande_seq.NEXTVAL,:dm_type_id,DEFAULT,:etat,:user_matricule)


/*
    SCRIPT MILA ATAO
    ALTER TABLE log_demande_type MODIFY LOG_DM_DATE DEFAULT SYSDATE;
    ALTER TABLE detail_demande_piece MODIFY det_dm_date DEFAULT SYSDATE;

*/

INSERT INTO detail_demande_piece (DETAIL_DM_TYPE_ID, DEMANDE_TYPE_ID,DET_DM_PIECE_URL, DET_DM_TYPE_URL, DET_DM_DATE)
VALUES (detail_dm_type_seq.NEXTVAL,1,'66d5525a953ad.png','bon_livraison',DEFAULT);
 types={"det_dm_piece_url":2,"det_dm_type_url":2,"dm_type_id":2}
