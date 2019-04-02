<?php
/**
 * Plugin Canevas pour Spip 2.0
 * Licence GPL
 * 
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_legende_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// si id_legende n'est pas un nombre, c'est une creation 
	if (!$id_legende = intval($arg)) {
		$id_document = _request('id_document');
		if (!$id_legende = legendes_action_insert_legende($id_document))
			return array(false,_L('echec'));
	}
	
	$err = action_legende_set($id_legende);
	return array($id_legende,$err);
}

function action_legende_set($id_legende){
	$err = '';

	$c = array();
	$c['posy'] = _request('top');
	$c['posx'] = _request('left');
	$c['width'] = _request('width');
	$c['height'] = _request('height');
	$c['texte'] = _request('texte');
	$c['id_document'] = _request('id_document');

	include_spip('inc/modifier');

	$err .= legendes_action_revision_legende($id_legende, $c);

	return $err;
}

// creer une nouvelle legende
function legendes_action_insert_legende($id_document){
	include_spip('inc/autoriser');
	if (!autoriser('creerdans','legende',$id_document))
		return false;
	
	$champs = array(
		'id_document' => $id_document,
		'id_auteur' => $GLOBALS['visiteur_session']['id_auteur'],
		'date' => date('Y-m-d H:i:s'));

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
			'table' => 'spip_legendes',
			),
			'data' => $champs
		)
	);

	$id_legende = sql_insertq("spip_legendes", $champs);

	if (!$id_legende){
		spip_log("legendes action insert legende : impossible d'ajouter une legende");
		return false;
	} 
	return $id_legende;	
}

// enregistrer une revision de legende
function legendes_action_revision_legende ($id_legende, $c=false) {
	
	include_spip('inc/autoriser');

	if (!autoriser('modifier', 'legende', $id_legende)){
		spip_log("editer_legende $id_legende refus " . join(' ', $c));
		return false;
	}
	modifier_contenu('legende', $id_legende, array(
		'invalideur' => "id='id_legende/$id_legende'"
	), $c);
	
	return ''; // pas d'erreur
}

// supprimer une legende
function legendes_action_supprime_legende($id_legende){
	include_spip('inc/autoriser');
	if (!autoriser('supprimer','legende',$id_legende))
		return false;
		
	if (intval($id_legende)){
		sql_delete("spip_legendes", "id_legende=".intval($id_legende));
	}
	$id_legende = 0;
	return $id_legende;
}

// tourner une legende
function legendes_action_tourner_legende($id_legende,$angle){
	
	// recuperer les infos dela note à tourner
	$legende = sql_fetsel('*','spip_legendes','id_legende='.intval($id_legende));
	$c = array();
	foreach($legende as $key=>$val)
		$c[$key] = $val;
	$n = array();
	
	// recuperer les infos de l'image associee
	$image = sql_fetsel('*','spip_documents','id_document='.intval($c['id_document']));
	$largeur = $image['largeur'];
	$hauteur = $image['hauteur'];
	
	include_spip('inc/modifier');
	
	if($angle==0){
		return '';
	}
	if($angle==-90){
		$n['posx'] = $c['posy'];
		$n['posy'] = $hauteur - ($c['posx'] + $c['width']);
		$n['width'] = $c['height'];
		$n['height'] = $c['width'];
		$err .= legendes_action_revision_legende($id_legende, $n);
	}
	if($angle==90){
		$n['posx'] = $largeur - ($c['posy'] + $c['height']);
		$n['posy'] = $c['posx'];
		$n['width'] = $c['height'];
		$n['height'] = $c['width'];
		$err .= legendes_action_revision_legende($id_legende, $n);
	}
	if($angle==180){
		$n['posx'] = $largeur - ($c['posx'] + $c['width']);
		$n['posy'] = $hauteur - ($c['posy'] + $c['height']);
		$n['width'] = $c['width'];
		$n['height'] = $c['height'];
		$err .= legendes_action_revision_legende($id_legende, $n);
	}
	
	return $err;

}

?>