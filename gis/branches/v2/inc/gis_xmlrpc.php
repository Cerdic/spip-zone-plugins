<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Récupère la liste des points géolocalisés
 * 
 * Arguments possibles :
 * -* login
 * -* pass
 * -* objet
 * -* id_objet
 * -* tri
 * -* limite
 */
function spip_liste_gis($args) {
	global $spip_xmlrpc_serveur;
	
	if(!$spip_xmlrpc_serveur)
		return false;
	
	$limite = $args['limite'] ? $args['limite'] : '20';
	
	$where = '';
	$order = array();
	if(intval($args['id_objet']) && $args['objet']){
		$where = 'lien.id_objet='.intval($args['id_objet']).' AND lien.objet='.sql_quote($args['objet']);
	}
	
	if($args['tri']){
		$order = array_map('trim',explode(',',$args['tri']));
	}

	$points_struct = array();

	if($points = sql_select('gis.id_gis','spip_gis as gis LEFT JOIN spip_gis_liens as lien ON gis.id_gis=lien.id_gis',$where,array('gis.id_gis'),$order,$limite)){
		while($point = sql_fetch($points)){
			$struct=array();
			$args['id_gis'] = $point['id_gis'];
			/**
			 * On utilise la fonction geodiv_lire_media pour éviter de dupliquer trop de code
			 */
			$struct = spip_lire_gis($args);
			$points_struct[] = $struct;
		}
	}
	return $points_struct;
}

/**
 * Récupère le contenu d'un point géolocalisé
 * 
 * Arguments possibles :
 * -* login
 * -* pass
 * -* id_gis (Obligatoire)
 */
function spip_lire_gis($args){
	global $spip_xmlrpc_serveur;
	
	if(!$spip_xmlrpc_serveur)
		return false;
	
	if(!intval($args['id_gis']) > 0){
		$erreur = _T('xmlrpc:erreur_identifiant',array('objet'=>'gis'));
		return new IXR_Error(-32601, attribut_html($erreur));
	}
	
	$args_gis = array('objet'=>'gis','id_objet'=>$args['id_gis']);
	$res = $spip_xmlrpc_serveur->read($args_gis);
	if(!$res)
		return $spip_xmlrpc_serveur->error;
	
	if(autoriser('modifier','gis',$args['id_gis'],$GLOBALS['visiteur_session']))
		$res['result'][0]['modifiable'] = 1;
	else
		$res['result'][0]['modifiable'] = 0;
	$logo = quete_logo('id_gis','on', $res['result'][0]['id_gis'], '', false);
	if(is_array($logo))
		$res['result'][0]['logo'] = url_absolue($logo[0]);
	
	$gis_struct = $res['result'][0];
	$gis_struct = array_map('texte_backend',$gis_struct);
	return $gis_struct;
}
?>