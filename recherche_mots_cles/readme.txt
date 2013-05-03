Nom du plugin : recherche sti
Auteurs : Vincent Grimaud et Jean-Luc Padiolleau
Organisme : Académie d'Orléans-Tours
Site académique du site STI
Version : 0.2
Date de création : Lundi 7 février 2011
Date de révision : Mardi 1er mars 2012

============================================================
| Plugin pour la recherche multi-critères sur le site STI  |
============================================================
Ce plugin permet d'effectuer une recherche d'articles dans spip par rapport à des critères rentrés sous forme de mots clés et associés dans des groupes de cohérence.

1 - Arborescence du plugin
==========================

Le plugin "recherche_sti" est à installer dans le dossier "plugins" de SPIP.

+- plugins
	|
	+- recherche_sti
	|			|- action	<- Contient les fichiers php exécutés pour les traitements des actions de l'espace privé
	|			+- base		<- Contient les fichiers php relatifs de gestion des tables
	|			|	|
	|			|	|- recherche_sti_tables.php		<- déclaration des tables rajoutées pour notre plugin
	|			|	|- recherche_sti_upgrade.php		<- installation / suppression / mise à jour des tables
	|			|- exec		<- Contient les fichiers php qui sont exécutés par les boutons de l'espace privé
	|			|
	|			|- images	<- Contient les images / icônes du plugin
	|			|- modeles	<- Contient les modèles (petits squelettes) du plugin pour la partie publique
	|				|
	|				|- recherche.html		<- modèle pour l'affichage du module de recherche dans la partie publique (avec balise "recherche")
	|- plugin.xml	<- Contient la déclaration du plugin
	|- readme.txt	<- Ce fichier !
	|- TODO.txt	<- Liste du travail ou des suggestions d'améliorations du code du plugin
	|- COPYING.txt	<- contenu de la licence GPL
	|- recherche_sti_options.php	<- Contient la déclaration de variable (chemin du plugin, dossier des images, etc..)


2 - Fonctionnalité et utilisation du Plugin
===========================================
Le plugin comprend une interface privée de configuration et une interface publique permettant la recherche multicritère d'articles dans la ou les rubriques choisis.
Les critères sont au préalable définis sous forme de mots clés SPIP rassemblés dans des groupes.

2-1 Interface privée de configuration
-------------------------------------
L'interface de configuration doit permettre à un administrateur de définir les groupes de mots clés qui sont associés à la recherche multi-critères ainsi que leur mode présentation dans
l'interface publique. Elle permet aussi de définir la rubrique que va afficher cette recherche multicritère.  Lors de l'installation du plugin, deux nouvelles table SPIP sont créées :
- 	la table "spip_sti_groupes_mots_cles" qui contient la liste des groupes de mots clés (id et titre) qui vont être utilisé pour la recherche ainsi que le mode de présentation dans 
	l'interface publique (liste déroulante ou cases à cocher). 
	Cette table contient donc respectivement les champs :
	-	"id_groupe"du type "bigint(21)",
	-	"titre" du type "texte",
	-	"mode_presentation" du type "tinyint".
- La table "spip_sti_rubriques" qui contient la liste des rubriques (id et titre) faisant appel à la recherche multi-critères. Cette table contient donc respectivement les champs :
	-	"id_rubrique" du type "bigint(21)",
	-	"titre" du type "texte".
- Le bouton de configuration (déclaré dans plugin.xml) exécute le script exec/recherche_sti_bouton.php pour créer le formulaire de sélection des groupes de mots clés et des rubriques.
	Le traitement de ce formulaire est exécuté dans le fichier action/recherche_sti_configuration.php.

2-1 Interface publique de recherche multicritères
-------------------------------------------------
L'interface de recherche multicritères s'insère dans un article ou une rubrique en plaçant simplement la balise <recherche1> (le chiffre 1 doit être remplacer par le n° d'occurence d'appel au plugin).
Il est aussi possible de l'insérer directement dans le squelette avec la balise [(#MODELE{recherche}] (à vérifier).
Le fichier correspondant à la programmation de ce modèle SPIP se nomme "recherche.html" et est placé dans le dossier "modeles".
