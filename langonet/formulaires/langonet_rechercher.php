<?php

function formulaires_langonet_rechercher_charger() {
	return array('pattern' => _request('pattern'),
				'recherche' => _request('recherche'));
}

function formulaires_langonet_rechercher_verifier() {
	$erreurs = array();
	if (!_request('pattern')) {
		$erreurs['pattern'] = _T('langonet:message_nok_champ_obligatoire');
	}
	return $erreurs;
}

function formulaires_langonet_rechercher_traiter() {

	// Recuperation des champs du formulaire
	$pattern = _request('pattern');
	$recherche = _request('recherche');
	$langonet_rechercher_item = charger_fonction('langonet_rechercher_item','inc');

	// Verification et formatage des resultats pour affichage
	$retour = array();
	$resultats = $langonet_rechercher_item($pattern, $recherche);
	if ($resultats['erreur']) {
		$retour['message_erreur'] = $resultats['erreur'];
	}
	else {
		$retour = formater_recherche($recherche, $resultats);
	}
	$retour['editable'] = true;
	return $retour;
}

function formater_recherche($recherche, $resultats) {
	include_spip('inc/layer');
	
	$texte = '';
	$total = 0;
	foreach ($resultats['item_trouve'] as $_pertinence => $_trouves) {
		if ($_trouves) {
			$total += count($_trouves);
			// On démarre un groupe d'items trouves avec un message
			$suffixe = (count($_trouves) == 1 ? '_1' : '_n');
			$texte .= '<div style="margin-bottom: 20px">' . "\n";
			$texte .= '<div class="success">' . "\n";
			$texte .= _T('langonet:message_ok_item_trouve_' . $_pertinence . $suffixe, array('sous_total' => count($_trouves))) . "\n";
			$texte .= '</div>' . "\n";
			foreach ($_trouves as $_item => $_fichiers) {
				$texte .= bouton_block_depliable($_item . ' (' . count($_fichiers) . ')', false);
				$texte .= debut_block_depliable(false);
				$texte .= "<p style=\"padding-left:2em;\">  "._T('langonet:texte_item_defini_ou')."\n<br />";
				foreach ($_fichiers as $fichier_def) {
					$texte .= "\t<span style=\"font-weight:bold;padding-left:2em;\">" .$fichier_def. "</span><br />\n";
				}
				$texte .= "</p>\n";
				$texte .= fin_block();
			}
			$texte .= '</div>' . "\n";
		}
	}
	
	// Tout s'est bien passe on renvoie le message ok et les resultats de la verification
	$retour['message_ok']['resume'] = _T('langonet:message_ok_item_trouve', array('pattern' => $resultats['pattern']));
	$retour['message_ok']['total'] = $total;
	$retour['message_ok']['trouves'] = $texte;

	return $retour;
}

?>