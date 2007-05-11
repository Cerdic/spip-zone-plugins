<?php

// German Language Module for v2.3 (translated by the QuiX project)

//$GLOBALS['spx']["charset"] = "iso-8859-1";

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(
	'date_fmt'      => "d.m.Y H:i",
	// error
	"error"			=> "FEHLER",
	"back"			=> "Zur&uuml;ck",
	
	// root
	"home"			=> "Das Home-Verzeichnis existiert nicht, kontrollieren sie ihre Einstellungen.",
	"abovehome"		=> "Das aktuelle Verzeichnis darf nicht h&ouml;her liegen als das Home-Verzeichnis.",
	"targetabovehome"	=> "Das Zielverzeichnis darf nicht h&ouml;her liegen als das Home-Verzeichnis.",
	
	// exist
	"direxist"		=> "Dieses Verzeichnis existiert nicht.",
	//"filedoesexist"	=> "Diese Datei existiert bereits.",
	"fileexist"		=> "Diese Datei existiert nicht.",
	"itemdoesexist"		=> "Dieses Objekt existiert bereits.",
	"itemexist"		=> "Dieses Objekt existiert nicht.",
	"targetexist"		=> "Das Zielverzeichnis existiert nicht.",
	"targetdoesexist"	=> "Das Zielobjekt existiert bereits.",
	
	// open
	"opendir"		=> "Kann Verzeichnis nicht &ouml;ffnen.",
	"readdir"		=> "Kann Verzeichnis nicht lesen",
	
	// access
	"accessdir"		=> "Zugriff auf dieses Verzeichnis verweigert.",
	"accessfile"		=> "Zugriff auf diese Datei verweigert.",
	"accessitem"		=> "Zugriff auf dieses Objekt verweigert.",
	"accessfunc"		=> "Zugriff auf diese Funktion verweigert.",
	"accesstarget"		=> "Zugriff auf das Zielverzeichnis verweigert.",
	
	// actions
	"permread"		=> "Rechte lesen fehlgeschlagen.",
	"permchange"		=> "Rechte &auml;ndern fehlgeschlagen.",
	"openfile"		=> "Datei &ouml;ffnen fehlgeschlagen.",
	"savefile"		=> "Datei speichern fehlgeschlagen.",
	"createfile"		=> "Datei anlegen fehlgeschlagen.",
	"createdir"		=> "Verzeichnis anlegen fehlgeschlagen.",
	"uploadfile"		=> "Datei hochladen fehlgeschlagen.",
	"copyitem"		=> "Kopieren fehlgeschlagen.",
	"moveitem"		=> "Versetzen fehlgeschlagen.",
	"delitem"		=> "L&ouml;schen fehlgeschlagen.",
	"chpass"		=> "Passwort &auml;ndern fehlgeschlagen.",
	"deluser"		=> "Benutzer l&ouml;schen fehlgeschlagen.",
	"adduser"		=> "Benutzer hinzuf&uuml;gen fehlgeschlagen.",
	"saveuser"		=> "Benutzer speichern fehlgeschlagen.",
	"searchnothing"		=> "Sie m&uuml;ssen etwas zum suchen eintragen.",
	
	// misc
	"miscnofunc"		=> "Funktion nicht vorhanden.",
	"miscfilesize"		=> "Datei ist gr&ouml;&szlig;er als die maximale Gr&ouml;&szlig;e.",
	"miscfilepart"		=> "Datei wurde nur zum Teil hochgeladen.",
	"miscnoname"		=> "Sie m&uuml;ssen einen Namen eintragen",
	"miscselitems"		=> "Sie haben keine Objekt(e) ausgew&auml;hlt.",
	"miscdelitems"		=> "Sollen die \"+num+\" markierten Objekt(e) gel&ouml;scht werden?",
	"miscdeluser"		=> "Soll der Benutzer '\"+user+\"' gel&ouml;scht werden?",
	"miscnopassdiff"	=> "Das neue und das heutige Passwort sind nicht verschieden.",
	"miscnopassmatch"	=> "Passw&ouml;rter sind nicht gleich.",
	"miscfieldmissed"	=> "Sie haben ein wichtiges Eingabefeld vergessen auszuf&uuml;llen",
	"miscnouserpass"	=> "Benutzer oder Passwort unbekannt.",
	"miscselfremove"	=> "Sie k&ouml;nnen sich selbst nicht l&ouml;schen.",
	"miscuserexist"		=> "Der Benutzer existiert bereits.",
	"miscnofinduser"	=> "Kann Benutzer nicht finden.",

	// links
	"permlink"		=> "RECHTE &Auml;NDERN",
	"editlink"		=> "BEARBEITEN",
	"downlink"		=> "HERUNTERLADEN",
	"uplink"		=> "H&Ouml;HER",
	"homelink"		=> "HOME",
	"reloadlink"		=> "ERNEUERN",
	"copylink"		=> "KOPIEREN",
	"movelink"		=> "VERSETZEN",
	"dellink"		=> "L&Ouml;SCHEN",
	"comprlink"		=> "ARCHIVIEREN",
	"adminlink"		=> "ADMINISTRATION",
	"logoutlink"		=> "ABMELDEN",
	"uploadlink"		=> "HOCHLADEN",
	"searchlink"		=> "SUCHEN",
	
	// list
	"nameheader"		=> "Name",
	"sizeheader"		=> "Gr&ouml;&szlig;e",
	"typeheader"		=> "Typ",
	"modifheader"		=> "Ge&auml;ndert",
	"permheader"		=> "Rechte",
	"actionheader"		=> "Aktionen",
	"pathheader"		=> "Pfad",
	
	// buttons
	"btncancel"		=> "Abbrechen",
	"btnsave"		=> "Speichern",
	"btnchange"		=> "&Auml;ndern",
	"btnreset"		=> "Zur&uuml;cksetzen",
	"btnclose"		=> "Schlie&szlig;en",
	"btncreate"		=> "Anlegen",
	"btnsearch"		=> "Suchen",
	"btnupload"		=> "Hochladen",
	"btncopy"		=> "Kopieren",
	"btnmove"		=> "Verschieben",
	"btnlogin"		=> "Anmelden",
	"btnlogout"		=> "Abmelden",
	"btnadd"		=> "Hinzuf&uuml;gen",
	"btnedit"		=> "&Auml;ndern",
	"btnremove"		=> "L&ouml;schen",
	
	// actions
	"actdir"		=> "Verzeichnis",
	"actperms"		=> "Rechte &auml;ndern",
	"actedit"		=> "Datei bearbeiten",
	"actsearchresults"	=> "Suchergebnisse",
	"actcopyitems"		=> "Objekt(e) kopieren",
	"actcopyfrom"		=> "Kopiere von /%s nach /%s ",
	"actmoveitems"		=> "Objekt(e) verschieben",
	"actmovefrom"		=> "Versetze von /%s nach /%s ",
	"actlogin"		=> "Anmelden",
	"actloginheader"	=> "Melden sie sich an um QuiXplorer zu benutzen",
	"actadmin"		=> "Administration",
	"actchpwd"		=> "Passwort &auml;ndern",
	"actusers"		=> "Benutzer",
	"actarchive"		=> "Objekt(e) archivieren",
	"actupload"		=> "Datei(en) hochladen",
	
	// misc
	"miscitems"		=> "Objekt(e)",
	"miscfree"		=> "Freier Speicher",
	"miscusername"		=> "Benutzername",
	"miscpassword"		=> "Passwort",
	"miscoldpass"		=> "Altes Passwort",
	"miscnewpass"		=> "Neues Passwort",
	"miscconfpass"		=> "Best&auml;tige Passwort",
	"miscconfnewpass"	=> "Best&auml;tige neues Passwort",
	"miscchpass"		=> "&Auml;ndere Passwort",
	"mischomedir"		=> "Home-Verzeichnis",
	"mischomeurl"		=> "Home URL",
	"miscshowhidden"	=> "Versteckte Objekte anzeigen",
	"mischidepattern"	=> "Versteck-Filter",
	"miscperms"		=> "Rechte",
	"miscuseritems"		=> "(Name, Home-Verzeichnis, versteckte Objekte anzeigen, Rechte, aktiviert)",
	"miscadduser"		=> "Benutzer hinzuf&uuml;gen",
	"miscedituser"		=> "Benutzer '%s' &auml;ndern",
	"miscactive"		=> "Aktiviert",
	"misclang"		=> "Sprache",
	"miscnoresult"		=> "Suche ergebnislos.",
	"miscsubdirs"		=> "Suche in Unterverzeichnisse",
	"miscpermnames"		=>
		"Nur ansehen/&Auml;ndern/Passwort &auml;ndern/&Auml;ndern & Passwort &auml;ndern/Administrator",
	"miscyesno"		=> "Ja/Nein/J/N",
	"miscchmod"		=> "Besitzer/Gruppe/Publik"
);
?>
