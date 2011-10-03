<?php

/**
 * Plugin Groupes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// chargement des valeurs par defaut des champs du formulaire
function formulaires_trouver_objet_charger($objet, $source, $id_source, $identifiant,$paramselecteur='',$retour){
 // on considere objet au singulier + s
	return 
		array(
			'objet' => $objet."s",
			'source' => $source,
			'id_source' => $id_source,
			id_table_objet($source) => $id_source,
			'identifiant' => $identifiant,
			'paramselecteur' => $paramselecteur,
			//'editable' => true,
			'ajax' => 'ajax'
		);
}

function formulaires_trouver_objet_verifier($objet, $source, $id_source, $identifiant,$paramselecteur='',$retour){
	// si pas d'id, le selecteur generique n'a pas fonctionne
	// on fait comment alors ??
	$id_koi ="id_".$objet;

	if (!_request('pid_objet')) {
		return array(
		'message_erreur' => _T('trouvobjet:pas_de_identifiant'),
			);
	}
}

function formulaires_trouver_objet_traiter($objet, $source, $id_source, $identifiant,$paramselecteur='',$retour){
	
  // Empecher le traitement en AJAX car on sait que le formulaire va rediriger autre part
     refuser_traiter_formulaire_ajax();
     
     $id_objet = _request('pid_objet');
     $id_koi ="id_".$objet;
     if($retour) {
     	     //$redirect=generer_url_public($retour,"$objet=$id_objet");
                 include_spip('inc/headers');
         	$redirect = redirige_par_entete(str_replace('&amp;','&', $retour."&$id_koi=$id_objet"));
     }
     else $redirect = generer_url_public($objet,"$id_koi=$id_objet");
    // Valeurs de retours
    return array(
    	'ajax' => ' ',
    	//'editable' => false,
        'redirect'=> $redirect

	    );

}

?>
