<?
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/actions'); // *action_auteur et determine_upload

function exec_swfupload_vider()
{
global $connect_statut, $connect_login, $connect_toutes_rubriques, $couleur_foncee, $flag_gz, $options,$supp;

if ($connect_statut != '0minirezo' ) {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('icone_swfupload'), "naviguer", "plugin");
		echo _T('avis_non_acces_page');
		echo fin_gauche(), fin_page();
		exit;
	}
	
// Change the upload-root
$upload_dir = determine_upload();

$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('swfupload:titre_swfupload'));
echo "<br />";
echo debut_gauche();
debut_boite_info();
echo "Le plugin Jupload permet de t&eacute;l&eacute;charger des fichiers dans votre dossier ".determine_upload()." m&ecirc;me si vous n'avez pas d'acc&egrave;s ftp.<br/><br/>Vous pourrez alors acc&egrave;der &agrave; ces fichiers lors de l'ajout de documents ou images &agrave; un article.";
fin_boite_info();
echo "<br/>";
debut_boite_info();
echo "<a href='?exec=swfupload_vider'>Vider le dossier</a> ".determine_upload();
fin_boite_info();

echo debut_droite();
echo gros_titre('Jupload - Suppression des fichiers');
echo debut_cadre_relief('image-24.gif',true);
swfupload_vider_upload($upload_dir);
echo fin_cadre_relief(true);
echo fin_gauche();
echo fin_page();
}

function swfupload_vider_upload($dirname)
{
    if (!file_exists($dirname)) {
        return false;
    }
 
    // Supprime fichier
    if (is_file($dirname) || is_link($dirname)) {
		echo $dirname." : Fichier supprim&eacute;<br/>"; 
		return @unlink($dirname);
    }
 
    // Boucle sur le repertoire
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
        // Skip pointeurs
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Recursivite - ne pas supprimer le fichier upload de l admin
		//if (dir_name$_ju_uploadRoot
        swfupload_vider_upload($dirname . DIRECTORY_SEPARATOR . $entry);

    }
 
    // Clean up
    $dir->close();
	if ($dirname!=determine_upload()) {
	echo $dirname." : Dossier supprim&eacute;<br/>";
    return @rmdir($dirname);}
}
 
?>