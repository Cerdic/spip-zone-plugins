<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// Fonction appelee par 'evenement_voir', via le fond '/prive/infos/evenement.html'.
// Permet de verifier qu'on est bien autorise changer de statut a la volee

function instituer_evenement($id_evenement, $statut=-1){
	$autorisation = autoriser('modifier', 'evenement', $id_evenement);

	if ($autorisation) {
		$instituer_evenement = charger_fonction('instituer_evenement', 'inc');
		return $instituer_evenement($id_evenement, $statut);
	}

	return "";
}

// sur le modele de presentation.php/voir_en_ligne
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

?>
