<?php
/**
 * Plugin Comptes & Contacts pour Spip 2.0
 * Licence GPL (c) 2009 - 2010 - Ateliers CYM
 */

/**
 *
 * Insertion dans le pipeline affiche_milieu
 * Dans la page auteur_infos, insertion des champs spécifiques d'Inscription2
 *
 * @return array Le $flux modifié
 * @param array $flux
 */
function comptes_affiche_milieu($flux){
	if($flux['args']['exec'] == 'auteur_infos') {
#		$exceptions_des_champs_auteurs_elargis = pipeline('i2_exceptions_des_champs_auteurs_elargis',array());
#		$legender_auteur_supp = recuperer_fond('prive/inscription2_fiche',array('id_auteur'=>$flux['args']['id_auteur'],'exceptions'=>$exceptions_des_champs_auteurs_elargis));
		$flux['data'] .= recuperer_fond('prive/listes/adresses',array('id_auteur'=>$flux['args']['id_auteur']));
		$flux['data'] .= recuperer_fond('prive/listes/numeros',array('id_auteur'=>$flux['args']['id_auteur']));
	}
	return $flux;
}

/**
 *
 * Insertion dans le pipeline editer_contenu_objet
 * Ajoute les champs I2 sur le formulaire CVT editer_auteur
 *
 * @return array Le $flux complété
 * @param array $flux
 */
function comptes_editer_contenu_objet($flux){
	if ($flux['args']['type']=='auteur') {
		include_spip('public/assembler');
		include_spip('inc/legender_auteur_supp');
		/**
		 *
		 * Insertion des champs dans le formulaire aprs le textarea PGP
		 *
		 */
		$adresses = "<li class='editer_adresses fieldset'>\n"
					."<fieldset>"
					."<h3 class='legend'>Adresses :</h3>"
					.recuperer_fond('prive/listes/adresses',array('id_auteur'=>$flux['args']['contexte']['id_auteur']))
					."</fieldset>"
					."</li>";
		$numeros = "<li class='editer_numeros fieldset'>\n"
					."<fieldset>"
					."<h3 class='legend'>Numeros :</h3>"
					.recuperer_fond('prive/listes/numeros',array('id_auteur'=>$flux['args']['contexte']['id_auteur']))
					."</fieldset>"
					."</li>";
		$flux['data'] = preg_replace('%(<li class="editer_pgp(.*?)</li>)%is', '$1'."\n".$adresses.$numeros, $flux['data']);
	}
	return $flux;
}




?>
