<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_generer_langue_zotero_charger_dist(){
	$valeurs = array(
		'source'=>'',
		'code' => ''
	);
	return $valeurs;
}

function formulaires_generer_langue_zotero_traiter_dist(){
	$source = trim(_request('source'));
	$source = explode("\n",$source);
	$code = '';
	foreach ($source as $ligne) {
		if(strpos($ligne,'=')) {
			$ligne = explode ('=',$ligne);
			$ligne[0] = trim($ligne[0]);
			$ligne[1] = trim($ligne[1]);
			$code .= "'".strtolower(str_replace('.','_',$ligne[0]))."' => '".str_replace("'","\\'",$ligne[1])."',\n";
		}
	}
	$code = trim($code);
	$code = substr($code,0,-1);
	set_request('code',$code);
}

?>