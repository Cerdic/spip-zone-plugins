*********************************************
*********************************************
** Installer un serveur Lilypond pour SPIP **
*********************************************
*********************************************

**********************************
* Installation PHP non sécurisée *
**********************************

Installer Lilypond en utilisant le script d’installation : http://lilypond.org/web/install/

	$ sudo ./lilypond-2.10.11-1.linux-x86.sh
	Password:

	LilyPond installer for version 2.10.11 release 1.
	Use --help for help
	You're about to install lilypond in /usr/local/lilypond/
	A script in /usr/local/bin/ will be created as a shortcut.
	Press ^C to abort, or Enter to proceed

	Making /usr/local/lilypond/
	Creating script /usr/local/bin/lilypond
	Creating script /usr/local/bin/lilypond-wrapper.python
	Creating script /usr/local/bin/lilypond-wrapper.guile
	Creating script /usr/local/bin/uninstall-lilypond
	Untarring ./lilypond-2.10.11-1.linux-x86.sh

	To uninstall lilypond, run
	/usr/local//bin/uninstall-lilypond

	For license and warranty information, consult
	/usr/local/lilypond/license/README

Puis ImageMagick s’il n’est pas déjà installé : http://www.imagemagick.org/script/install-source.php#unix
disponible aussi dans les dépots Ubuntu).

Copier le fichier "server.php" dans le dossier du serveur Web et adapter les variables
globales :

	$convert_bin = "/usr/bin/convert" ;
	$lilypond_bin = "/usr/local/bin/lilypond" ;
	$lilypond_version = "2.10.11" ;

Créer le dossier "CACHE/lilyserv/" à la racine du dossier Web.

Lilypond et exécuté en mode sécurisé (mode safe) mais il peut boucler indéfiniment.



*********************************************************************
* Installation du serveur en utilisant un script bash (recommandée) *
*********************************************************************

Cette installation utilise la commande bash "ulimit -t" pour limiter la durée d’exécution
de Lilypond.

La procédure d’installation de Lilypond et ImageMagick est identique à celle du chapitre
précédent.

Copier dans un dossier extérieur au serveur Web (par exemple /home/script/) le script
"lilypond.sh" puis l’adapter si nécessaire :

	#!/bin/sh
	ulimit -t 60
	cmd="/usr/local/bin/lilypond --safe --png --output=$1 $2 2> $1"
	eval $cmd
	
où 60 correspond à 60 secondes de temps CPU avant que le processus ne soit stoppé.

Copier le fichier "bashserver.php" dans le dossier du serveur web et adapter les variables
globales :

	$convert_bin = "/usr/bin/convert" ;
	$script="/home/script/lilypond.sh" ; //chemin du script bash
	$lilypond_version = "2.10.11" ;

Créer le dossier "CACHE/lilyserv/" à la racine du dossier Web.

Si PHP est utilisé en safe mode, les fonctions comme exec() et toutes celles qui
permettent l’exécution en ligne de commande refuseront d’exécuter des programmes qui
ne sont pas dans le dossier safe_mode_exec_dir.



********************************************************************
* Installation du serveur en mode jail en utilisant un script bash *
********************************************************************

Cette installation permettant d’utiliser toutes les fonctionnalités de Lilypond (include ...)
n’a pas été testée.

La commande à utiliser est :

	-j,--jail=user,group,jail,dir

Elle permet de spécifier l’utilisateur disposant de droits restreints, le groupe et le dossier
d’exécution de Lilypond (cf. http://lilypond.org/doc/v2.11/Documentation/user/lilypond/Invoking-lilypond).

Pour pouvoir utiliser le mode jail il est nécessaire d’exécuter Lilypond avec sudo sans mot
de passe en ajoutant dans le fichier "/etc/sudoers" :

	utilisateur ALL=NOPASSWD:/home/script/lilyspip.sh

où l’utilisateur est "apache" ou "nobody" suivant la version.
