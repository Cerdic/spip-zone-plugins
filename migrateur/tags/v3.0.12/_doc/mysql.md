Configurer Mysql
================

Mysql (et mysqldump), selon la version, peut à juste titre raler, si on
exécute une commande qui transmet le mot de passe directement avec `--password *******`.
Question de sécurité.

Cela ajoute du coup une ligne en tête du fichier .sql ou .sql.gz généré
par mysqldump, ce qui rend celui-ci erroné.

Pour sécuriser, mysql a prévu une option : `login_path` indique à mysql
l'endroit où sont stockés les identifiants de connexion, qu'on aura au
préalable renseignés avec la commande `mysql_config_editor`.

Donc, pour l'utiliser il faut créer, pour l'utilisateur qui se connecte en ssh
au serveur, un login_path spécifique. On peut se reporter à ce site :
http://blog.georgio.fr/mysql_config_editor-un-utilitaire-mysql-en-les-lignes-de-commandes/
Attention dans cet article il y a un piège : `--host` est pour le host,
souvent 'localhost', pas pour indiquer le nom de la base !

Exemple
-------
    mysql_config_editor set --login-path=MON_NOM --host=MON_HOST --user=MON_USER --password
    > Valider puis saisissez le mot de passe...

    mysql_config_editor set --login-path=migrateur_dev --host=localhost --user=migrateur --password
    > Valider puis saisissez le mot de passe...


Constantes de configuration à définir pour le migrateur
-------------------------------------------------------

### Sans login-path :

    // SQL source
    define('MIGRATEUR_SOURCE_SQL_USER', 'migrateur'); 
    define('MIGRATEUR_SOURCE_SQL_PASS', '*********');
    define('MIGRATEUR_SOURCE_SQL_BDD', 'spip'); // nom de la bdd


### Avec login-path :

    // SQL source
    define('MIGRATEUR_SOURCE_SQL_LOGIN_PATH', 'migrateur_dev');
    define('MIGRATEUR_SOURCE_SQL_BDD', 'spip'); // nom de la bdd
