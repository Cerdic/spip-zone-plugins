<?php
/***************************************************************************\
 *  SPIPAL, Utilitaire de paiement en ligne pour SPIP                      *
 *                                                                         *
 *  Copyright (c) 2007 Thierry Schmit                                      *
 *  Copyright (c) 2011 Emmanuel Saint-James                                *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('base/abstract_sql');

function balise_FORMULAIRE_PAYER($p) {
    return calculer_balise_dynamique($p, 'FORMULAIRE_PAYER', array('id_article'));
}

function balise_FORMULAIRE_PAYER_stat($args, $filtres) {
    if ( !$args[0] )
        return erreur_squelette(
            _T('zbug_champ_hors_motif',
                array ('champ' => '#FORMULAIRE_PAYER',
                       'motif' => 'pas de contexte ARTICLES')), '');
    
    $row = sql_fetsel(
        'id_article, ref_produit,don,prix_unitaire_ht,tva,nom_com',
	'spip_spipal_produits',
	'id_article='.intval($args[0])
    );

    return $row;
}

function balise_FORMULAIRE_PAYER_dyn($id_article, $ref_produit, $don, $prix_unitaire_ht, $tva, $nom_com) {
    $quantite          = _request('quantite');
    $taxes = 0.0;
    if ( !$don ) 
        $taxes = round($prix_unitaire_ht * ($tva / 100), 2);
    $prix_unitaire_ttc = $taxes + $prix_unitaire_ht;
    
    if ( $quantite === null ) {
        $quantite = 1;
    }
    else {
        $quantite = intval($quantite);
    }
    
    return array(
        'formulaires/formulaire_payer', 
        0, 
        array(
            'custom'            => serialize(array('id_auteur' => (isset($GLOBALS['auteur_session']['id_auteur']))?$GLOBALS['auteur_session']['id_auteur']:0)),
            'id_article'        => $id_article,
            'ref_produit'       => $ref_produit,
            'nom_com'           => $nom_com,
            'don'               => $don,
            'quantite'          => $quantite,
            'prix_unitaire_ht'  => $prix_unitaire_ht,
            'taxes'             => $taxes,
            'prix_unitaire_ttc' => $prix_unitaire_ttc,
            'monnaie'           => 'EUR',
            'total_ttc'         => $quantite * $prix_unitaire_ttc,
            'dir_notification'  => _DIR_PLUGIN_SPIPAL
        )
    );
}
?>
