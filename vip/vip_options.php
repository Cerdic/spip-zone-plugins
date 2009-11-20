<?php
include_spip('base/vip');
function autoriser($faire, $type='', $id=0, $qui = NULL, $opt = NULL) {
	static $restreint = array();
	static $vip = array();
	
	// Qui ? auteur_session ?
	if ($qui === NULL)
		$qui = $GLOBALS['auteur_session']; // "" si pas connecte
	elseif (is_int($qui)) {
		$s = spip_query("SELECT * FROM spip_auteurs WHERE id_auteur=".$qui);
		$qui = spip_fetch_array($s);
	}
		
	// Admins restreints, les verifier ici (pas generique mais...)
	// Par convention $restreint est un array des rubriques autorisees
	// (y compris leurs sous-rubriques), ou 0 si admin complet
	if (is_array($qui)
	AND $qui['statut'] == '0minirezo'
	AND !isset($qui['restreint'])) {
		if (!isset($restreint[$qui['id_auteur']])) {
			include_spip('inc/auth'); # pour auth_rubrique
			$restreint[$qui['id_auteur']] = auth_rubrique($qui['id_auteur'], $qui['statut']);
		}
		$qui['restreint'] = $restreint[$qui['id_auteur']];
	}
	
	//spip_log("vip:($faire,$type,$id,$qui[nom])".serialize($opt));
	if (!is_array($vip[$qui['id_auteur']])) { 
		$xvip=array();
		$st="'".$qui['id_auteur']."','*'";
		switch ($qui['statut']){
			case '0minirezo' : $st.=",'0minirezo','1comite','6forum'";break;
			case '1comite' : $st.=",'1comite','6forum'";break;
			case '6forum' : $st.=",'6forum'";break;
		}
		$rvip = spip_query("SELECT * FROM spip_vips WHERE qui IN (".$st.")");
		while ($tvip=spip_fetch_array($rvip)){
			if (vip_test($tvip, $faire, $type, $id, $opt))
				return true;
			$xvip[$tvip['id_vip']]=$tvip;
		}
		$vip[$qui['id_auteur']]=$xvip;
	} else {
		foreach ($vip[$qui['id_auteur']] as $tvip)
			if (vip_test($tvip, $faire, $type, $id, $opt))
				return true;
	}
//	spip_log("vip ".$qui['id_auteur'].":".serialize($vip[$qui['id_auteur']]));
	
	
	
	// Chercher une fonction d'autorisation explicite
	if (
	// 1. Sous la forme "autoriser_type_faire"
		(
		$type
		AND $f = 'autoriser_'.$type.'_'.$faire
		AND (function_exists($f) OR function_exists($f.='_dist'))
		)

	// 2. Sous la forme "autoriser_type"
	// ne pas tester si $type est vide
	OR (
		$type
		AND $f = 'autoriser_'.$type
		AND (function_exists($f) OR function_exists($f.='_dist'))
	)

	// 3. Sous la forme "autoriser_faire"
	OR (
		$f = 'autoriser_'.$faire
		AND (function_exists($f) OR function_exists($f.='_dist'))
	)

	// 4. Sinon autorisation generique
	OR (
		$f = 'autoriser_defaut'
		AND (function_exists($f) OR function_exists($f.='_dist'))
	)

	)
		$a = $f($faire,$type,intval($id),$qui,$opt);

	if (_DEBUG_AUTORISER) spip_log("$f($faire,$type,$id,$qui[nom]): ".($a?'OK':'niet'));
	//spip_log("vip:$f($faire,$type,$id,$qui[nom]): ".($a?'OK':'niet'));
	return $a;
}
function vip_test($tvip, $faire, $type, $id, $opt){
	$r = (($tvip['faire']==$faire) || ($tvip['faire']=='*')) 
			&& (($tvip['sur']==$type) || ($tvip['sur']=='*')) 
			&& (($tvip['quoi']==$id) || ($tvip['quoi']=='*'));
	if(!is_array($opt)) $opt=unserialize($opt);
	if ($r && $tvip['options']) {
		if (!is_array($opt)){
			spip_log('vip_test : option precisee mais opt n\'est pas un tableau');
			return false;
		}
		//spip_log('vip_test avec options:'.$tvip['options'].'/'.serialize($opt));
		$options=explode(',',$tvip['options']);
		foreach($options as $option){
			$t=explode('=',$option);
			if (count($t)==2) {
				$r=$r && in_array($opt[$t[0]],explode('|',$t[1]));
			} else spip_log('vip_test erreur format option :'.$option);
		}
	}
	
	return $r;
}

?>