<?php


// http://doc.spip.org/@inc_editer_mot_dist
function formulaires_importer_cp_charger(){
include_spip('cp_config');

	return array();
}



function formulaires_importer_cp_verifier_dist(){
include_spip('cp_config');

	/*$erreurs = formulaires_editer_objet_verifier('spip_'._request('fichier'),'new',array('fichier'));
	*/
         $erreurs=array();
    $tab_fichier=cp_config_tab_fichier();
	$emplacement=_DIR_PLUGIN_CP.lire_config('cp/chemin_donnee');
	if (!file_exists($emplacement.$tab_fichier['code_postal']['nom_fichier'])) {
		echo($emplacement.$tab_fichier['code_postal']['nom_fichier']);
			$erreurs['fichier'] .= _T('cp:fichier_introuvable')." ".$emplacement.$tab_fichier[$fichier]['fichier'] ;
			$erreurs['message_erreur'] .= _T('cp:fichier_introuvable');
		}


	return $erreurs;
}

// http://doc.spip.org/@inc_editer_mot_dist
function formulaires_importer_cp_traiter_dist(){
include_spip('cp_config');
	 $options=array(
	 'truncate'=>_request("option_truncate"),
	 'relier_communes'=>_request("option_relier_communes"),
	 'filtre'=>_request("option_filtre")
	 );

	$message=cp_import($options);

	return array('message_ok'=>$message);
}







function cp_import($options)
{

	$contenu_fichier=array();
	$message="";

$tab_fichier=cp_config_tab_fichier();
$tab_colonne = cp_config_correspondance_colonne();
$tab_colonne=$tab_colonne['code_postal'];
$colonnes=$tab_colonne['colonnes'];

if(isset($tab_colonne['filtre'])){
	$filtre_config=$tab_colonne['filtre'];
	}
if(isset($tab_colonne['liaison'])){
	$liaison=$tab_colonne['liaison'];
	}



	$tab_filtres= array();
	$option_truncate	= false;
	if(isset($options['truncate']))
		$option_truncate= $options['truncate'];
	$option_relier_communes	= false;
	if(isset($options['relier_communes']))
		$option_relier_communes= $options['relier_communes'];
	$option_filtre	= '';
	if (isset($options['filtre']))
		$option_filtre	= $options['filtre'];

	$filtres=explode(';',$option_filtre);

	foreach($filtres as $filtre)
		{
		$tab_temp=explode('=',$filtre);
		$tab_filtres[$tab_temp[0]]=$tab_temp[1];
		}

	$emplacement=_DIR_PLUGIN_CP.lire_config('cp/chemin_donnee');
	$message=  'Importation du fichier '.$fichier."<br />";
	$fichier_modele=$emplacement.$tab_fichier['code_postal']['nom_fichier'];


	$table='spip_code_postals';
	if($option_truncate)
	{
	$message.=  'Purge de la table '.$table."<br />";
	spip_mysql_query('truncate table '.$table);
	if($option_relier_communes){
		sql_delete("spip_communes_liens",'objet=\'code_postal\'');
		}
	}


	$pointeur_fichier = fopen($fichier_modele,"r");
	if($pointeur_fichier<>0)
	{
	$nb_ligne=0;
	while (!feof($pointeur_fichier))
	{
		$ligne= fgets($pointeur_fichier, 4096);
		$tab=explode("\t",$ligne);
		if(count($tab)>1)
		{
			if(isset($filtre_config))
					{
					if(!preg_match('/^'.$filtre['valeur'].'$/',$tab[$filtre['cle']]))
						continue;
					}
			if(!empty($tab_filtres))
					{
					if (!cp_applique_filtre($tab,$tab_filtres))
						continue;
					}

			foreach($colonnes as $nom_colonne=>$num_colonne) {
				$champs[''.$nom_colonne]=$tab[$num_colonne];
			}

			if($option_truncate)
				{
				$id_code_postal=0;
				}
			else
				{
				$where=array();
				foreach($champs as $key=>$valeur)
					$where[]=$key.'='.sql_quote($valeur);
				$id_code_postal=sql_getfetsel('id_code_postal',$table,implode(' AND ',$where));
				}

				if(!$id_code_postal){
					$id_code_postal=sql_insertq($table,$champs);
					}

			if($option_relier_communes){
				$id_cog_commune=sql_getfetsel('id_cog_commune','spip_cog_communes','departement='.$tab[6].' and nom_majuscule = '.sql_quote(strtoupper($tab[2])));
				if($id_cog_commune){
					sql_insertq("spip_cog_communes_liens",array('id_cog_commune'=>$id_cog_commune,'objet'=>'code_postal','id_objet'=>$id_code_postal));
					}
				}
			$nb_ligne++;

		}
	}
   }
	$message.=$nb_ligne.' enregistrements ajoutés.';
	fclose($pointeur_fichier);
	return $message;

}



function cp_applique_filtre($tab_value,$tab_filtres)
{
	foreach($tab_filtres as $col=>$filtre)
	{
		if(isset($tab_value[$col]))
		{
		if(!preg_match('/^'.$filtre.'$/',$tab_value[$col]))
			{
			return false;
			}
		}
	}
return true;
}



?>
