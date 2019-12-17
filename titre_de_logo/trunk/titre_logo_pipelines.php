<?php

/*
 * Plugin Titre de logo
 *
 * @plugin Titre de logo
 *
 * @copyright  2015
 * @author Arno*
 * @licenceGPL 3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function titre_logo_recuperer_fond($flux) {
	if ($flux['args']['fond'] == 'formulaires/editer_logo') {
		$id_objet	= $flux['args']['contexte']['id_objet'];
		$objet		= $flux['args']['contexte']['objet'];
		$editable	= $flux['args']['contexte']['_options']['editable'];

		if ($editable) {
			$objets_autorises = lire_config('titre_logo/objets_autorises', array('spip_articles'));
			$objets_autorises = (isset($objets_autorises))
				? array_filter($objets_autorises)
				: array();

			$table_objet = table_objet_sql($objet);
			$texte = $flux['data']['texte'];

			// regarder si c'est bien l'otion a bien été activé pour cet objet
			// et si une image été téleverser
			if (in_array($table_objet, $objets_autorises) && isset($flux['args']['contexte']['logo_on'])) {
				$cont = array(
					'objet'		=> $objet,
					'id_objet'	=> $id_objet
				);

				$ajouter = recuperer_fond('prive/inc_editer_titre_logo', $cont);
				$flux['data']['texte']= str_replace('</form>', '</form>'.$ajouter, $texte);
			}
		}
	}
	return $flux;
}
