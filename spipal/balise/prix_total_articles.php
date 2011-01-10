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

function balise_PRIX_TOTAL_ARTICLE($p) {
    $ttc = interprete_argument_balise(1,$p);
    if ( !$ttc )
        $ttc = 'ttc';
    $tva = 0;
    if ( $ttc == 'ttc' ) {
        $tva = champ_sql('tva', $p);
    }
    $_taxe = "(1 + $tva / 100)";
    $_prix_unitaire_ht = champ_sql('prix_unitaire_ht', $p);
    $_quantite      = champ_sql('quantite', $p);
    
    $p->code = "number_format($_prix_unitaire_ht * $_quantite * $_taxe, 2, ',', ' ')";
    
    return $p;
}

?>
