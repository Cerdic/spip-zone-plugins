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
	   'forme_livre' => lire_config('bible/forme_livre'),
	   'url'	=> lire_config("bible/url")
	);
	return $valeurs;
}
function formulaires_bible_reference_verifier_dist(){
    
    $passage    = str_replace(' ','',_request('passage'));
    $version    = _request('version');
    $numeros    = _request('numeros');
    $retour     = _request('retour');
    $ref        = _request('ref');
    $forme_livre  = _request('forme_livre');
    $url 	= _request('url');
    include_spip('bible_fonctions');
    $resultat = bible($passage,$version,true);

    if ($resultat == _T('bible:pas_livre')){
        return array('erreur'   =>  _T('bible:form_ref_incorrecte'),
                    'numeros'   =>  $numeros,
                    'retour'    =>  $retour,
                    'ref'       =>  $ref,
                    'version'   =>  $version,
		    'forme_livre'=> $forme_livre,
		    'url'=> $url   
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
    $forme_livre  = _request('forme_livre');
    $url  = _request('url');
    include_spip('bible_fonctions');
    include_spip('inc/utils');
    $resultat = proteger_amp(recuperer_fond('modeles/bible',
	array(	'passage'=>$passage,
		'traduction'=>$version,
		'retour'=>!$retour ? 'non' : $retour,
		'numeros'=>!$numeros ? 'non' : $numeros,
		'ref'=>!$ref ? 'non' : $ref,
		'url'=>!$url ? 'non' : $url,
		'forme_livre'=>$forme_livre ?  $forme_livre : 'abbr',
		'propre'=>'non')));
    


    return array('message_ok'=>array('resultat'   =>  $resultat,
    			'passage'	=> $passage,
                'numeros'   =>  $numeros,
                'retour'    =>  $retour,
                'ref'       =>  $ref,
		'url'	     => $url,
                'version'   =>  $version,
		'forme_livre'=>$forme_livre  
                ));

}
?>