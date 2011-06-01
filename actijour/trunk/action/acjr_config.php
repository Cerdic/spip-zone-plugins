<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.55 - 05/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifie KOAK2.0 strict, mais si !
+--------------------------------------------+
| ecrire configuration
+--------------------------------------------+
*/

function action_acjr_config() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	include_spip('inc/headers');
	
	if (!autoriser('configurer', 'plugins')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	$metas=array();
	
	$metas['version']=_request('version_plug');
	
	$nbl_art=intval(_request('nbl_art'));
	$nbl_aut=intval(_request('nbl_aut'));
	$nbl_mensuel=intval(_request('nbl_mensuel'));
	$nbl_topsem=intval(_request('nbl_topsem'));
	$nbl_topmois=intval(_request('nbl_topmois'));
	$nbl_topgen=intval(_request('nbl_topgen'));
	
	$default=false;	
	$ordon_pg_m=_request('ordon_pg_m');
	# piti controle
	$vals=explode(',',$ordon_pg_m);
	foreach($vals as $v) { 
		if($v>4 OR $v<1) $default=true;
	}
	if(strlen($ordon_pg_m)!=7) { $default=true; }
	if($default) {
		$ordon_pg_m='1,2,3,4';
	}
	
	$metas['nbl_art']=$nbl_art;
	$metas['nbl_aut']=$nbl_aut;
	$metas['nbl_mensuel']=$nbl_mensuel;
	$metas['nbl_topsem']=$nbl_topsem;
	$metas['nbl_topmois']=$nbl_topmois;
	$metas['nbl_topgen']=$nbl_topgen;
	$metas['admin-'._request('id_admin')]['ordon_pg_m']=$ordon_pg_m;

	$chaine = serialize($metas);
	ecrire_meta('actijour',$chaine);
	ecrire_metas();
	
	$redirect = urldecode(_request('redirect'));
}
?>
