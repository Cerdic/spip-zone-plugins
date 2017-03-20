<?php
/**
 * Pipelines de plugin Logos Rôles
 *
 * @plugin     logos_roles
 * @copyright  2016
 * @author     bystrano
 * @licence    GNU/GPL
 */

/**
 * Empêcher les logos de sortir dans les boucles DOCUMENTS standard. C'est
 * nécessaire pour la rétro-compatibilité avec les squelettes existants. Pour
 * Pour voir les logos dans les boucles DOCUMENTS, il faut utiliser
 * explicitement le critère {role}
 *
 * @pipeline pre_boucle
 * @param  array $boucle Données du pipeline
 * @return array       Données du pipeline
 */
function logos_roles_pre_boucle($boucle) {

	if ($boucle->type_requete === 'documents') {

		$utilise_critere_logo = false;
		foreach ($boucle->criteres as $critere) {
			if ($critere->type === 'critere') {
				if (($critere->param[0][0]->texte === 'role') or
					($critere->op === 'role')) {

					$utilise_critere_logo = true;
				}
			}
		}

		if (! $utilise_critere_logo) {
			include_spip('inc/objets');
			$table_liens = table_objet_sql('documents') . '_liens';
			$abbrev_table_lien = array_search($table_liens, $boucle->from);

			if (! $boucle->modificateur['tout']) {
				$boucle->where[] = array(
					"'NOT REGEXP'",
					"'$abbrev_table_lien.role'",
					"'\'^logo\''"
				);
			}
		}
	}

	return $boucle;
}

/**
 * Insérer du js et du css dans le head de l'espace privé
 *
 * @pipeline header_prive
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function logos_roles_header_prive($flux) {

	$flux .= '<script type="text/javascript" src="'
		. find_in_path('javascript/logos_roles.js')
		. '"></script>';

	return $flux;
}