<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/abstract_sql');

function balise_ADXMENU_dist($p) {
    return calculer_balise_dynamique($p, ADXMENU, array('id_article', 'id_rubrique'));
}

function balise_ADXMENU_dyn($id_article=null,$id_rubrique=null,$_rub=false,$couper=30,$couper_car='.') {
	$fond = 'adxmenu';
	$rub = array();
	if (empty($couper)) $couper=30;
	if (empty($couper_car)) $couper_car='.';

	// Configuration
	$conf = function_exists('lire_config') ? lire_config('adxmenu') : false;
	if (!$conf || !isset($conf['liste_rub'])) 
		$conf = array('liste_rub' => tipafriend_transform_string(ADXMENU_RUB_DEFAUT));

	// Parametres
	$rub_demande = strlen(trim($_rub)) ? tipafriend_transform_string($_rub) : $conf['liste_rub'];
	$rub_a_exclure = array();

//echo '<br />arg : '.var_export($rub_demande,1);
	// cas de "secteurs!x:y" ou "tout!x:y"
	if (strpos($rub_demande, '!')) {
		$_ex = explode('!', $rub_demande);
		if (count($_ex>1)) {
			$rub_demande = $_ex[0];
			$rub_a_exclure = explode(':', tipafriend_transform_string($_ex[1]));
		}
	}

	// explode en separant par ":"
	if (!in_array($rub_demande, array('secteurs','tout'))) 
		$rub_demande = explode(':', $rub_demande);

//echo '<br />rub_demande : '.var_export($rub_demande,1);
//echo '<br />rub_a_exclure : '.var_export($rub_a_exclure,1);

	// Swith pour les ID rubriques
	switch($rub_demande){
		case 'secteurs' :
			$rub = tipafriend_get_secteurs( $rub_demande );
			break;
		case 'tout' :
			$rub = tipafriend_get_tout( $rub_demande );
			break;
		default :
			foreach($rub_demande as $k=>$id) 
				$rub[] = intval($id);
			break;
	}

	// exclusion si demande
	if (!empty($rub_a_exclure)) {
		foreach($rub_a_exclure as $_rubid)
			if (array_search($_rubid, $rub))
				unset($rub[array_search($_rubid, $rub)]);
	}

//echo '<br />rubs : '.var_export($rub,1);

	// Renvoi du calcul du squelette
	$contexte = array( 
		'lang' => $GLOBALS['spip_lang'], 
		'fond' => 'modeles/'.$fond,
		'dir_racine' => _DIR_RACINE,
		'adx_rub' => $rub,
		'couper' => $couper,
		'couper_car' => $couper_car,
		'id_rubrique' => $id_rubrique,
		'id_article' => $id_article
	); 
	echo recuperer_fond('modeles/'.$fond, $contexte);
}

function tipafriend_get_secteurs( $str ) 
{
	$rub = array();
	$req = sql_select("id_secteur", "spip_rubriques");
	if(sql_count($req) > 0)
		while($row=spip_fetch_array($req))
			$rub[] = $row['id_secteur'];
	return $rub;
}

function tipafriend_get_tout( $str ) 
{
	$rub = array();
	$req = sql_select("id_rubrique", "spip_rubriques");
	if(sql_count($req) > 0)
		while($row=spip_fetch_array($req))
			$rub[] = $row['id_rubrique'];
	return $rub;
}

function tipafriend_transform_string( $str ) 
{
	return str_replace(array(',', ';', '.', ' ', '/', ':', '\'', '"'), ':', trim($str));
}

?>