<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

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
	if(!$conf || !isset($conf['liste_rub'])) 
		$conf = array('liste_rub' => str_replace(array(',', ';', '.', ' ', '/', ':', '\'', '"'), ':', trim(ADXMENU_RUB_DEFAUT)));

	// Parametres
	$rub_demande = strlen(trim($_rub)) ? str_replace(array(',', ';', '.', ' ', '/', ':', '\'', '"'), ':', trim($_rub)) : $conf['liste_rub'];
	if(!in_array($rub_demande,array('secteurs','tout'))) 
		$rub_demande = explode(':', $rub_demande);

	// Swith pour les ID rubriques
	switch($rub_demande){
		case 'secteurs' :
			$req = sql_select("id_secteur", "spip_rubriques");
			if(sql_count($req) > 0)
				while($row=spip_fetch_array($req))
					$rub[] = $row['id_secteur'];
			break;
		case 'tout' :
			$req = sql_select("id_rubrique", "spip_rubriques");
			if(sql_count($req) > 0)
				while($row=spip_fetch_array($req))
					$rub[] = $row['id_rubrique'];
			break;
		default :
			foreach($rub_demande as $k=>$id) 
				$rub[] = intval($id);
			break;
	}

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
?>