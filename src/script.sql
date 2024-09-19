-- insert into groupe_utilisateur (grp_id,grp_libelle,grp_niveau) values (grp_seq.NEXTVAL,'Tsotra',10);
-- insert into groupe_utilisateur (grp_id,grp_libelle,grp_niveau) values (grp_seq.NEXTVAL,'SG',20);
-- insert into groupe_utilisateur (grp_id,grp_libelle,grp_niveau) values (grp_seq.NEXTVAL,'Tresorier',30);


-- insert into demande (dm_id,libelle) values (demande_seq.NEXTVAL,'Decaissement');
-- insert into plan_compte (cpt_id,cpt_numero,cpt_libelle) values (cpt_seq.NEXTVAL,'51','Caisse');
-- insert into plan_compte (cpt_id,cpt_numero,cpt_libelle) values (cpt_seq.NEXTVAL,'510001','Caisse Siege');
-- insert into plan_compte (cpt_id,cpt_numero,cpt_libelle) values (cpt_seq.NEXTVAL,'611000','Social (Prime diverse)');	
insert into exercice (exercice_id,exercice_date_debut) values (exercice_seq.NEXTVAL,TO_DATE('01-01-2004','DD-MM-YYYY'));
insert into exercice (exercice_id,exercice_date_debut) values (exercice_seq.NEXTVAL,TO_DATE('01-01-2024','DD-MM-YYYY'));


-- insert into utilisateur (user_id,user_matricule,dt_ajout,grp_id) values 
-- (user_seq.NEXTVAL,'1989',(select current_date from dual),(select gp.grp_id from groupe_utilisateur gp where gp.grp_libelle='Tsotra'));

-- insert into utilisateur (user_id,user_matricule,dt_ajout,grp_id) values 
-- (user_seq.NEXTVAL,'1846',(select current_date from dual),(select gp.grp_id from groupe_utilisateur gp where gp.grp_libelle='Tresorier'));

-- insert into utilisateur (user_id,user_matricule,dt_ajout,grp_id) values 
-- (user_seq.NEXTVAL,'2060',(select current_date from dual),(select gp.grp_id from groupe_utilisateur gp where gp.grp_libelle='SG'));

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
    (select u.user_id from utilisateur u where u.user_matricule='tesla'),
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

INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '330000', 'Compte d attente à régulariser');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '442710', 'Matériels de bureau');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '442720', 'Mobiliers de bureau');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '442730', 'Matériels informatiques');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '442740', 'Matériels de communication');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '442750', 'Autres matériels');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '51', 'Caisse');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '510001', 'Caisse Siège');
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
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '615000', 'Social (Départ à la retraite)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '62', 'Social décès');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '621000', 'Social (Décès mpiasa)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '622000', 'Social (Décès vady, zanaka, RAR)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '623000', 'Social (Décès : location fiara)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '624000', 'Social (Décès : Indemnité agent)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '63', 'Social (Hospitalisation)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '64', 'Dépenses CE');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '65', 'Dotation fête');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '631000', 'Frais de déplacement');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '632000', 'Frais d hébergement');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '633000', 'Per diem');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '651000', 'Achats divers');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '652000', 'Locations diverses');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '653000', 'Honoraires prestataires');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '654000', 'Autres charges sur fêtes');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '66', 'Activité sportive');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661', 'Activité sportive (collation)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661100', 'Activité sportive (collation) : Football');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661110', 'Activité sportive (collation) : Basket ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661120', 'Activité sportive (collation) : Volley ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661130', 'Activité sportive (collation) : Rugby');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661140', 'Activité sportive (collation) : Ping pong');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661150', 'Activité sportive (collation) : Tennis');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661160', 'Activité sportive (collation) : Athlétisme');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661170', 'Activité sportive (collation) : Boule');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661180', 'Activité sportive (collation) : Belotte');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661190', 'Activité sportive (collation) : Natation');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '661200', 'Activité sportive (collation) : Judo');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662', 'Activité sportive (droit de participation)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662100', 'Activité sportive (D.P.) : Football');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662110', 'Activité sportive (D.P.) : Basket ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662120', 'Activité sportive (D.P.) : Volley ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662130', 'Activité sportive (D.P.) : Rugby');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662140', 'Activité sportive (D.P.) : Ping pong');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662150', 'Activité sportive (D.P.) : Tennis');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662160', 'Activité sportive (D.P.) : Athlétisme');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662170', 'Activité sportive (D.P.) : Boule');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662180', 'Activité sportive (D.P.) : Belotte');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662190', 'Activité sportive (D.P.) : Natation');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '662200', 'Activité sportive (D.P.) : Judo');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663', 'Activité sportive (loyer et honoraires)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663100', 'Activité sportive (L&H) : Football');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663110', 'Activité sportive (L&H) : Basket ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663120', 'Activité sportive (L&H) : Volley ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663130', 'Activité sportive (L&H) : Rugby');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663140', 'Activité sportive (L&H) : Ping pong');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663150', 'Activité sportive (L&H) : Tennis');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663160', 'Activité sportive (L&H) : Athlétisme');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663170', 'Activité sportive (L&H) : Boule');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663180', 'Activité sportive (L&H) : Belotte');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663190', 'Activité sportive (L&H) : Natation');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663200', 'Activité sportive (L&H) : Judo');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663300', 'Activité sportive (L&H) : Autres');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '663210', 'Activité sportive (L&H) : Sport en salle');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664', 'Activité sportive (frais de déplacement)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664100', 'Activité sportive (F.D.) : Football');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664110', 'Activité sportive (F.D.) : Basket ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664120', 'Activité sportive (F.D.) : Volley ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664130', 'Activité sportive (F.D.) : Rugby');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664140', 'Activité sportive (F.D.) : Ping pong');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664150', 'Activité sportive (F.D.) : Tennis');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664160', 'Activité sportive (F.D.) : Athlétisme');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664170', 'Activité sportive (F.D.) : Boule');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664180', 'Activité sportive (F.D.) : Belotte');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664190', 'Activité sportive (F.D.) : Natation');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '664200', 'Activité sportive (F.D.) : Judo');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665', 'Activité sportive (achat matériel)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665100', 'Activité sportive (A.M.) : Football');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665110', 'Activité sportive (A.M.) : Basket ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665120', 'Activité sportive (A.M.) : Volley ball');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665130', 'Activité sportive (A.M.) : Rugby');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665140', 'Activité sportive (A.M.) : Ping pong');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665150', 'Activité sportive (A.M.) : Tennis');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665160', 'Activité sportive (A.M.) : Athlétisme');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665170', 'Activité sportive (A.M.) : Boule');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665180', 'Activité sportive (A.M.) : Belotte');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665190', 'Activité sportive (A.M.) : Natation');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '665200', 'Activité sportive (A.M.) : Judo');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '666', 'Activité sportive (autres)');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '67', 'Frais et commission bancaire');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '670001', 'Frais de tenue de compte');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '670002', 'Frais de demande de chèque');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '670003', 'Frais de retrait');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '670004', 'Autres frais bancaires');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '670005', 'Autres charges');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '681000', 'Activité culturelle');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '682000', 'Activité musicale');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '683000', 'Autres charges et activités');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '701000', 'Subvention BFM');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '702000', 'Intérêt créditeur');
INSERT INTO plan_compte (cpt_id, cpt_numero, cpt_libelle) VALUES (cpt_seq.NEXTVAL, '703000', 'Produits divers');

