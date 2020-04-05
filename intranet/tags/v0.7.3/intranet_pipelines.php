<?php
/**
 * Plugin Intranet
 *
 * (c) 2013-2016 kent1
 * Distribue sous licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion dans le pipeline boite_infos (SPIP)
 *
 * Ajoute un bouton dans la boite descriptive des objets pour les sortir de l'intranet
 *
 * @pipeline boite_infos
 * @param array $flux
 * @return array
 */
function intranet_boite_infos($flux) {
	$objet = $flux['args']['type'];
	include_spip('inc/filtres');
	if (lire_config('intranet/intranet_ouverts', '') == 'on'
		and objet_info($objet, 'principale') == 'oui'
		and $id_objet = intval($flux['args']['id'])
		and objet_info($objet, 'page') !== false
		and autoriser('instituer', $objet, $id_objet)) {
		$statut = objet_info($objet, 'statut');
		if (isset($statut[0]['champ']) && isset($statut[0]['publie'])) {
			$statut_objet = sql_getfetsel($statut[0]['champ'], table_objet_sql($objet), id_table_objet($objet) .' = '.intval($id_objet));
			if ($statut_objet == $statut[0]['publie']) {
				include_spip('inc/presentation');
				$existe = sql_getfetsel('objet', 'spip_intranet_ouverts', 'objet='.sql_quote($objet). ' AND id_objet='.intval($id_objet));

				if ($existe) {
					$action = 'moins';
					$message = _T('intranet:message_intranet_remettre');
					$image = 'intranet-plus-24.png';
				} else {
					$action = 'plus';
					$message = _T('intranet:message_intranet_sortir');
					$image = 'intranet-moins-24.png';
				}

				$h = redirige_action_auteur('intranet_sortir', "$id_objet/$objet/$action", _request('exec'), id_table_objet($objet).'='._request(id_table_objet($objet)).'&modif=oui');
				$icone = icone_horizontale($message, $h, $image, 'rien.gif', false);

				if ($p = strpos($flux['data'], '</ul>')) {
					while ($q = strpos($flux['data'], '</ul>', $p+5)) {
						$p = $q;
					}
					$flux['data'] = substr($flux['data'], 0, $p+5) . $icone . substr($flux['data'], $p+5);
				} else {
					$flux['data'].= $icone;
				}
			}
		}
	}
	return $flux;
}
