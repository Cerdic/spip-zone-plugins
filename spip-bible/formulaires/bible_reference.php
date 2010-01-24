<?php
include_spip('inc/texte');
include_spip('inc/lien');

function formulaires_bible_reference_charger_dist($lang='fr'){
    
	$valeurs = array(
	   'action'    => $script,
	   'version'      =>  lire_config('bible/traduction_'.$lang),
	   'lang'      =>$lang;
	   'numeros'   => lire_config('bible/numeros'),
	   'retour'    => lire_config('bible/retour'),
	   'ref'       => lire_config('bible/ref'),
	);
	return $valeurs;
}
function formulaires_bible_reference_verifier_dist(){
    
    $passage    = str_replace(' ','',_request('passage'));
    $version    = _request('version');
    $numeros    = _request('numeros');
    $retour     = _request('retour');
    $ref        = _request('ref');
    include_spip('bible_fonctions');
    $resultat = bible($passage,$version);

    if ($resultat == _T('bible:pas_livre')){
        return array('erreur'   =>  _T('bible:form_ref_incorrecte'),
                    'numeros'   =>  $numeros,
                    'retour'    =>  $retour,
                    'ref'       =>  $ref,
                    'version'   =>  $version    
                    );
    
    }
    
}

function formulaires_bible_reference_traiter_dist(){

    $passage    = str_replace(' ','',_request('passage'));
    $version    = _request('version');
    $numeros    = _request('numeros');
    $retour     = _request('retour');
    $ref        = _request('ref');
    include_spip('bible_fonctions');
    $resultat = bible($passage,$version,!$retour ? 'non' : $retour,!$numeros ? 'non' : $numeros,!$ref ? 'non' : $ref);


    return array('message_ok'=>array('resultat'   =>  $resultat,
                'numeros'   =>  $numeros,
                'retour'    =>  $retour,
                'ref'       =>  $ref,
                'version'   =>  $version    
                ));


	
	 // on vérifie pas ...
}



?>