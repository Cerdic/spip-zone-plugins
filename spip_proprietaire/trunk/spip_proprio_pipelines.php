<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion des infos proprietaires dans le header.
 */
function spip_proprio_insert_head($flux) {
	$proprietaire = spip_proprio_recuperer_config();
	$flux .= '<!-- SpipProprio insert head -->';
	if (!empty($proprietaire['proprietaire_nom'])) {
		$flux .= "\n<meta name=\"copyright\" content=\"".textebrut($proprietaire['proprietaire_nom']).'" />';
	}
	if (!empty($proprietaire['createur_nom']) || !empty($proprietaire['proprietaire_nom'])) {
		$flux .= "\n<meta name=\"author\" content=\"".textebrut(!empty($proprietaire['createur_nom']) ? $proprietaire['createur_nom'] : $proprietaire['proprietaire_nom']).'" />';
	}
	if (!empty($proprietaire['proprietaire_mail_administratif'])) {
		$flux .= "\n<meta name=\"reply-to\" content=\"".$proprietaire['proprietaire_mail_administratif'].'" />';
	}
	$flux .= "<!-- END ADX Menu insert head -->\n";

	return $flux;
}
