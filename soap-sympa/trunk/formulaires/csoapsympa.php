<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');



function formulaires_csoapsympa_saisies_dist(){
$config = @unserialize($GLOBALS['meta']['soapsympa']);

$valeurs = array(
		    array('saisie' => 'input',
		    'options' => array(
		    'nom' => 'serveur_distant',
		    'label' => _T('soapsympa:serveur_wsdl'),
		    'obligatoire' => 'oui',
		    'size' => 50,
		    'defaut' => $config['serveur_distant']) // une saisie
		    ),
		    array('saisie' => 'input',
			'options' => array(
			'nom' => 'remote_host',
			'label' => _T('soapsympa:nom_domaine'),
			
			'size' => 50,
			'defaut' => $config['remote_host']) // une saisie
		    ),
array('saisie' => 'input',
			'options' => array(
			'nom' => 'identifiant',
			'label' => _T('soapsympa:identifiant'),
			'size' => 50,
			'obligatoire' => 'oui',
			'defaut' => $config['identifiant']) // une saisie
		    ),
array('saisie' => 'input',
			'options' => array(
			'nom' => 'mot_de_passe',
			'label' => _T('soapsympa:mot_de_passe'),
			'size' => 50,
			'obligatoire' => 'oui',
			'defaut' => $config['mot_de_passe']) // une saisie
		    ) ,

array('saisie' => 'input',
			'options' => array(
			'nom' => 'proprietaire',
			'label' => _T('soapsympa:email_proprietaire'),
			'size' => 50,
			'obligatoire' => 'oui',
			'defaut' => $config['proprietaire']) // une saisie
		    ) 
    );

return $valeurs;
	
}

function formulaires_csoapsympa_verifier_dist(){
	$erreurs = array() ;
	
	if( _request('serveur_distant') != "0" ) {
		include_spip('soapsympa_pipeline');
		//a revoir
		//$erreurs = soapsympa_api_tester(_request('serveur_distant'), _request('identifiant'), _request('mot_de_passe')) ;
	}
	return $erreurs;
}


function formulaires_csoapsympa_traiter_dist(){
refuser_traiter_formulaire_ajax();
$res = array();
$config = unserialize($GLOBALS['meta']['soapsympa']);
if (!is_array($config)) {
$config = array();
}
$config = array_merge($config, array(
				'serveur_distant' => _request('serveur_distant'),
				'remote_host' => _request('remote_host'),
				'identifiant' => _request('identifiant'),
				'mot_de_passe' => _request('mot_de_passe'),
				//'robot' => _request('robot'),
				'proprietaire' => _request('proprietaire'),
		));
		ecrire_meta('soapsympa', serialize($config));
		$res['message_ok'] = _T('soapsympa:enregistrement_reussi');
		$res['editable'] = true;
		return $res ;
}
?>