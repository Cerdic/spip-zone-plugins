<?php


function formulaires_importer_cog_charger(){
include_spip('cog_config');
include_spip('inc/cog_import');
include_spip('inc/config');
$tab_objet=cog_config_tab_fichier();
$emplacement=_DIR_TMP.lire_config('cog/chemin_donnee');
foreach($tab_objet as &$objet)
	{
		$fichier_manquant=false;
		$tab_fichier=cog_tab_fichier_telecharger($objet['fichier']);
		foreach($tab_fichier as $fichier)
		{
			$infos_fichier=pathinfo($fichier);
			$nom_fichier=$emplacement.$infos_fichier['filename'].'.'.$infos_fichier['extension'];
			if($infos_fichier['extension']=='zip')
				$nom_fichier=$emplacement.$infos_fichier['filename'].'.txt';
			if(!file_exists($nom_fichier))
				$fichier_manquant=true;
		}
		$objet['fichier_manquant']=$fichier_manquant;
	}
return array('objet'=>'','tab_objet'=>$tab_objet);
}


function formulaires_importer_cog_verifier_dist(){
	include_spip('cog_config');
	include_spip('inc/config');
	$tab_objet=cog_config_tab_fichier();
	$emplacement=_DIR_TMP.lire_config('cog/chemin_donnee');

	if ($objet = _request('objet')) {
		if (!isset($tab_objet[$objet])) {
			$erreurs['fichier'] = _T('cog:fichier_incorrect');
			$erreurs['message_erreur'] .= _T('cog:fichier_incorrect');
		}else {
			$tab_objet=$tab_objet[$objet]['fichier'];
			if(is_array($tab_objet))
				{
				$tab_objet=$tab_objet;
				}
			else
				{
				$tab_objet=array($tab_objet);
				}
			foreach($tab_objet as $fichier)
			{
			$infos_fichier=pathinfo($fichier);
			$extension = $infos_fichier['extension'];
			if($extension =='zip')
				$extension = 'txt';
			$fichier=$infos_fichier['filename'].'.'.$extension;
			if (!file_exists($emplacement.$fichier)) {
				$erreurs['fichier'] .= _T('cog:fichier_introuvable')." ".$emplacement.$fichier ;
				$erreurs['message_erreur'] .= _T('cog:fichier_introuvable');
			}
			}
		}
	}

	return $erreurs;
}

// http://doc.spip.org/@inc_editer_mot_dist
function formulaires_importer_cog_traiter_dist(){

	 $options=array(
	 'truncate'=>_request("option_truncate"),
	 'replace'=>_request("option_ecraser"),
	 'filtre'=>_request("option_filtre")
	 );
	$objet=_request("objet");
	 if (function_exists($fonction='cog_import_'._request("fichier")))
		list($message,$erreurs)=$fonction($objet, $options);
	else
		list($message,$erreurs)=cog_import($objet, $options);

$retour['editable']=true;
if(count($erreurs)==0){
	$retour['message_ok'] = $message;
} else {
	$retour['message_erreur'] = implode('<br />',$erreurs);
}

return $retour;
}




function cog_import_epcis($objet,$options)
{

	$options['decalage']=0;
	$tab_correspondance= array(
	'code'		=>	0,
	'libelle'	=>	1,
	'nature'	=>	array('fichier'=>1,'col'=>0),
	);
	$tab_relation= array(
	'code_insee'	=>	array('fichier'=>1,'col'=>3),
	'objet'		=>	'cog_epci',
	);

	return cog_import($objet,$options,$tab_correspondance,$tab_relation);

}









function cog_applique_filtre($tab_value,$tab_filtres)
{
	foreach($tab_filtres as $col=>$filtre)
	{
		if(isset($tab_value[$col]))
		{
		if($tab_value[$col]!=$filtre)
			{
			return false;
			}
		}
	}
return true;
}


function cog_renvoyer_valeur(&$ligne,&$correspondance,&$fichier,&$contenu_fichier,$one=true)
{
	if(isset($correspondance['fichier']))
	{
		return  cog_ramener_valeur($ligne,$correspondance,$fichier,$contenu_fichier,$one);
	}
	elseif(isset($correspondance['col']))
	{
		return  $ligne[$correspondance['col']];
	}
	else
	{
		return  $ligne[$correspondance];
	}
}


