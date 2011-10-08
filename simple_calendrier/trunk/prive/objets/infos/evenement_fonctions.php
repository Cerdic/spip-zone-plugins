<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

function instituer_evenement($id_evenement, $id_rubrique, $statut=-1){
	if ($id_rubrique){
        $is_autorise = autoriser('publierdans', 'rubrique', $id_rubrique);
    } else{
        $is_autorise = autoriser('modifier', 'evenement', $id_evenement);
    }
	if ($is_autorise) {
		$instituer_evenement = charger_fonction('instituer_evenement', 'inc');
		return $instituer_evenement($id_evenement,$statut);
	}
	return "";
}

// Fonction A virer. sur le modele de presentation.php/voir_en_ligne
/*
function simplecal_voir_en_ligne ($type, $id, $statut=false, $image='racine-24.gif', $af = true, $inline=true) {
    $en_ligne = $message = '';
	switch ($type) {
        case 'evenement':
            if ($statut == 'publie')
                $en_ligne = 'calcul';
            else if ($statut == 'prop')
                $en_ligne = 'preview';
            break;
        
        default: return '';
	}

	if ($en_ligne == 'calcul') {
		$message = _T('icone_voir_en_ligne');
    }
	else if ($en_ligne == 'preview'	and autoriser('previsualiser')) {
		$message = _T('previsualiser');
    }
	else {
		return '';
    }

	$h = generer_url_action('redirect', "type=$type&id=$id&var_mode=$en_ligne");

	return $inline  
        ? icone_inline($message, $h, $image, "rien.gif", $GLOBALS['spip_lang_left'])
        : icone_horizontale($message, $h, $image, "rien.gif",$af);
}
*/
?>
