<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */


/**
 * Recuperer les champs date_xx et heure_xx, verifier leur coherence et les reformater
 *
 * @param string $suffixe
 * @param bool $horaire
 * @param array $erreurs
 * @return int
 */
function agenda_verifier_corriger_date_saisie($suffixe,$horaire,&$erreurs){
	include_spip('inc/filtres');
	$date = _request("date_$suffixe").($horaire?' '.trim(_request("heure_$suffixe")).':00':'');
	$date = recup_date($date);
	$ret = null;
	if (!$ret=mktime(0,0,0,$date[1],$date[2],$date[0]))
		$erreurs["date_$suffixe"] = _L('date incorrecte');
	elseif (!$ret=mktime($date[3],$date[4],$date[5],$date[1],$date[2],$date[0]))
		$erreurs["date_$suffixe"] = _L('heure incorrecte');
	if ($ret){
		if (trim(_request("date_$suffixe")!==($d=date('d/m/Y',$ret)))){
			$erreurs["date_$suffixe"] = _L('saisie corrigee');
			set_request("date_$suffixe",$d);
		}
		if ($horaire AND trim(_request("heure_$suffixe")!==($h=date('H:i',$ret)))){
			$erreurs["heure_$suffixe"] = _L('saisie corrigee');
			set_request("heure_$suffixe",$h);
		}
	}
	return $ret;
}

?>