function cog_ramener_valeur(&$ligne,&$correspondance,&$fichier,&$contenu_fichier,$one=true)
{
	include_spip('inc/config');
	$tab_result=array();
	$num_fichier=$correspondance['fichier'];
	$col_key1=$fichier['fichier'][$num_fichier][1];
	$col_key2=$fichier['fichier'][$num_fichier][2];
	if(!isset($contenu_fichier[$num_fichier]))
		{
			$emplacement=_DIR_TMP.lire_config('cog/chemin_donnee');
			$nom_fichier=$emplacement.$fichier['fichier'][$num_fichier][0];
			$pointeur_fichier = fopen($nom_fichier,"r");
			if($pointeur_fichier<>0)
			{
				$ligne_temp= fgets($pointeur_fichier, 4096);
				$indice=0;
				$anc_code='';
				while (!feof($pointeur_fichier))
				{
					$ligne_temp= fgets($pointeur_fichier, 4096);
					$ligne_temp=explode("\t",$ligne_temp);
					if($ligne_temp[$col_key2]==$anc_code){
						$indice++;}
					else {
						$indice=0;
						$anc_code=$ligne_temp[$col_key2];}
					if(count($ligne_temp)>=2)
						$contenu_fichier[$num_fichier][$col_key2][$ligne_temp[$col_key2]][$indice]=$ligne_temp;
				}
			}
		}
		//print_r($contenu_fichier);
		//exit();
		//echo("<br />toto".$one);
		//print_r($contenu_fichier[$num_fichier][$col_key2]);

	if(isset($contenu_fichier[$num_fichier][$col_key2][$ligne[$col_key1]][0]))
		{
			//echo("<br />".$one);
			if($one)
				{
				return $contenu_fichier[$num_fichier][$col_key2][$ligne[$col_key1]][0][$correspondance['col']];
				}
			else
				{
					//echo("<br />eoeoe");

					$tab_result=array();
					foreach($contenu_fichier[$num_fichier][$col_key2][$ligne[$col_key1]] as $ligne_temp)
						{
							//echo("<br />".$ligne_temp[$correspondance['col']]);
							$tab_result[]=$ligne_temp[$correspondance['col']];
						}
					return $tab_result;
				}
		}
	return '';
}







