<?php
/**
 * Plugin tradrub
 * Licence GPL (c) 2008-2010 Stephane Laurent (Bill), Matthieu Marcillaud
 * 
 */

/**
 * ajouter la liste des traductions et le formulaire pour definir une traduction
 *
 * @param array $flux
 * @return array
 */
function tradrub_affiche_milieu($flux) {
	if (($type = $flux['args']['exec'])=='naviguer'){
		$id = $flux['args']['id_rubrique'];
		$trad = recuperer_fond('prive/traduire/rubrique', array('id_rubrique' => $id));
		$flux['data'] .= $trad;
	}
	return $flux;
}

?>
