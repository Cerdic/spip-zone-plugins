<?php
include_spip('inc/presentation');
include_spip('inc/publiHAL_gestion');
function exec_publihal_uninstall(){
	global $connect_statut, $connect_toutes_rubriques;
	debut_page(_T('Désinstallation des tables et données publiHAL'), "", "");
	echo "<br /><br />";
	gros_titre(_T('Désinstallation pour publiHAL'));
	debut_cadre_relief(  "", false, "", $titre = _T('Actions de désinstallation'));
	debut_boite_info();
	echo '<br>';
	$r=publiHAL_uninstall();
	echo $r.'<br>';
	if($r&1)	{echo '!!! suppression groupe de mots "publiHAL_Type_de_document"';}
	else {echo '0 suppression pour le groupe de mots "publiHAL_Type_de_document"';}
	echo '<br>';
	if($r&2)	{echo '!!! suppression groupe de mots "publiHAL_auteurs_publi"';}
	else {echo '0 suppression pour le groupe de mots "publiHAL_auteurs_publi"';}
	echo '<br>';
	if($r&4)	{echo '!!! suppression groupe de mots "publiHAL_Labo_publi"';}
	else {echo '0 suppression pour le groupe de mots "publiHAL_Labo_publi"';}
	echo '<br>';
	if($r&8)	{echo '!!! suppression de la base "spip_mots_syndic_articles"';}
	else {echo '0 suppression de base ';}
	echo '<br>';
	if($r&16)	{echo '!!! suppression de la base "publiHAL_Keywords"';}
	else {echo '0 suppression de base ';}
	echo '<br>';
	echo '<br><b>ALLER DANS CONFIGURATION>PLUGINS ET DESINSTALLER publication HALL avec RSS</b>';
	echo '<br>';
	fin_boite_info();
	fin_cadre_relief();
	fin_page();
	exit;
}
?>