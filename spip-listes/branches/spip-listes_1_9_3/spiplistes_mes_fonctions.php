<?php
/**
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$
 
if(!defined('_ECRIRE_INC_VERSION')) return;


// Boucles SPIP-listes
global $tables_principales,$exceptions_des_tables,$table_date;


//
// <BOUCLE(LISTES)>
//
function boucle_LISTES($id_boucle, &$boucles) {
	global $table_des_tables;
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$type = $boucle->type_requete;
	$id_table = $table_des_tables[$type];
	if (!$id_table)
	//      table hors SPIP
		$boucle->from[$type] =  $type;
	else {
	// les tables declarees par spip ont un prefixe et un surnom
		$boucle->from[$id_table] =  'spip_' . $type ;
	}
	
	return (calculer_boucle($id_boucle, $boucles));
}

//
// <BOUCLE(AUTEURS_LISTES)>
//
if(function_exists('spiplistes_spip_est_inferieur_193') AND spiplistes_spip_est_inferieur_193()) {
	function boucle_AUTEURS_LISTES($id_boucle, &$boucles) {
		global $table_des_tables;
		$boucle = &$boucles[$id_boucle];
		$type = $boucle->type_requete; 
		$id_table = $table_des_tables[$type];
		if (!$id_table)
		//      table hors SPIP
			$boucle->from[$type] =  $type;
		else {
		// les tables declarees par spip ont un prefixe et un surnom
			$boucle->from[$id_table] =  'spip_' . $type ;
		}
		return (calculer_boucle($id_boucle, $boucles));
	}
}

//
// <BOUCLE(COURRIERS)>
//
function boucle_COURRIERS ($id_boucle, &$boucles) {
	/*
	$boucle = &$boucles[$id_boucle];
	if(spiplistes_spip_est_inferieur_193()) {
		$id_table = $boucle->id_table;
		$boucle->from[] =  "spip_courriers AS $id_table";
	}
	$boucle->where[] = array("'='","'type'","'\"nl\"'"); 
	return (calculer_boucle($id_boucle, $boucles));
	/**/
	global $table_des_tables;
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$type = $boucle->type_requete;
	$id_table = $table_des_tables[$type];
	if (!$id_table)
	//      table hors SPIP
		$boucle->from[$type] =  $type;
	else {
	// les tables declarees par spip ont un prefixe et un surnom
		$boucle->from[$id_table] =  'spip_' . $type ;
	}
	
	return (calculer_boucle($id_boucle, $boucles));
}

// Filtres SPIP-listes

/**
 * @deprecated
 */
function supprimer_destinataires ($texte) {
	return eregi_replace("__bLg__[0-9@\.A-Z_-]+__bLg__","",$texte);
}


function date_depuis($date) {
	    
	    if (!$date) return;
 	    $decal = date("U") - date("U", strtotime($date));
 	    
	    if ($decal < 0) {
 	        $il_y_a = "date_dans";
 	        $decal = -1 * $decal;
	    } else {
 	        $il_y_a = "spiplistes:date_depuis";
	    }
	    
	    if ($decal < 3600) {
 	        $minutes = ceil($decal / 60);
	        $retour = _T($il_y_a, array("delai"=>"$minutes "._T("date_minutes")));
	    }
	    else if ($decal < (3600 * 24) ) {
	        $heures = ceil ($decal / 3600);
 	        $retour = _T($il_y_a, array("delai"=>"$heures "._T("date_heures")));
 	    }
    else if ($decal < (3600 * 24 * 7)) {
 	        $jours = ceil ($decal / (3600 * 24));
 	        $retour = _T($il_y_a, array("delai"=>"$jours "._T("date_jours")));
	    }
	    else if ($decal < (3600 * 24 * 7 * 4)) {
	        $semaines = ceil ($decal / (3600 * 24 * 7));
 	        $retour = _T($il_y_a, array("delai"=>"$semaines "._T("date_semaines")));
	    }
	    else if ($decal < (3600 * 24 * 30 * 6)) {
 	        $mois = ceil ($decal / (3600 * 24 * 30));
 	        $retour = _T($il_y_a, array("delai"=>"$mois "._T("date_mois")));
 	    }
	    else {
 	        $retour = _T($il_y_a, array("delai"=>" ")).affdate_court($date);
 	    }
 	
 	
 	
 	    return $retour;
}

/* CP-20090109
 * Deux filtres SPIP2 bien sympathiques pour le formulaire.
 * */
if (function_exists('spiplistes_spip_est_inferieur_193') AND spiplistes_spip_est_inferieur_193()) {
	if(!function_exists('oui')) {
		function oui($c) { return($c ? ' ' : ''); }
	}
	if(!function_exists('non')) {
		function non($c) { return($c ? '' : ' '); }
	}
}

/**
 * Un filtre pour transformer les URLs relatives
 * à l'espace privé en URLs pour espace public.
 * A appliquer au conteneur, dans le patron,
 * du style : [(#TEXTE|liens_publics)]
 * @version CP-20110629
 * @example [(#TEXTE|liens_publics)]
 * @see https://www.spip.net/fr_article3377.html
 * @param string $texte
 * @return string
 */
function liens_publics ($texte)
{
	$url_site = $GLOBALS['meta']['adresse_site'];
	
	$replace = array(
		'articles' => 'article',
		'naviguer' => 'rubrique',
		'breves' => 'breve',
		'mots_edit' => 'mot',
		'sites_tous' => 'site',
	);
	
	foreach ($replace as $key => $value)
	{
		if (preg_match_all(',(<a[[:space:]]+[^<>]*href=["\']?' . $url_site . ')'
						   . '/ecrire/\?exec=(' . $key . ')'
						   . '([^<>]*>),imsS', 
						$texte,
						$liens,
						PREG_SET_ORDER))
		{
			foreach ($liens as $lien)
			{
				$to = $lien[1] . '/?page=' . $value . $lien[3];
				$texte = str_ireplace($lien[0], $to, $texte);
			}
		}
	}
	return ($texte);
}