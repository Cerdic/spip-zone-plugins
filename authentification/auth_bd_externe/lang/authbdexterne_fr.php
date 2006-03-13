<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(
'titre_plugin' => 'Authentification des auteurs via une BD externe',
'titre_param_connexion_db' => 'Etape 1/3 : parametrage de la connexion au serveur de BD',
'info_serveur' => 'Type de serveur de BD',
'pear_warning' => 'La librairie Pear DB n\'est pas installée ou accessible. L\'authentification externe ne peut s\'effectuer que via un serveur MySQL. Installez le <a href="http://pear.php.net/package/DBDB">package Pear DB</a> si vous avez besoin de mettre en place une authentification sur un des serveurs suivants : PostgreSQL, InterBase, Mini SQL, Microsoft SQL Server, Oracle 7/8/8i, ODBC (Open Database Connectivity), SyBase, Informix, FrontBase.',
'info_serveur_hostname' => 'Nom du serveur',
'info_serveur_login' => 'Identifiant de connexion',
'info_serveur_password' => 'Mot de passe de connexions',
'info_serveur_database' => 'Nom de la base',
'db_connect_warning' => 'La connexion au serveur de base de donnees a echoue. Verifiez les parametres de connexion que vous avez saisis !',
'titre_param_db' => 'Etape 2/3 : parametrage table(s) de la base externe',
'aide1' => 'Un profil utilisateur doit être associé à une clé unique. Si les profils sont stockés dans deux tables (cas par exemple d\'une table pour les identifiants de connexion et d\'une seconde table contenant les informations personnelles), il est possible de réaliser une jointure sur la clef.',
'info_bd_table' => 'Table auteurs',
'info_bd_champ_cle' => 'Champ clef (identifiant unique)',
'info_bd_table_jointure' => 'Table pour jointure [optionnel]',
'titre_param_champs' => 'Etape 3/3 : parametrage des champs de la base externe',
'titre_section2' => 'Specification des champs pour l\'authentification et type de cryptage du mot de passe',
'info_bd_champ_login_ext' => 'Champ login',
'info_bd_champ_passwd' => 'Champ mot de passe',
'info_bd_type_passwd' => 'Type de cryptage du mot de passe',
'info_bd_champ_alea' => 'Champ alea [uniquement pour challenge md5 spip]',
'titre_section3' => 'Specification des champs relatifs aux informations personnelles',
'aide3' => 'Les champs suivants sont optionnels. Le champ prenom s\'il est specifié est utilisé pour déterminer le nom d\'un auteur qui résulte alors de la concaténation du prénom et du nom. Les autres champs correspondent aux informations associées à un auteur spip.',
'info_bd_champ_prenom' => 'Champ prenom',
'info_bd_champ_nom' => 'Champ nom',
'info_bd_champ_bio' => 'Champ biographie',
'info_bd_champ_email' => 'Champ email',
'info_bd_champ_nom_site' => 'Champ nom du site',
'info_bd_champ_url_site' => 'Champ URL du site',
'info_bd_champ_pgp' => 'Champ clé PGP',
'titre_section4' => 'Specification du champ determinant le statut des auteurs',
'aide4' => 'Le champ statut est optionnel.<br /><br />Dans le cas où il n\'est pas spécifié, tous les utilisateurs sont par défauts de simples rédacteurs (leurs statuts pouvant être changé en utilisant la section auteurs de l\'interface spip.<br /><br />Si le champ statut est renseigné, il faut également renseigner la ou les valeurs associées aux statuts de rédacteur et d\'administrateur : si plusieurs valeurs sont possibles, elles doivent être séparées par le caractère ; (point-virgule). Aucun accès à l\'interface privée n\'est autorisée pour les individus dont la valeur du champ statut différe des valeurs associées aux statuts de rédacteur et d\'administrateur.',
'info_bd_champ_statut' => 'Champ statut',
'info_bd_val_redacteur' => 'Valeur(s) associé(s) au statut de rédacteur',
'info_bd_val_administrateur' => 'Valeur(s) associé(s) au statut d\'administrateur'


);


?>