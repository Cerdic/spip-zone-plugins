<?php




// http://doc.spip.org/@inc_editer_mot_dist
function formulaires_completer_commune_gps_charger(){
include_spip('coggps_config');
$fichier=false;
$dir=_DIR_PLUGIN_COG.lire_config('cog/chemin_donnee');
$fichier=file_exists($dir.'geonames_FR.txt');
	return array('fichier'=>$fichier);
}


function formulaires_completer_commune_gps_verifier_dist(){
include_spip('coggps_config');
	$erreurs=array();

	return $erreurs;
}

// http://doc.spip.org/@inc_editer_mot_dist
function formulaires_completer_commune_gps_traiter_dist(){
include_spip('coggps_config');


	$message=coggps_complete_commune_gps($fichier);


$retour['editable']=true;
if(count($erreurs)==0){
	$retour['message_ok'] = $message;
} else {
	$retour['message_erreur'] = 'Problème dans l\'importation du fichier';
}
return $retour;
}




function coggps_complete_commune_gps($fichier)
{
include_spip('coggps_config');
$tab_colonne =coggps_config_correspondance_colonne();
$tab_colonne=$tab_colonne['FR'];
$colonnes=$tab_colonne['colonnes'];
if(isset($tab_colonne['filtre'])){
	$filtre=$tab_colonne['filtre'];
	}
if(isset($tab_colonne['liaison'])){
	$liaison=$tab_colonne['liaison'];
	}

$tab_departement=sql_allfetsel('distinct departement','spip_cog_communes');
foreach($tab_departement as &$departement)
	{$departement=$departement['departement'];}


$tab_fichier = array();
    $nb = 0;
	$dir=_DIR_PLUGIN_COG.lire_config('cog/chemin_donnee');
	$fichier=$dir.'geonames_FR.txt';
	$pointeur_fichier = @fopen($fichier, "r");
	$commune_temp='';
	$temp_code="";
	if ($pointeur_fichier <> 0) {

		while (!feof($pointeur_fichier)) {
			$ligne = fgets($pointeur_fichier, 4096);

			$tab = explode("\t", $ligne);
				if(isset($filtre))
					{
					if(!preg_match('/^'.$filtre['valeur'].'$/',$tab[$filtre['cle']]))
						continue;
					}
				if(in_array(substr($tab[$liaison],0,2),$tab_departement))
				{
				$id_cog_commune = sql_getfetsel('id_cog_commune','spip_cog_communes','departement='.sql_quote(substr($tab[$liaison],0,2)).' and code ='.sql_quote(substr($tab[$liaison],2)));
				if ($id_cog_commune) {
					$champs =array();
					foreach($colonnes as $nom_colonne=>$num_colonne)
						{
						$champs[''.$nom_colonne]=$tab[$num_colonne];
						}
					$where='id_cog_commune='.$id_cog_commune;
					sql_updateq('spip_cog_communes', $champs,$where);
					$nb++;
					}
				}
			}
		}
return $nb.' communes viennent d\'être compl&eacute;t&eacute;es';
}







?>
