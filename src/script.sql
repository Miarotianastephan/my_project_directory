insert into groupe_utilisateur (grp_id,grp_libelle,grp_niveau) values (grp_seq.NEXTVAL,'Tsotra',10);
insert into groupe_utilisateur (grp_id,grp_libelle,grp_niveau) values (grp_seq.NEXTVAL,'SG',20);
insert into groupe_utilisateur (grp_id,grp_libelle,grp_niveau) values (grp_seq.NEXTVAL,'Tresorier',30);

insert into demande (dm_id,libelle) values (demande_seq.NEXTVAL,'Decaissement');

insert into exercice (exercice_id,exercice_date_debut) values (exercice_seq.NEXTVAL,TO_DATE('01-01-2004','DD-MM-YYYY'));

--insert into utilisateur (user_id,user_matricule,dt_ajout,grp_id) values (user_seq.NEXTVAL,'1989',(select current_date from dual),(select gp.grp_id from groupe_utilisateur gp where gp.grp_libelle='Tsotra'));
--insert into utilisateur (user_id,user_matricule,dt_ajout,grp_id) values (user_seq.NEXTVAL,'1846',(select current_date from dual),(select gp.grp_id from groupe_utilisateur gp where gp.grp_libelle='Tresorier'));
--insert into utilisateur (user_id,user_matricule,dt_ajout,grp_id) values (user_seq.NEXTVAL,'2060',(select current_date from dual),(select gp.grp_id from groupe_utilisateur gp where gp.grp_libelle='SG'));

insert into demande_type 
(DM_TYPE_ID,ENTITY_CODE_ID,UTILISATEUR_ID,PLAN_COMPTE_ID,EXERCICE_ID,DM_ID,DM_DATE,DM_MONTANT,DM_MODE_PAIEMENT,REF_DEMANDE,DM_ETAT) 
values (
    demande_type_seq.NEXTVAL,
    (select ENTITY.cpt_id from plan_compte ENTITY where ENTITY.cpt_numero='510001'),
    (select u.user_id from utilisateur u where u.user_matricule='tesla'),
    (select PLAN_COMPTE.cpt_id from plan_compte PLAN_COMPTE where PLAN_COMPTE.cpt_numero='611000'),
    (select e.exercice_id from exercice e where e.exercice_date_debut=TO_DATE('01-01-2004','DD-MM-YYYY')),
    (select d.dm_id from demande d where d.libelle='Decaissement'),
    TO_DATE('27-08-2024','DD-MM-YYYY'),20000000,'Cheque','REF_001',10)
    ;


insert into demande_type
(DM_TYPE_ID,ENTITY_CODE_ID,UTILISATEUR_ID,PLAN_COMPTE_ID,EXERCICE_ID,DM_ID,DM_DATE,DM_MONTANT,DM_MODE_PAIEMENT,REF_DEMANDE,DM_ETAT) 
values (
    demande_type_seq.NEXTVAL,
    (select ENTITY.cpt_id from plan_compte ENTITY where ENTITY.cpt_numero='510001'),
    (select u.user_id from utilisateur u where u.user_matricule='tesla'),
    (select PLAN_COMPTE.cpt_id from plan_compte PLAN_COMPTE where PLAN_COMPTE.cpt_numero='611000'),
    (select e.exercice_id from exercice e where e.exercice_date_debut=TO_DATE('01-01-2004','DD-MM-YYYY')),
    (select d.dm_id from demande d where d.libelle='Decaissement'),
    TO_DATE('27-08-2024','DD-MM-YYYY'),500000,'Espece','REF_002',10)
    ;

insert into demande_type
(DM_TYPE_ID,ENTITY_CODE_ID,UTILISATEUR_ID,PLAN_COMPTE_ID,EXERCICE_ID,DM_ID,DM_DATE,DM_MONTANT,DM_MODE_PAIEMENT,REF_DEMANDE,DM_ETAT)
values (
    demande_type_seq.NEXTVAL,
    (select ENTITY.cpt_id from plan_compte ENTITY where ENTITY.cpt_numero='510001'),
    (select u.user_id from utilisateur u where u.user_matricule='1989'),
    (select PLAN_COMPTE.cpt_id from plan_compte PLAN_COMPTE where PLAN_COMPTE.cpt_numero='611000'),
    (select e.exercice_id from exercice e where e.exercice_date_debut=TO_DATE('01-01-2004','DD-MM-YYYY')),
    (select d.dm_id from demande d where d.libelle='Decaissement'),
    TO_DATE('27-08-2024','DD-MM-YYYY'),200000,'Espece','REF_003',10)
    ;

insert into demande_type
(DM_TYPE_ID,ENTITY_CODE_ID,UTILISATEUR_ID,PLAN_COMPTE_ID,EXERCICE_ID,DM_ID,DM_DATE,DM_MONTANT,DM_MODE_PAIEMENT,REF_DEMANDE,DM_ETAT) 
values (
    demande_type_seq.NEXTVAL,
    (select ENTITY.cpt_id from plan_compte ENTITY where ENTITY.cpt_numero='510001'),
    (select u.user_id from utilisateur u where u.user_matricule='tesla'),
    (select PLAN_COMPTE.cpt_id from plan_compte PLAN_COMPTE where PLAN_COMPTE.cpt_numero='611000'),
    (select e.exercice_id from exercice e where e.exercice_date_debut=TO_DATE('01-01-2004','DD-MM-YYYY')),
    (select d.dm_id from demande d where d.libelle='Decaissement'),
    TO_DATE('27-08-2024','DD-MM-YYYY'),200000,'Espece','REF_004',10)
    ;

