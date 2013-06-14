<?php

/* Plugin Comarquage -flux V2- pour SPIP 1.9
 * Copyright (C) 2006 Cedric Morin
 * Copyright (C) 2010 Vernalis Interactive
 *
 * Licence GPL
 *
 */

include_spip('inc/comarquage');

function comarquage_run($parametres_defaut){

	$url_base = preg_replace(',\?.*$,','',self());

	$parametres =& comarquage_parametres($parametres_defaut,$url_base);


	$ma_page =& comarquage_compile_page_xml($parametres,$url_base);

	if (!is_string($ma_page)) {
		if ($ma_page == -20)
			$ma_page = _T('comarquage:avis_serveur_indisponible');
		else
			$ma_page = _T('comarquage:avis_erreur');
	}

	return $ma_page;
}

// recuperer les parametres specifiques au comarquage et les autres
function & comarquage_parametres($defaut,&$urlbase) {
	$parametres = array();
	$parametres_attendus = array('xml' => ',^[a-z0-9_\-]*[.]xml$,i' , 'xsl' => ',^[a-z0-9_\-]*[.]xsl$,i', 'lettre' =>',^[a-z]$,i', 'motcle'=>'','categorie' => '');

	foreach ($parametres_attendus as $k=>$reg) {
		$p=_request($k);

		if (($p==NULL)&&isset($defaut[$k]))
			$p = $defaut[$k];
		if (strlen($reg) AND !preg_match($reg,$p))
			$p=NULL;
		if ($p==NULL)
			$p = isset($GLOBALS['meta']['comarquage_default_'.$k.'_file'])?$GLOBALS['meta']['comarquage_default_'.$k.'_file']:NULL;
		if ($p!==NULL){
			$parametres[$k] = $p;
			$urlbase = parametre_url($urlbase,$k,'');
		}
	}
	if (strpos($urlbase,'?')===FALSE)
		$urlbase.='?';
	else
		$urlbase.='&';

	if (isset($parametres['xml'])){
		$parametres['xml'] = basename($parametres['xml'],'.xml').'.xml';

		// Regarde si on un xml local dans le répertoire du plugin (pour utiliser Glossaire, Dossiercat, etc...)
		$xml_local = _DIR_PLUGIN_COMARQUAGE.'xml/'.$parametres['xml'];


		// Si le xml dans le dossier du plugin existe, on le prend
		// Sinon on regarde dans le répertoire de cache de la categorie
		if (file_exists($xml_local)) {
			$parametres['xml_full_path'] = _DIR_PLUGIN_COMARQUAGE.'xml/'.$parametres['xml'];
			spip_log("XML LOCAL trouvé : $xml_local","comarquage");
		}
		else {
			spip_log("XML LOCAL non trouvé : $xml_local","comarquage");
			$parametres['xml_full_path'] = sous_repertoire(_DIR_CACHE, _DIR_CACHE_COMARQUAGE_XML.'_'.$parametres['categorie']).$parametres['xml'];
		}

	}
	if (isset($parametres['xsl']))
		// Feuille XSL différentes pour la catégorie "entreprises"
		if($parametres['categorie'] == 'entreprises')
			$parametres['xsl'] = "spThemesEntreprises.xsl";

		$parametres['xsl'] = basename($parametres['xsl'],'.xsl').'.xsl';
	//	$parametres['xsl_full_path'] = _DIR_PLUGIN_COMARQUAGE.'xsl/'.$parametres['categorie'].'/'.$parametres['xsl'];

		$parametres['xsl_full_path'] = _DIR_PLUGIN_COMARQUAGE.'xsl/'.$parametres['xsl'];


	return $parametres;
}


function comarquage_post_propre($texte){
	if (strpos($texte,'<comarquage')===FALSE) return $texte;

	$pattern="<comarquage*([^>]*)>";
	if ( 	(preg_match_all("{" . $pattern . "}is", $texte, $matches,PREG_SET_ORDER))
			&& comarquage_processeur_disponible()) {

		foreach($matches as $occurence){
			$args = trim($occurence[1]);
			$args = trim($args);
			$args = explode("|",$args);
			$targs = array();

			foreach($args as $arg){
				//spip_log("arg de comarquage run :".$arg, "comarquage");
				if (preg_match("#(.+)=(.+)#is",$arg,$extract))
					//spip_log($extract,"comarquage");
					$targs[$extract[1]] = $extract[2];
			}

		  $out = comarquage_run($targs);
		  if ($GLOBALS['meta']['charset']!='utf-8'){
		  	include_spip('inc/charsets');
		  	$out = importer_charset($out,"utf-8");
		  }
			$texte = str_replace($occurence[0],$out,$texte);
		}
	}

  return $texte;
}
?>