<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/actions'); // *action_auteur et determine_upload

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
echo debut_gauche('',true);
debut_boite_info(true);
echo "Le plugin SWFupload permet de t&eacute;l&eacute;charger des fichiers dans votre dossier ".determine_upload()." m&ecirc;me si vous n'avez pas d'acc&egrave;s ftp.<br/><br/>Vous pourrez alors acc&egrave;der &agrave; ces fichiers lors de l'ajout de documents ou images &agrave; un article.";
fin_boite_info(true);
echo "<br/>";
debut_boite_info(true);
echo "<a href='?exec=swfupload_vider' onclick='return confirm(\"Etes vous sûr ?\");'>Vider le dossier</a> ".determine_upload();
echo "<br/><strong>Attention en cliquant sur ce lien vous supprimerez tous les fichiers et dossiers.</strong>";
fin_boite_info(true);

echo debut_droite('',true);
echo gros_titre(_T('swfupload:titre_swfupload'),'',false);
echo '<form id="form1" action="index.php" method="post" enctype="multipart/form-data">
		<div>'._T('swfupload:texte_swfupload').'</div>

		<div class="content">
			<fieldset class="flash" id="fsUploadProgress">
				<legend>'._T('swfupload:texte_uploadqueue').'</legend>
			</fieldset>
			<div id="divStatus">0 '._T('swfupload:texte_filesupload').'</div>
			<div style="padding-left: 5px;">
				<span id="spanButtonPlaceholder"></span>
				<input id="btnCancel" type="button" value="'._T('swfupload:texte_cancelupload').'" onclick="cancelQueue(swfu);" disabled="disabled"  style="margin-left: 2px; height: 22px; font-size: 8pt;" /><br />

			</div>
		</div>
	</form>';
echo fin_gauche();
echo fin_page();
}

?>