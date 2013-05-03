Nom du plugin : recherche sti
Auteurs : Vincent Grimaud et Jean-Luc Padiolleau
Organisme : Acad�mie d'Orl�ans-Tours
Site acad�mique du site STI
Version : 0.2
Date de cr�ation : Lundi 7 f�vrier 2011
Date de r�vision : Mardi 1er mars 2012

============================================================
| Plugin pour la recherche multi-crit�res sur le site STI  |
============================================================
Ce plugin permet d'effectuer une recherche d'articles dans spip par rapport � des crit�res rentr�s sous forme de mots cl�s et associ�s dans des groupes de coh�rence.

1 - Arborescence du plugin
==========================

Le plugin "recherche_sti" est � installer dans le dossier "plugins" de SPIP.

+- plugins
	|
	+- recherche_sti
	|			|- action	<- Contient les fichiers php ex�cut�s pour les traitements des actions de l'espace priv�
	|			+- base		<- Contient les fichiers php relatifs de gestion des tables
	|			|	|
	|			|	|- recherche_sti_tables.php		<- d�claration des tables rajout�es pour notre plugin
	|			|	|- recherche_sti_upgrade.php		<- installation / suppression / mise � jour des tables
	|			|- exec		<- Contient les fichiers php qui sont ex�cut�s par les boutons de l'espace priv�
	|			|
	|			|- images	<- Contient les images / ic�nes du plugin
	|			|- modeles	<- Contient les mod�les (petits squelettes) du plugin pour la partie publique
	|				|
	|				|- recherche.html		<- mod�le pour l'affichage du module de recherche dans la partie publique (avec balise "recherche")
	|- plugin.xml	<- Contient la d�claration du plugin
	|- readme.txt	<- Ce fichier !
	|- TODO.txt	<- Liste du travail ou des suggestions d'am�liorations du code du plugin
	|- COPYING.txt	<- contenu de la licence GPL
	|- recherche_sti_options.php	<- Contient la d�claration de variable (chemin du plugin, dossier des images, etc..)


2 - Fonctionnalit� et utilisation du Plugin
===========================================
Le plugin comprend une interface priv�e de configuration et une interface publique permettant la recherche multicrit�re d'articles dans la ou les rubriques choisis.
Les crit�res sont au pr�alable d�finis sous forme de mots cl�s SPIP rassembl�s dans des groupes.

2-1 Interface priv�e de configuration
-------------------------------------
L'interface de configuration doit permettre � un administrateur de d�finir les groupes de mots cl�s qui sont associ�s � la recherche multi-crit�res ainsi que leur mode pr�sentation dans
l'interface publique. Elle permet aussi de d�finir la rubrique que va afficher cette recherche multicrit�re.  Lors de l'installation du plugin, deux nouvelles table SPIP sont cr��es :
- 	la table "spip_sti_groupes_mots_cles" qui contient la liste des groupes de mots cl�s (id et titre) qui vont �tre utilis� pour la recherche ainsi que le mode de pr�sentation dans 
	l'interface publique (liste d�roulante ou cases � cocher). 
	Cette table contient donc respectivement les champs :
	-	"id_groupe"du type "bigint(21)",
	-	"titre" du type "texte",
	-	"mode_presentation" du type "tinyint".
- La table "spip_sti_rubriques" qui contient la liste des rubriques (id et titre) faisant appel � la recherche multi-crit�res. Cette table contient donc respectivement les champs :
	-	"id_rubrique" du type "bigint(21)",
	-	"titre" du type "texte".
- Le bouton de configuration (d�clar� dans plugin.xml) ex�cute le script exec/recherche_sti_bouton.php pour cr�er le formulaire de s�lection des groupes de mots cl�s et des rubriques.
	Le traitement de ce formulaire est ex�cut� dans le fichier action/recherche_sti_configuration.php.

2-1 Interface publique de recherche multicrit�res
-------------------------------------------------
L'interface de recherche multicrit�res s'ins�re dans un article ou une rubrique en pla�ant simplement la balise <recherche1> (le chiffre 1 doit �tre remplacer par le n� d'occurence d'appel au plugin).
Il est aussi possible de l'ins�rer directement dans le squelette avec la balise [(#MODELE{recherche}] (� v�rifier).
Le fichier correspondant � la programmation de ce mod�le SPIP se nomme "recherche.html" et est plac� dans le dossier "modeles".