INSERT INTO transaction_type (TRS_ID,TRS_CODE,TRS_DEFINITION,TRS_LIBELLE) VALUES (trs_seq.NEXTVAL, 'CE-007', NULL,'Dépense payées directement pas BFM');
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

INSERT INTO utilisateur (user_id, user_matricule, dt_ajout, roles, grp_id) 
VALUES (user_seq.NEXTVAL, 'eclid', '2024-08-30 00:00:00', '[\"ROLE_USER\"]', 1); 
INSERT INTO utilisateur (user_id, user_matricule, dt_ajout, roles, grp_id) 
VALUES (?, ?, ?, ?, ?) 
(
    parameters: array{"1":81,"2":"euclid","3":"2024-08-30 00:00:00","4":"[\"ROLE_USER\"]","5":1},
    types: array{"1":1,"2":2,"3":2,"4":2,"5":1}
) params={"1":81,"2":"euclid","3":"2024-08-30 00:00:00","4":"[\"ROLE_USER\"]","5":1} 
sql="INSERT INTO utilisateur (user_id, user_matricule, dt_ajout, roles, grp_id) VALUES (?, ?, ?, ?, ?)" 
types={"1":1,"2":2,"3":2,"4":2,"5":1}


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

  :NEW.REF_DEMANDE := v_prefix || '_' || v_year || '_' || v_sequence;
END;
/

insert into demande_type
(DM_TYPE_ID,ENTITY_CODE_ID,UTILISATEUR_ID,PLAN_COMPTE_ID,EXERCICE_ID,DM_ID,DM_DATE,DM_MONTANT,DM_MODE_PAIEMENT,DM_ETAT,DM_DATE_OPERATION) 
values (
    demande_type_seq.NEXTVAL,
    (select ENTITY.cpt_id from plan_compte ENTITY where ENTITY.cpt_numero='510001'),
    (select u.user_id from utilisateur u where u.user_matricule='tesla'),
    (select PLAN_COMPTE.cpt_id from plan_compte PLAN_COMPTE where PLAN_COMPTE.cpt_numero='611000'),
    (select e.exercice_id from exercice e where e.exercice_date_debut=TO_DATE('01-01-2004','DD-MM-YYYY')),
    (select d.dm_id from demande d where d.libelle='Decaissement'),
    TO_DATE('16-09-2024','DD-MM-YYYY'),25000,'Espece',10,TO_DATE('16-09-2024','DD-MM-YYYY'))
    ;


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