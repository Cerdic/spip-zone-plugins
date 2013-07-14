<?php
include_spip('inc/config');
function formulaires_bible_reference_charger_dist($lang='fr',$cadre=1){
    
	$valeurs = array(
		'cadre'=>$cadre,
	   'action'    => $script,
	   'version'      =>  lire_config('bible/traduction_'.$lang),
	   'lang'      =>$lang,
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
    $resultat = bible($passage,$version,true);

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
    include_spip('inc/filtres');
    $passage    = str_replace(' ','',_request('passage'));
    $version    = _request('version');
    $numeros    = _request('numeros');
    $retour     = _request('retour');
    $ref        = _request('ref');
    include_spip('bible_fonctions');
    include_spip('inc/utils');
    $resultat = proteger_amp(recuperer_fond('modeles/bible',array('passage'=>$passage,'traduction'=>$version,'retour'=>!$retour ? 'non' : $retour,'numeros'=>!$numeros ? 'non' : $numeros,'ref'=>!$ref ? 'non' : $ref,'propre'=>'non')));
    


    return array('message_ok'=>array('resultat'   =>  $resultat,
    			'passage'	=> $passage,
                'numeros'   =>  $numeros,
                'retour'    =>  $retour,
                'ref'       =>  $ref,
                'version'   =>  $version    
                ));


	
	 // on vérifie pas ...
}



?>