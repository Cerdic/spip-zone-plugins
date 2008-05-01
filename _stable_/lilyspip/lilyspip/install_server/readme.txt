*********************************************
*********************************************
** Installer un serveur Lilypond pour SPIP **
*********************************************
*********************************************

**********************************
* Installation PHP non s�curis�e *
**********************************

Installer Lilypond en utilisant le script d'installation : http://lilypond.org/web/install/

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

Puis ImageMagick s'il n'est pas d�j� install� : http://www.imagemagick.org/script/install-source.php#unix
disponible aussi dans les d�pots Ubuntu).

Copier le fichier "server.php" dans le dossier du serveur Web et adapter les variables
globales :

	$convert_bin = "/usr/bin/convert" ;
	$lilypond_bin = "/usr/local/bin/lilypond" ;
	$lilypond_version = "2.10.11" ;

Cr�er le dossier "CACHE/lilyspip/" au m�me niveau que le fichier bashserver.php.

Lilypond et ex�cut� en mode s�curis� (mode safe) mais il peut boucler ind�finiment.



*********************************************************************
* Installation du serveur en utilisant un script bash (recommand�e) *
*********************************************************************

Cette installation utilise la commande bash "ulimit -t" pour limiter la dur�e d'ex�cution
de Lilypond.

La proc�dure d'installation de Lilypond et ImageMagick est identique � celle du chapitre
pr�c�dent.

Copier dans un dossier ext�rieur au serveur Web (par exemple /home/script/) le script
"lilypond.sh" puis l'adapter si n�cessaire :

	#!/bin/sh
	ulimit -t 60
	cmd="/usr/local/bin/lilypond --safe --png --output=$1 $2 2> $1"
	eval $cmd
	
o� 60 correspond � 60 secondes de temps CPU avant que le processus ne soit stopp�.

Copier le fichier "bashserver.php" dans le dossier du serveur web et adapter les variables
globales :

	$convert_bin = "/usr/bin/convert" ;
	$script="/home/script/lilypond.sh" ; //chemin du script bash
	$lilypond_version = "2.10.11" ;

Cr�er le dossier "CACHE/lilysip/" au m�me niveau que le fichier bashserver.php.

Si PHP est utilis� en safe mode, les fonctions comme exec() et toutes celles qui
permettent l'ex�cution en ligne de commande refuseront d'ex�cuter des programmes qui
ne sont pas dans le dossier safe_mode_exec_dir.



********************************************************************
* Installation du serveur en mode jail en utilisant un script bash *
********************************************************************

Cette installation permettant d'utiliser toutes les fonctionnalit�s de Lilypond (include ...)
n'a pas �t� test�e.

La commande � utiliser est :

	-j,--jail=user,group,jail,dir

Elle permet de sp�cifier l'utilisateur disposant de droits restreints, le groupe et le dossier
d'ex�cution de Lilypond (cf. http://lilypond.org/doc/v2.11/Documentation/user/lilypond/Invoking-lilypond).

Pour pouvoir utiliser le mode jail il est n�cessaire d'ex�cuter Lilypond avec sudo sans mot
de passe en ajoutant dans le fichier "/etc/sudoers" :

	utilisateur ALL=NOPASSWD:/home/script/lilyspip.sh

o� l'utilisateur est "apache" ou "nobody" suivant la version.