function cog_import($objet,$options,$tab_correspondance=array(),$tab_relation=array())
{
include_spip('cog_config');
include_spip('inc/config');
$erreurs=array();
	$contenu_fichier=array();
	$message="";
	$tab_filtres= array();
	$option_truncate	= false;
	if(isset($options['truncate']))
		$option_truncate= $options['truncate'];
	$option_replace	= false;
	if(isset($options['replace']))
		$option_replace = $options['replace'];
	$option_filtre	= '';
	if (isset($options['filtre']))
		$option_filtre	= $options['filtre'];
	$option_decalage = 1;
	if (isset($options['decalage']))
		$option_decalage	= $options['decalage'];

	//print_r($filtres);
	$filtres=explode(';',$option_filtre);
	//print_r($filtres);
	foreach($filtres as $filtre)
		{
		$tab_temp=explode('=',$filtre);
		$tab_filtres[$tab_temp[0]]=$tab_temp[1];
		}
	$tab_objet=cog_config_tab_fichier();
	$emplacement=_DIR_TMP.lire_config('cog/chemin_donnee');
	$message=  'Importation du fichier '.$objet."<br />";
//	$message.= 'Emplacement du fichier : '.$emplacement."<br />";
	if(is_array($tab_objet[$objet]['fichier']))
	{
		$fichier_modele=$tab_objet[$objet]['fichier'][0];
	}
	else
	{
		$fichier_modele=$tab_objet[$objet]['fichier'];
	}

	$infos_fichier=pathinfo($fichier_modele);
	$extension = $infos_fichier['extension'];
	if($extension =='zip')
		$extension = 'txt';
	$fichier_modele=$emplacement.$infos_fichier['filename'].'.'.$extension;

	$table='spip_cog_'.$objet;
	$tab_description=description_table($table);
	if($option_truncate==1)
	{
	$message.=  'Purge de la table '.$table."<br />";
	spip_mysql_query('truncate table '.$table);
	sql_delete($table,array("1"=>"1"));
	if(!empty($tab_relation))
		{
		sql_delete("spip_cog_communes_liens",'objet='.sql_quote($tab_relation['objet']));
		}
	}


	$pointeur_fichier = fopen($fichier_modele,"r");
	if($pointeur_fichier<>0)
	{
	$ligne= fgets($pointeur_fichier, 4096);
	$nb_ligne=0;
	//print_r($tab_correspondance);
	while (!feof($pointeur_fichier))
	{
		$ligne= fgets($pointeur_fichier, 4096);
		$tab=explode("\t",$ligne);
		if(count($tab)>1)
		{
			$tab_value=array();
			$i=0;
			reset($tab_description['field']);
			while(list ($key, $val) = each ($tab_description['field']))
				{
					if($option_decalage>$i)
					{
						$i++;
						continue;
					}

					if(!empty($tab_correspondance))
					{
						if(isset($tab_correspondance[$key]))
						{

							if(!is_array($tab_correspondance[$key]))
							{
								$tab_value[$key] = $tab[$tab_correspondance[$key]];
							}
							else
							{
								if(isset($tab_correspondance[$key]['col']))
								{
									$tab_value[$key]=cog_renvoyer_valeur($tab,$tab_correspondance[$key],$tab_objet[$objet],$contenu_fichier);
								}
								else
								{
									$tab_value[$key]="" ;
									reset($tab_correspondance[$key]);
									while(list ($indice1, $valeur1) = each ($tab_correspondance[$key]))
										{
											$tab_value[$key] .=sql_quote(cog_renvoyer_valeur($tab,$valeur1,$tab_objet[$objet],$contenu_fichier));
										}
								}
							}
						}
					}
						else
						{
							//print_r($tab);
							$tab_value[$key] = $tab[$i-$option_decalage];
						}
						$i++;
				}

				$filtre_relation=false;
				if(!empty($tab_relation))
				{
					$tab_depcom=cog_renvoyer_valeur($tab,$tab_relation['code_insee'],$tab_objet[$objet],$contenu_fichier,false);
					//print_r($tab_depcom);
					//print_r("rara");
					//exit();
					for($ii=0;$ii<count($tab_depcom);$ii++)
					{
						$tab_depcom[$ii]=array('departement'=>substr($tab_depcom[$ii],0,2),'code'=>substr($tab_depcom[$ii],2));

						if(!cog_applique_filtre($tab_depcom[$ii],$tab_filtres))
							$filtre_relation=true;

					}
				//	print_r($tab_depcom);
				}

				if(!cog_applique_filtre($tab_value,$tab_filtres) || $filtre_relation)
					continue;


				if($option_replace && $existe_deja)
					{
						sql_delete($table, $primarys);
						$id=sql_insertq($table, $tab_value);
					}
				elseif(!$existe_deja)
					{
						//print_r($tab_value);
						$id=sql_insertq($table, $tab_value);
					}

				if(!empty($tab_relation))
				{

					foreach($tab_depcom as $depcom)
					{
						//exit();

							if($id_cog_commune=sql_getfetsel('id_cog_commune','spip_cog_communes','departement='.sql_quote($depcom['departement']).' and code= '.sql_quote($depcom['code'])))
								{
								//	print_r(sql_get_select('id_cog_commune','spip_cog_communes','departement='.sql_quote($depcom['departement']).' and code= '.sql_quote($depcom['code']))."<br />");

								sql_insertq("spip_cog_communes_liens",array('id_cog_commune'=>$id_cog_commune,'id_objet'=>$id,'objet'=> $tab_relation['objet']));
								}
							else {
								$erreurs[]="Erreur grave Commune introuvable : ".$com['ccocom'];
								}

					}

				}

				$nb_ligne++;
			}
		}
	}
	$message.=$nb_ligne.' enregistrements ajoutés.';
	fclose($pointeur_fichier);
	return array($message,$erreurs);

}



function importer_fichier_distant($source)
{
include_spip('inc/distant');
$fichier=copie_locale($source);



	include_spip('inc/pclzip');
	$archive = new PclZip($fichier);
	$archive->extract(
		  PCLZIP_OPT_PATH, _tmp_dir,
		  PCLZIP_CB_PRE_EXTRACT, 'callback_deballe_fichier'
		  );
	$contenu = verifier_compactes($archive);
	$titrer = _request('titrer') == 'on';
	foreach ($contenu as $fichier => $size) {
	$f = basename($fichier);
	$x = $ajouter_documents(_tmp_dir. $f, $f,
	$type, $id, $mode, $id_document, $actifs, $titrer);
	}
	effacer_repertoire_temporaire(_tmp_dir);
	return $x;
}



?>
