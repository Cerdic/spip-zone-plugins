<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/actions'); // *action_auteur et determine_upload
include_spip('inc/swfupload_form'); // *action_auteur et determine_upload

session_start();
$_SESSION["file_info"] = array();


function exec_swfupload_admin_dist()
{
global $connect_statut, $connect_login, $connect_toutes_rubriques, $couleur_foncee, $flag_gz, $options,$supp;

if ($connect_statut != '0minirezo' ) {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('icone_swfupload'), "naviguer", "plugin");
		echo _T('avis_non_acces_page');
		echo fin_gauche(), fin_page();
		exit;
	}

$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('swfupload:titre_swfupload'));
echo "<br />";
echo debut_gauche();
debut_boite_info();
echo "Le plugin SWFupload permet de t&eacute;l&eacute;charger des fichiers dans votre dossier ".determine_upload()." m&ecirc;me si vous n'avez pas d'acc&egrave;s ftp.<br/><br/>Vous pourrez alors acc&egrave;der &agrave; ces fichiers lors de l'ajout de documents ou images &agrave; un article.";
fin_boite_info();
echo "<br/>";
debut_boite_info();
echo "<a href='?exec=swfupload_vider'>Vider le dossier</a> ".determine_upload();
echo "<br/><strong>Attention en cliquant sur ce lien vous supprimerez tous les fichiers et dossiers.</strong>";
fin_boite_info();

echo debut_droite();
echo gros_titre(_T('swfupload:titre_swfupload'));
echo swfupload_form();
echo fin_gauche();
echo fin_page();
}



?>