insert into budget_type (BUDGET_TYPE_ID,LIBELLE) values (budget_type_seq.NEXTVAL,'Depense');
insert into budget_type (BUDGET_TYPE_ID,LIBELLE) values (budget_type_seq.NEXTVAL,'Investissement');
insert into budget_type (BUDGET_TYPE_ID,LIBELLE) values (budget_type_seq.NEXTVAL,'Fonctionnement');

insert into detail_budget (DETAIL_BUDGET_ID,BUDGET_MONTANT,BUDGET_DATE,BUDGET_TYPE_ID,EXERCICE_ID,COMPTE_MERE_ID)
values (detail_budget_seq.NEXTVAL,20000,TO_DATE('27-08-2024','DD-MM-YYYY'));



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


/*insert into demande_type
(DM_TYPE_ID,ENTITY_CODE_ID,UTILISATEUR_ID,PLAN_COMPTE_ID,EXERCICE_ID,DM_ID,DM_DATE,DM_MONTANT,DM_MODE_PAIEMENT,REF_DEMANDE,DM_ETAT)
values (
demande_type_seq.NEXTVAL,
(select ENTITY.cpt_id from plan_compte ENTITY where ENTITY.cpt_libelle='Caisse Siege'),
(select u.user_id from utilisateur u where u.user_matricule='1989'),
(select PLAN_COMPTE.cpt_id from plan_compte PLAN_COMPTE where PLAN_COMPTE.cpt_libelle='Social (Prime diverse)'),
(select e.exercice_id from exercice e where e.exercice_date_debut=TO_DATE('01-01-2004','DD-MM-YYYY')),
(select d.dm_id from demande d where d.libelle='Decaissement'),
TO_DATE('27-08-2024','DD-MM-YYYY'),8000,'Espece','REF_002',10);*/


