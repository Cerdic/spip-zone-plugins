<?php
/*
 * Plugin Encarts
 * (c) 2011 Camille Lafitte, Cyril Marion
 * Avec l'aide de Matthieu Marcillaud
 * Distribue sous licence GPL
 *
 */


function encarts_afficher_fiche_objet($flux) {


	// si on est sur la page ?exec=articles on affiche le bouton et/ou le bloc encarts
	if ($flux['args']['type'] == 'article' AND $id_article = $flux['args']['id']) {
	
		
		// a corriger $_GET... trop permissif
		$contexte = $_GET;
		$flux['data'] .= recuperer_fond('prive/boite/encarts_article', $contexte, array('ajax'=>true));

	}

	return $flux;
}

if (!defined('_TYPES_ENCARTS')) {
	define('_TYPES_ENCARTS', 'encart|marge');
}

/**
 * Traiter les textes contenant des <encart> .... </encart>
 * ou des <marge>...</marge>
 * en les remplaçant par un span...
 *
 * @param string $texte à analyser 
 * @return texte modifié
**/
function encarts_pre_propre($texte) {
	if (false !== strpos($texte, '<')) {
		if (preg_match_all(',<(' . _TYPES_ENCARTS . ')>(.*?)</\1>,is', $texte, $regs, PREG_SET_ORDER)) {
			foreach ($regs as $reg) {
				$css = 'encart';
				if ($reg[1] != 'encart') {
					$css .= " " . $reg[1];
				}
				$texte = str_replace($reg[0], "<span class='$css'>".$reg[2]."</span>", $texte);
			}
		}
	}
	return $texte;
}

/**
 * Mettre les vu=oui lorsque l'on met un modèle
 * d'encart dans un texte.
 *
**/
function encarts_post_edition($flux) {
	if (!in_array($flux['args']['type'], array('forum','signature'))) {
		$marquer_doublons_encart = charger_fonction('marquer_doublons_encart', 'inc');
		$marquer_doublons_encart($flux['data'],$flux['args']['id_objet'],$flux['args']['type'],id_table_objet($flux['args']['type'], $flux['args']['serveur']),$flux['args']['table_objet'],$flux['args']['spip_table_objet'], '', $flux['args']['serveur']);
	}
	return $flux;
}
?>
