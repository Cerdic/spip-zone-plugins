CHANGELOG
=========

Version 3.0.5
-------------

Compatibilité minimale avec SPIP 2.1 : un site 2.1 peut servir de source de données.
Notons que PHP 5.4 minimum est nécessaire…

Version 3.0
-----------

Suppression de tout ce qui concerne la connexion SSH et Rsync, trop compliqué à utiliser
en fonction des différentes permissions des serveurs ;

Gestion d'une relation client / serveur sécurisée pour les transferts, par HTTP,
(un peu comme le plugin 'migration' d'ailleurs) qui prend bien plus de temps pour effectuer
certaines taches entre les serveurs, tel que la syncronisation de IMG/ par exemple,
mais qui simplifie grandement la mise en place de l'outil.




Version 2.6
-----------

- prise en compte de login-path pour exporter la base de données, si renseigné.
  Pour cela, utiliser la constante MIGRATEUR_SOURCE_SQL_LOGIN_PATH.

Version 2.5
-----------

- restructuration du code pour plus de clarté.
- compatible SPIP 3.1


Version 2.4
-----------

- pouvoir exécuter des commandes sur un serveur distant
- l'export de la bdd peut se faire sur un serveur distant

Version 2.3
-----------

- le migrateur peut maintenant s'appliquer entre serveurs via clé ssh
- pouvoir accéder à un spip source sur un serveur distant
- rsync de IMG peut se faire sur un spip distant
