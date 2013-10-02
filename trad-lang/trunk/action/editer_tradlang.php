<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_tradlang_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// si id_tradlang n'est pas un nombre, c'est une creation 
	// mais on verifie qu'on a toutes les donnees qu'il faut.
	if (!$id_tradlang = intval($arg))
		return false;

	// Enregistre l'envoi dans la BD
	$err = tradlang_set($arg);
 
	return array($arg,'');
}

function tradlang_set($id_tradlang,$set=null){
	$err = '';

	include_spip('inc/modifier');
	include_spip('inc/filtres');
	
	$c = collecter_requests(
		// white list
		objet_info('tradlang','champs_editables'),
		// black list
		array(),
		// donnees eventuellement fournies
		$set
	);
	
	/**
	 * On vérifie s'il y a au moins un champ modifié pour ajouter l'id_auteur dans les traducteurs
	 */
	$infos_tradlang = sql_fetsel('*','spip_tradlangs','id_tradlang='.intval($id_tradlang));

	$modifie = false;
	foreach($c as $champ => $valeur){
		if($valeur != $infos_tradlang[$champ]){
			$modifie = true;
			break;
		}
	}
	
	if($modifie && ($GLOBALS['visiteur_session']['id_auteur'] > 0)){
		$traducteurs = array();
		if($infos_tradlang['traducteur'])
			$traducteurs = array_map('trim',explode(',',$infos_tradlang['traducteur']));
		if(!in_array($GLOBALS['visiteur_session']['id_auteur'],$traducteurs)){
			$traducteurs[] = $GLOBALS['visiteur_session']['id_auteur'];
			$c['traducteur'] = implode(', ',$traducteurs);
		}
	}
	
	if(_request('str'))
		set_request('md5',md5(_request('str')));
	$invalideur = "id='id_tradlang/$id_tradlang'";
	if ($err = objet_modifier_champs('tradlang', $id_tradlang,
		array(
			'nonvide' => array(),
			'invalideur' => $invalideur,
			'indexation' => true,
		),
		$c)){
		return $err;
	}
	
	if(($statut = (in_array(_request('statut'),array('NEW','MODIF','OK','RELIRE'))) ? _request('statut') : $c['statut']) && ($statut != $infos_tradlang['statut'])){
		sql_updateq('spip_tradlangs',array('statut' => $statut),'id_tradlang='.intval($id_tradlang));
		$infos_maj = array();
		$bilan = sql_fetsel('chaines_ok,chaines_relire,chaines_modif,chaines_new','spip_tradlangs_bilans','module='.sql_quote($infos_tradlang['module']).' AND lang='.sql_quote($infos_tradlang['lang']));
		if($statut == 'OK')
			$infos_maj['chaines_ok'] = ($bilan['chaines_ok']+1);
		else if($statut == 'RELIRE')
			$infos_maj['chaines_relire'] = ($bilan['chaines_relire']+1);
		else if($statut == 'MODIF')
			$infos_maj['chaines_modif'] = ($bilan['chaines_modif']+1);
		else if($statut == 'NEW')
			$infos_maj['chaines_new'] = ($bilan['chaines_new']+1);
		
		if($infos_tradlang['statut'] == 'OK')
			$infos_maj['chaines_ok'] = ($bilan['chaines_ok']-1);
		else if($infos_tradlang['statut'] == 'RELIRE')
			$infos_maj['chaines_relire'] = ($bilan['chaines_relire']-1);
		else if($infos_tradlang['statut'] == 'MODIF')
			$infos_maj['chaines_modif'] = ($bilan['chaines_modif']-1);
		else if($infos_tradlang['statut'] == 'NEW')
			$infos_maj['chaines_new'] = ($bilan['chaines_new']-1);
		
		sql_updateq('spip_tradlangs_bilans',$infos_maj,'module='.sql_quote($infos_tradlang['module']).' AND lang='.sql_quote($infos_tradlang['lang']));
	}
	
	//$c = collecter_requests(array('statut'),array(),$set);
	//$err .= instituer_tradlang($id_tradlang, $c);

	return $err;
}

// $c est un array
//
// statut et rubrique sont lies, car un admin restreint peut deplacer
// un article publie vers une rubrique qu'il n'administre pas
// http://doc.spip.org/@instituer_article
function instituer_tradlang($id_tradlang, $c) {

	include_spip('inc/autoriser');
	include_spip('inc/rubriques');
	include_spip('inc/modifier');

	$statut = sql_getfetsel("statut", "spip_tradlangs", "id_tradlang=".intval($id_tradlang));
	$statut_ancien = $statut = $statut;
	$champs = array();

	$s = isset($c['statut'])?$c['statut']:$statut;

	// cf autorisations dans inc/instituer_article
	if ($s != $statut) {
		if (autoriser('modifier', 'tradlang', $tradlang))
			$statut = $champs['statut'] = $s;
		else
			spip_log("editer_tradlang $id_tradlang refus " . join(' ', $c));
	}
	// Envoyer aux plugins
	$champs = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => 'spip_tradlangs',
				'objet' => 'tradlang',
				'id_objet' => $id_tradlang,
				'action'=>'instituer',
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);

	if (!count($champs)) return;
	// Envoyer les modifs.
	sql_updateq('spip_tradlangs',$champs,"id_tradlang=".intval($id_tradlang));

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='tradlang/$id_tradlang'");

	// Pipeline
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_tradlangs',
				'objet' => 'tradlang',
				'id_objet' => $id_tradlang,
				'action'=>'instituer',
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);

	// Notifications
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('instituertradlang', $id_tradlang,
			array('statut' => $statut, 'statut_ancien' => $statut_ancien)
		);
	}

	return ''; // pas d'erreur
}
?>