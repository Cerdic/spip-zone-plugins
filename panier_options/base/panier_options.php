<?php
/**
 * Plugin Panier Options
 * (c) 2015 Anne-lise Martenot / Elastick.net
 * Licence GPL V3
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


function clientele_declarer_champs_extras($champs = array()){
	
        /* PRENOM sur AUTEUR */
	$champs['spip_paniers']['prenom'] = array(
                                             'saisie' => 'input', // Type du champs (voir plugin Saisies)
                                             'options' => array(
                                                                'nom' => 'prenom',
                                                                'label' => _T('clientele:label_prenom'),
                                                                'sql' => "text NOT NULL DEFAULT ''",
                                                                'defaut' => '', // Valeur par défaut
                                                                ),
                                             );
     
	
	/* nom_client sur spip_adresses */
	$champs['spip_adresses']['nom_client'] = array(
                                                   'saisie' => 'input', // Type du champs (voir plugin Saisies)
                                                   'options' => array(
                                                                      'nom' => 'nom_client', // nom sql
                                                                      'label' => _T('clientele:label_nom'), // chaine de langue 'prefix:cle'
                                                                      'sql' => "text NOT NULL DEFAULT ''", // declaration sql
                                                                      'defaut' => '', // Valeur par défaut
                                                                      ),
                                                   );
        /* profession_client sur spip_adresses */
	$champs['spip_adresses']['profession_client'] = array(
                                                   'saisie' => 'input', // Type du champs (voir plugin Saisies)
                                                   'options' => array(
                                                                      'nom' => 'profession_client', // nom sql
                                                                      'label' => _T('clientele:label_profession'), // chaine de langue 'prefix:cle'
                                                                      'sql' => "text NOT NULL DEFAULT ''", // declaration sql
                                                                      'defaut' => '', // Valeur par défaut
                                                                      ),
                                                   );
	
	return $champs;
}
