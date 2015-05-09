<?php
/**
 * Utilisations de pipelines par encarts
 *
 * @plugin     encarts
 * @copyright  2013
 * @author     Cyril
 * @licence    GNU/GPL
 * @package    SPIP\Encarts\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Les encarts sont par défaut dans le texte de l'article
 * mais il peuvent être aussi en marge de ce texte
 */
if (!defined('_TYPES_ENCARTS')) {
        define('_TYPES_ENCARTS', 'encart|marge');
}

/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function encarts_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	// encarts sur les articles
	if (!$e['edition'] AND in_array($e['type'], array('article'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'encarts',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}


/**
 * Optimiser la base de données en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function encarts_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('encart'=>'*'),'*');
	return $flux;
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
