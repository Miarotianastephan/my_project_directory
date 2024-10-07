Symfony 6.4 ready to use webapp

To Set Up :

1/ créena le utilisateur:DEV_USR sy mot de passe:DEV_PASS @ oracle
2/ manao commande create migration indray mandeha
3/ execute-na le migration

4/ execute-na reto script reto après migration mety tsara :
    ALTER TABLE utilisateur MODIFY DT_AJOUT DEFAULT SYSDATE;
    ALTER TABLE log_demande_type MODIFY LOG_DM_DATE DEFAULT SYSDATE;
    ALTER TABLE detail_demande_piece MODIFY det_dm_date DEFAULT SYSDATE;

5/Executena ito trigger ito :
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

6/ Insertion anah utilisateur par default :
  6-a/ assurer que DoctrineFixturesBundle est installé : composer require --dev doctrine/doctrine-fixtures-bundle
  6-b/ executer la commande : php bin/console doctrine:fixtures:load --append

7/ Executena ny script ao anaty ataovy tsikelikely mba hi assurena hoe mety
  "src\script.sql"

  NB rehefa ao anaty ilay script: 
  -- AVANT INSERTION NY DETAIL TRANSACTION COMPTE 
  -- IRETO DIA MI CONNECTE ADMIN @ LE APPLICATION ALOHA DIA MIDITRA
  -- PAGE INSERTION PLAN COMPTE DIA MI INSERT PLAN COMPTE 

  


