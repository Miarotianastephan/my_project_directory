Symfony 6.4 ready to use webapp


* téléchargé icon : composer require symfony/ux-icons
* télécharger de façon permanent :php bin/console ux:icons import [heroicons:information-circle]
* téléchargé composer upload file : composer require vich/uploader-bundle 
* php bin/console ux:icons:import flat-color-icons:print
* php bin/console ux:icons:import heroicons:information-circle
* php bin/console ux:icons:import heroicons:arrow-left-circle

**Scénario de test**:
1. Ajout utilisateur admin (Data fixture)
2. Distribution de role (sg, tresorier, comptable , commisaire au compte)
3. Création exercice
3. Import plan de compte
4. Ajout de budget par exercice par plan de compte
5. Ajout DETAIL_TRANSACTION_COMPTE (script)
6. Ajout transaction_type (script)
7. ajout detail_transaction_compte (script)
