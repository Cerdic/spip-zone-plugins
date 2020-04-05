<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// chargement des valeurs par defaut des champs du formulaire
function formulaires_navigateur_a2a_charger($id_article){
	$parents = array();
	$id_article_orig = $id_article;
	return 
		array(
			'parents' => $parents,
			'id_article_orig' => $id_article_orig,
			'type_liaison' => ''
		);
}

function formulaires_navigateur_a2a_verifier($id_article){
	$nv_type_liaison = _request('type_liaison');
	$types_liaions = array_keys(lister_types_liaisons());
	if ($nv_type_liaison){
		if (!in_array($nv_type_liaison,$types_liaions)){
			$erreurs = array('message_erreur'=>_T('a2a:type_inexistant'));
		}
	}
	elseif(lire_config('a2a/type_obligatoire')){
		$erreurs = array('message_erreur'=>_T('a2a:type_inexistant'));
	}
	$parents = _request('parents');
	if (!is_array($parents)){
		$erreurs = array('message_erreur'=>_T('a2a:explication_navigateur'));
	}
	return $erreurs;
}

function formulaires_navigateur_a2a_traiter($id_article){
	include_spip('action/a2a');
	$retour = array('editable' => true);
	
	$parents = _request('parents');
	$type = _request('both') ? 'both' : 'uni';
	$type_liaison = _request('type_liaison');	

	foreach($parents as $p){
		$p = explode('|',$p);
		if (preg_match('/^[a-z0-9_]+$/i', $objet=$p[0])){ // securite
			$id_article_cible = intval($p[1]);
			$action_a2a = charger_fonction('a2a_lier_article','action');
			$action_a2a($id_article_cible, $id_article, $type_liaison, $type);
		}
	}

	include_spip('inc/headers');
	$retour['redirect'] = self();
	
	return $retour;
}

