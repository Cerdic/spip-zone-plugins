<?php

// French Language Module for v2.3 (translated by Olivier Pariseau & the QuiX project)

//$GLOBALS['spx']["charset"] = "iso-8859-1";

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(
	'date_fmt'      => "d/m/Y H:i",
	// error
	"error"			=> "ERREUR(S)",
	"back"			=> "Page pr&eacute;c&eacute;dente",
	
	// root
	"home"			=> "Le r&eacute;pertoire home n'existe pas, v&eacute;rifiez vos pr&eacute;f&eacute;rences.",
	"abovehome"		=> "Le r&eacute;pertoire courant n'a pas l'air d'&ecirc;tre au-dessus du r&eacute;pertoire home.",
	"targetabovehome"	=> "Le r&eacute;pertoire cible n'a pas l'air d'&ecirc;tre au-dessus du r&eacute;pertoire home.",
	
	// exist
	"direxist"		=> "Ce r&eacute;pertoire n'existe pas.",
	//"filedoesexist"	=> "Ce fichier existe deja.",
	"fileexist"		=> "Ce fichier n'existe pas.",
	"itemdoesexist"		=> "Cet item existe deja.",
	"itemexist"		=> "Cet item n'existe pas.",
	"targetexist"		=> "Le r&eacute;pertoire cible n'existe pas.",
	"targetdoesexist"	=> "L'item cible existe d&eacute;j&eacute;.",
	
	// open
	"opendir"		=> "Impossible d'ouvrir le r&eacute;pertoire.",
	"readdir"		=> "Impossible de lire le r&eacute;pertoire.",
	
	// access
	"accessdir"		=> "Vous n'&ecirc;tes pas autoris&eacute; &agrave; acc&eacute;der &agrave; ce r&eacute;pertoire.",
	"accessfile"		=> "Vous n'&ecirc;tes pas autoris&eacute; &agrave; acc&eacute;der &agrave; ce fichier.",
	"accessitem"		=> "Vous n'&ecirc;tes pas autoris&eacute; &agrave; acc&eacute;der &agrave; cet item.",
	"accessfunc"		=> "Vous ne pouvez pas utiliser cette fonction.",
	"accesstarget"		=> "Vous n'&ecirc;tes pas autoris&eacute; &agrave; acc&eacute;der au repertoire cible.",
	
	// actions
	"permread"		=> "Lecture des permissions &eacute;chou&eacute;e.",
	"permchange"		=> "Changement des permissions &eacute;chou&eacute;.",
	"openfile"		=> "Ouverture du fichier &eacute;chou&eacute;e.",
	"savefile"		=> "Sauvegarde du fichier &eacute;chou&eacute;e.",
	"createfile"		=> "Cr&eacute;ation du fichier &eacute;chou&eacute;e.",
	"createdir"		=> "Cr&eacute;ation du r&eacute;pertoire &eacute;chou&eacute;e.",
	"uploadfile"		=> "Envoie du fichier &eacute;chou&eacute;.",
	"copyitem"		=> "La copie a &eacute;chou&eacute;e.",
	"moveitem"		=> "Le d&eacute;placement a &eacute;chou&eacute;.",
	"delitem"		=> "La supression a &eacute;chou&eacute;e.",
	"chpass"		=> "Le changement de mot de passe a &eacute;chou&eacute;.",
	"deluser"		=> "La supression de l'usager a &eacute;chou&eacute;e.",
	"adduser"		=> "L'ajout de l'usager a &eacute;chou&eacute;e.",
	"saveuser"		=> "La sauvegarde de l'usager a &eacute;chou&eacute;e.",
	"searchnothing"		=> "Vous devez entrez quelquechose &agrave; chercher.",
	
	// misc
	"miscnofunc"		=> "Fonctionalit&eacute; non disponible.",
	"miscfilesize"		=> "La taille du fichier exc&egrave;de la taille maximale autoris&eacute;e.",
	"miscfilepart"		=> "L'envoi du fichier n'a pas &eacute;t&eacute; compl&eacute;t&eacute;.",
	"miscnoname"		=> "Vous devez entrer un nom.",
	"miscselitems"		=> "Vous n'avez s&eacute;lectionn&eacute; aucuns item(s).",
	"miscdelitems"		=> "Ê&Ecirc;tes-vous certain de vouloir supprimer ces \"+num+\" item(s)?",
	"miscdeluser"		=> "Ê&Ecirc;tes-vous certain de vouloir supprimer l'usager '\"+user+\"'?",
	"miscnopassdiff"	=> "Le nouveau mot de passe est indentique au pr&eacute;c&eacute;dent.",
	"miscnopassmatch"	=> "Les mots de passe diff&eacute;rent.",
	"miscfieldmissed"	=> "Un champs requis n'a pas &eacute;t&eacute; rempli.",
	"miscnouserpass"	=> "Nom d'usager ou mot de passe invalide.",
	"miscselfremove"	=> "Vous ne pouvez pas supprimer votre compte.",
	"miscuserexist"		=> "Ce nom d'usager existe d&eacute;j&agrave;.",
	"miscnofinduser"	=> "Usager non trouv&eacute;.",

	// links
	"permlink"		=> "CHANGER LES PERMISSIONS",
	"editlink"		=> "&Eacute;DITER",
	"downlink"		=> "T&Eacute;L&Eacute;CHARGER",
	"uplink"		=> "PARENT",
	"homelink"		=> "HOME",
	"reloadlink"		=> "RAFRA&icirc;CHIR",
	"copylink"		=> "COPIER",
	"movelink"		=> "D&Eacute;PLACER",
	"dellink"		=> "SUPPRIMER",
	"comprlink"		=> "ARCHIVER",
	"adminlink"		=> "ADMINISTRATION",
	"logoutlink"		=> "D&Eacute;CONNECTER",
	"uploadlink"		=> "ENVOYER",
	"searchlink"		=> "RECHERCHER",
	
	// list
	"nameheader"		=> "Nom",
	"sizeheader"		=> "Taille",
	"typeheader"		=> "Type",
	"modifheader"		=> "Modifi&eacute;",
	"permheader"		=> "Perm's",
	"owner_group"		=> "Prop./Groupe",
	"actionheader"		=> "Actions",
	"pathheader"		=> "Chemin",
	
	// buttons
	"btncancel"		=> "Annuler",
	"btnsave"		=> "Sauver",
	"btnchange"		=> "Changer",
	"btnreset"		=> "R&eacute;initialiser",
	"btnclose"		=> "Fermer",
	"btncreate"		=> "Cr&eacute;er",
	"btnsearch"		=> "Chercher",
	"btnupload"		=> "Envoyer",
	"btncopy"		=> "Copier",
	"btnmove"		=> "D&eacute;placer",
	"btnlogin"		=> "Connecter",
	"btnlogout"		=> "D&eacute;connecter",
	"btnadd"		=> "Ajouter",
	"btnedit"		=> "&Eacute;diter",
	"btnremove"		=> "Supprimer",
	
	// actions
	"actdir"		=> "R&eacute;pertoire",
	"actperms"		=> "Changer les permissions",
	"actedit"		=> "&Eacute;diter le fichier",
	"actsearchresults"	=> "R&eacute;sultats de la recherche",
	"actcopyitems"		=> "Copier le(s) item(s)",
	"actcopyfrom"		=> "Copier de /%s à /%s ",
	"actmoveitems"		=> "D&eacute;placer le(s) item(s)",
	"actmovefrom"		=> "D&eacute;placer de /%s à /%s ",
	"actlogin"		=> "Connecter",
	"actloginheader"	=> "Connecter pour utiliser QuiXplorer",
	"actadmin"		=> "Administration",
	"actchpwd"		=> "Changer le mot de passe",
	"actusers"		=> "Usagers",
	"actarchive"		=> "Archiver le(s) item(s)",
	"actupload"		=> "Envoyer le(s) fichier(s)",
	
	// misc
	"miscitems"		=> "Item(s)",
	"miscfree"		=> "Disponible",
	"miscusername"		=> "Usager",
	"miscpassword"		=> "Mot de passe",
	"miscoldpass"		=> "Ancien mot de passe",
	"miscnewpass"		=> "Nouveau mot de passe",
	"miscconfpass"		=> "Confirmer le mot de passe",
	"miscconfnewpass"	=> "Confirmer le nouveau mot de passe",
	"miscchpass"		=> "Changer le mot de passe",
	"mischomedir"		=> "R&eacute;pertoire home",
	"mischomeurl"		=> "URL home",
	"miscshowhidden"	=> "Voir les items cach&eacute;s",
	"mischidepattern"	=> "Cacher pattern",
	"miscperms"		=> "Permissions",
	"miscuseritems"		=> "(nom, r&eacute;pertoire home, Voir les items cach&eacute;s, permissions, actif)",
	"miscadduser"		=> "ajouter un usager",
	"miscedituser"		=> "&eacute;diter l'usager '%s'",
	"miscactive"		=> "Actif",
	"misclang"		=> "Langage",
	"miscnoresult"		=> "Aucun r&eacute;sultats.",
	"miscsubdirs"		=> "Rechercher dans les sous-r&eacute;pertoires",
	"miscpermnames"		=>
		"Lecture seulement/Modifier/Changement le mot de passe/Modifier & Changer le mot de passe/Administrateur",
	"miscyesno"		=> "Oui/Non/O/N",
	"miscchmod"		=> "Propri&eacute;taire/Groupe/Publique"
);
?>
