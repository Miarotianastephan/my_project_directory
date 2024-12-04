Symfony 6.4 ready to use webapp


**Scénario de déploiement**:
1. Importer le dossier zippé dans le serveur
2. Décompresser le dossier dans /var/www/html
3. Allouer les droits nécessaires
4. Changer le owner en tant qu'apache
5. Lire et executer A_execute.bat (à renomer A_executer.sh sur serveur linux)

**Scénario de test**:
1. Ajout utilisateur admin (Data fixture)
2. Distribution de role (sg, tresorier, comptable , commisaire au compte)
3. Création exercice
3. Import plan de compte
4. Ajout de budget par exercice par plan de compte
5. Ajout DETAIL_TRANSACTION_COMPTE (script)
6. Ajout transaction_type (script)
7. ajout detail_transaction_compte (script)

**Accès SSH pour le code**:
1. Ajouter l'extension remote SSH sur visual studio code
2. ouvrir la ligne de commande et executé : code --remote ssh-remote+[nom_user]@[0.0.0.0] /var/www/html
3. Pour modification en tant que root ajouter l'extension : Save as Root in remote - ssh

