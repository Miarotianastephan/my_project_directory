php bin/console ScriptAlterTable
php bin/console ScriptAddGpUser
php bin/console ScriptAddDemande
php bin/console ScriptAddBudgetType
php bin/console ScriptAddTransaction
php bin/console ScriptAddEtatDemande
@REM Mila mi import PLAN COMPTE VAO MANDEHA NY APPRO
@REM php bin/console ScriptAddDetailTransactionCompte

php bin/console VMouvementDebitCaisseSiege
php bin/console VMouvementDebitBanque
php bin/console VMouvementCreditCaisse
php bin/console VMouvementCreditBanque
php bin/console VDebitCaisseMensuel
php bin/console VDebitBanqueMensuel