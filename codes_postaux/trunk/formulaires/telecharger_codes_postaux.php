<?php


function formulaires_telecharger_codes_postaux_charger(){
include_spip('codes_postaux_config');
$tab_objet=codes_postaux_config_tab_fichier();
foreach($tab_objet as $key=>&$objet)
{
	//var_dump($fichier);
	if(is_array($objet['url_fichier'])){
		$objet['url_fichier']=implode(',',$objet['url_fichier']);
	}
	if(preg_match('`^http`i',$objet['url_fichier'])){
		$objet=$objet;
		}
	else {
		unset($tab_objet[$key]);
		}
	}


return array('tab_objet'=>$tab_objet);
}





function formulaires_telecharger_codes_postaux_verifier_dist(){
	include_spip('codes_postaux_config');
	include_spip('inc/config');
	$erreurs = array();
	$tab_objet=codes_postaux_config_tab_fichier();
	$emplacement=sous_repertoire(_DIR_TMP,lire_config('cog/chemin_donnee'));
	// login trop court ou existant
	if ($objet = _request('objet')) {
		if (!isset($tab_objet[$objet])) {
			$erreurs['objet'] = _T('codes_postaux:fichier_incorrect');
			$erreurs['message_erreur'] .= _T('codes_postaux:fichier_incorrect');
		}
	}else{
	$erreurs['objet'] = _T('codes_postaux:choix_erronne');

	}

	return $erreurs;
}

// http://doc.spip.org/@inc_editer_mot_dist
function formulaires_telecharger_codes_postaux_traiter_dist(){
include_spip('codes_postaux_config');
include_spip('inc/codes_postaux_import');
include_spip('inc/config');
$tab_objet=codes_postaux_config_tab_fichier();
$objet =_request('objet');
$tab_fichier_telecharger=array();
$tab_fichier=codes_postaux_tab_fichier_telecharger($tab_objet[$objet]['url_fichier']);
foreach($tab_fichier as $fichier) {
	$nom_fichier_txt="";
if(isset($tab_objet[$objet]['nom_fichier'])){
	$nom_fichier_txt=$tab_objet[$objet]['nom_fichier'];
	}
	$nom_fichier=codes_postaux_telecharger_fichier_distant($fichier,$nom_fichier_txt);
	if($nom_fichier)
		$tab_fichier_telecharger[]=$nom_fichier;
}

$retour['editable']=true;
if(count($tab_fichier_telecharger)==count($tab_fichier)){
	$retour['message_ok'] = 'Le ou les fichier(s) '.$objet.' a bien été télécharger, vous pouvez procéder à son importation.';
} else {
	$retour['message_erreur'] = 'Problème dans le téléchargement du fichier';
}
return $retour;

}




function codes_postaux_telecharger_fichier_distant($source,$nom_fichier_txt="")
{
include_spip('inc/distant');
include_spip('inc/config');
$fichier=copie_locale($source);
$infos_fichier=pathinfo($source);
$emplacement=sous_repertoire(_DIR_TMP,lire_config('codes_postaux/chemin_donnee'));
chmod($emplacement,0777);
$nom_fichier=$emplacement.$infos_fichier['filename'].'.'.$infos_fichier['extension'];
$emplacement.$infos_fichier['filename'].'.txt';
$nom_fichier_txt=empty($nom_fichier_txt)?$nom_fichier_txt=$emplacement.$infos_fichier['filename'].'.txt':$emplacement.$nom_fichier_txt;


rename(_DIR_RACINE.$fichier,$nom_fichier);
$infos_fichier=pathinfo($nom_fichier);

// Si c'est un zip on l'extrait
if($infos_fichier['extension']=='zip')
{

	include_spip('inc/pclzip');
	include_spip('inc/joindre_document');
	$archive = new PclZip($nom_fichier);
	$archive->extract(_DIR_TMP);
	$contenu = joindre_decrire_contenu_zip($archive);

	if(isset($contenu[0]))	{
		foreach ($contenu[0] as $fichier) {
			if($fichier['filename']!="readme.txt"){
			rename(_DIR_TMP.$fichier['filename'],$nom_fichier_txt);
			}
			
		}
	}
	unlink($nom_fichier);
}

return $nom_fichier;

}


?>
