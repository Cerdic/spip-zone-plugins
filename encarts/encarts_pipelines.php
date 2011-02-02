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


function encarts_pre_propre($texte) {
	if (false !== strpos($texte, '<')) {
		if (preg_match_all(',<encart>(.*?)</encart>,is', $texte, $regs, PREG_SET_ORDER)) {
			foreach ($regs as $reg) {
				$texte = str_replace($reg[0], "<span class='encart'>".$reg[1]."</span>", $texte);
			}
		}
	}
	return $texte;
}

?>