-- Insertion des données dans la table `plan_compte`
/*
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '330000', 'Compte d attente a regulariser');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '442710', 'Materiels de bureau');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '442720', 'Mobiliers de bureau');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '442730', 'Materiels informatiques');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '442740', 'Materiels de communication');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '442750', 'Autres materiels');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '51', 'Caisse');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '510001', 'Caisse Siege');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '510002', 'Caisse RT ATB');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '510003', 'Caisse RT ATR');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '510004', 'Caisse RT FNR');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '510005', 'Caisse RT MDV');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '510006', 'Caisse RT MHJ');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '510007', 'Caisse RT MNK');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '510008', 'Caisse RT NSB');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '510009', 'Caisse RT SBV');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '510010', 'Caisse RT TLG');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '510011', 'Caisse RT TLR');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '510012', 'Caisse RT TMS');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '520000', 'Banque');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '61', 'Social Primes diverses');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '611000', 'Social (Prime diverse)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '612000', 'Social (Prime de reussite examen)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '613000', 'Social (Prime de naissance)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '614000', 'Social (Prime de mariage)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '615000', 'Social (Depart à la retraite)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '62', 'Social deces');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '621000', 'Social (Deces mpiasa)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '622000', 'Social (Deces vady, zanaka, RAR)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '623000', 'Social (Deces : location fiara)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '624000', 'Social (Deces : Indemnite agent)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '63', 'Social (Hospitalisation)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '64', 'Depenses CE');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '65', 'Dotation fete');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '631000', 'Frais de deplacement');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '632000', 'Frais d hebergement');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '633000', 'Per diem');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '651000', 'Achats divers');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '652000', 'Locations diverses');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '653000', 'Honoraires prestataires');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '654000', 'Autres charges sur fêtes');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '66', 'Activite sportive');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661', 'Activite sportive (collation)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661100', 'Activite sportive (collation) : Football');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661110', 'Activite sportive (collation) : Basket ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661120', 'Activite sportive (collation) : Volley ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661130', 'Activite sportive (collation) : Rugby');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661140', 'Activite sportive (collation) : Ping pong');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661150', 'Activite sportive (collation) : Tennis');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661160', 'Activite sportive (collation) : Athletisme');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661170', 'Activite sportive (collation) : Boule');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661180', 'Activite sportive (collation) : Belotte');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661190', 'Activite sportive (collation) : Natation');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661200', 'Activite sportive (collation) : Judo');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662', 'Activite sportive (droit de participation)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662100', 'Activite sportive (D.P.) : Football');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662110', 'Activite sportive (D.P.) : Basket ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662120', 'Activite sportive (D.P.) : Volley ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662130', 'Activite sportive (D.P.) : Rugby');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662140', 'Activite sportive (D.P.) : Ping pong');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662150', 'Activite sportive (D.P.) : Tennis');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662160', 'Activite sportive (D.P.) : Athletisme');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662170', 'Activite sportive (D.P.) : Boule');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662180', 'Activite sportive (D.P.) : Belotte');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662190', 'Activite sportive (D.P.) : Natation');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662200', 'Activite sportive (D.P.) : Judo');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663', 'Activite sportive (loyer et honoraires)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663100', 'Activite sportive (L&H) : Football');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663110', 'Activite sportive (L&H) : Basket ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663120', 'Activite sportive (L&H) : Volley ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663130', 'Activite sportive (L&H) : Rugby');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663140', 'Activite sportive (L&H) : Ping pong');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663150', 'Activite sportive (L&H) : Tennis');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663160', 'Activite sportive (L&H) : Athletisme');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663170', 'Activite sportive (L&H) : Boule');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663180', 'Activite sportive (L&H) : Belotte');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663190', 'Activite sportive (L&H) : Natation');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663200', 'Activite sportive (L&H) : Judo');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663300', 'Activite sportive (L&H) : Autres');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663210', 'Activite sportive (L&H) : Sport en salle');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664', 'Activite sportive (frais de deplacement)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664100', 'Activite sportive (F.D.) : Football');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664110', 'Activite sportive (F.D.) : Basket ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664120', 'Activite sportive (F.D.) : Volley ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664130', 'Activite sportive (F.D.) : Rugby');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664140', 'Activite sportive (F.D.) : Ping pong');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664150', 'Activite sportive (F.D.) : Tennis');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664160', 'Activite sportive (F.D.) : Athletisme');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664170', 'Activite sportive (F.D.) : Boule');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664180', 'Activite sportive (F.D.) : Belotte');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664190', 'Activite sportive (F.D.) : Natation');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664200', 'Activite sportive (F.D.) : Judo');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665', 'Activite sportive (achat materiel)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665100', 'Activite sportive (A.M.) : Football');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665110', 'Activite sportive (A.M.) : Basket ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665120', 'Activite sportive (A.M.) : Volley ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665130', 'Activite sportive (A.M.) : Rugby');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665140', 'Activite sportive (A.M.) : Ping pong');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665150', 'Activite sportive (A.M.) : Tennis');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665160', 'Activite sportive (A.M.) : Athletisme');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665170', 'Activite sportive (A.M.) : Boule');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665180', 'Activite sportive (A.M.) : Belotte');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665190', 'Activite sportive (A.M.) : Natation');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665200', 'Activite sportive (A.M.) : Judo');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '666', 'Activite sportive (autres)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '67', 'Frais et commission bancaire');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '670001', 'Frais de tenue de compte');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '670002', 'Frais de demande de cheque');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '670003', 'Frais de retrait');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '670004', 'Autres frais bancaires');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '670005', 'Autres charges');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '681000', 'Activite culturelle');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '682000', 'Activite musicale');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '683000', 'Autres charges et activites');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '701000', 'Subvention BFM');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '702000', 'Interet crediteur');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '703000', 'Produits divers');
*/


INSERT INTO transaction_type (TRS_ID,TRS_CODE,TRS_DEFINITION,TRS_LIBELLE) VALUES (trs_seq.NEXTVAL, 'CE-007', NULL,'Depense payees directement pas BFM');
INSERT INTO transaction_type (TRS_ID,TRS_CODE,TRS_DEFINITION,TRS_LIBELLE) VALUES (trs_seq.NEXTVAL, 'CE-011', NULL,'Comptabilisation de frais bancaire');

INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),(select cpt_id from plan_compte where cpt_numero = '442750'));
INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),(select cpt_id from plan_compte where cpt_numero = '442710'));
INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),(select cpt_id from plan_compte where cpt_numero = '442720'));
INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),(select cpt_id from plan_compte where cpt_numero = '442730'));
INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),(select cpt_id from plan_compte where cpt_numero = '442740'));
INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),(select cpt_id from plan_compte where cpt_numero = '67'));
INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),(select cpt_id from plan_compte where cpt_numero = '670001'));
INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),(select cpt_id from plan_compte where cpt_numero = '670002'));
INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),(select cpt_id from plan_compte where cpt_numero = '670003'));
INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),(select cpt_id from plan_compte where cpt_numero = '670004'));
INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-007'),(select cpt_id from plan_compte where cpt_numero = '670005'));

INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-011'),(select cpt_id from plan_compte where cpt_numero = '67'));
INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-011'),(select cpt_id from plan_compte where cpt_numero = '670001'));
INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-011'),(select cpt_id from plan_compte where cpt_numero = '670002'));
INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-011'),(select cpt_id from plan_compte where cpt_numero = '670003'));
INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-011'),(select cpt_id from plan_compte where cpt_numero = '670004'));
INSERT INTO DETAIL_TRANSACTION_COMPTE ( DET_TRS_CPT_ID,TRANSACTION_TYPE_ID,PLAN_COMPTE_ID) VALUES (detail_trs_cpt_seq.NEXTVAL, (select TRS_ID from TRANSACTION_TYPE where TRS_CODE = 'CE-011'),(select cpt_id from plan_compte where cpt_numero = '670005'));

