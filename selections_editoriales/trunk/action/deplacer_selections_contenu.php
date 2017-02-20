<?php
/**
 * Déplace le rang d'un contenu de sélection
 *
 * @plugin     Sélections éditoriales
 * @copyright  2016
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Selections_editoriales\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_deplacer_selections_contenu_dist($arg = null) {
	if (is_null($arg)) {
		// DEMI sécurité : s'il y a un hash, on teste la sécurité
		if (_request('hash')) {
			$securiser_action = charger_fonction('securiser_action', 'inc');
			$arg = $securiser_action();
		} else {
			// Sinon, on prend l'arg direct
			$arg = _request('arg');
		}
	}

	// Argument de la forme "123-haut" ou "123-bas" ou "123-3" (rang précis)
	list($id_selections_contenu, $deplacement) = explode('-', $arg);

	// Il faut pouvoir modifier le contenu et que le déplacement soit un truc valide
	if (
		$id_selections_contenu = intval($id_selections_contenu)
		and autoriser('modifier', 'selections_contenu', $id_selections_contenu)
		and (
			in_array($deplacement, array('haut', 'bas'))
			or ($nouveau_rang = intval($deplacement) and $nouveau_rang >= 0)
		)
	) {
		// On cherche le parent
		$id_parent = sql_getfetsel('id_selection', 'spip_selections_contenus', 'id_selections_contenu = '.$id_selections_contenu);

		// On cherche le rang de lu contenu en question
		$rang = sql_getfetsel('rang', 'spip_selections_contenus', 'id_selections_contenu = '.$id_selections_contenu);

		// On cherche le rang le plus grand du même parent
		$dernier_rang = sql_getfetsel('rang', 'spip_selections_contenus', 'id_selection = '.$id_parent, '', 'rang desc', '0,1');

		// On teste maintenant les différents cas
		if ($deplacement === 'bas') {
			// Si c'était tout en bas, on remonte en haut
			if ($rang >= $dernier_rang) {
				$nouveau_rang = 1;
				// On décale tous les rangs vers le bas
				sql_update(
					'spip_selections_contenus',
					array('rang' => 'rang + 1'),
					'id_selection = '.$id_parent
				);
			} else {
				$nouveau_rang = $rang + 1;
				// On échange avec le contenu qui avait ce rang là
				sql_updateq(
					'spip_selections_contenus',
					array('rang' => $rang),
					array(
						'id_selection = '.$id_parent,
						'rang = '.$nouveau_rang
					)
				);
			}
		} elseif ($deplacement === 'haut') {
			// Si c'était tout en haut, on redescend tout en bas
			if ($rang <= 1) {
				$nouveau_rang = $dernier_rang;
				// On décale tous les rangs vers le haut
				sql_update(
					'spip_selections_contenus',
					array('rang' => 'rang - 1'),
					'id_selection = '.$id_parent
				);
			} else {
				$nouveau_rang = $rang - 1;
				// On échange avec le contenu qui avait ce rang là
				sql_updateq(
					'spip_selections_contenus',
					array('rang' => $rang),
					array(
						'id_selection = '.$id_parent,
						'rang = '.$nouveau_rang
					)
				);
			}
		} elseif ($nouveau_rang) {
			// Si le nouveau rang est inférieur au rang actuel, on décale tous vers le bas entre les deux
			if ($nouveau_rang < $rang) {
				sql_update(
					'spip_selections_contenus',
					array('rang' => 'rang + 1'),
					array(
						'id_selection = '.$id_parent,
						'rang >= '.$nouveau_rang,
						'rang < '.$rang
					)
				);
			} elseif ($nouveau_rang > $rang) {
				// Sinon l'inverse
				sql_update(
					'spip_selections_contenus',
					array('rang' => 'rang - 1'),
					array(
						'id_selection = '.$id_parent,
						'rang <= '.$nouveau_rang,
						'rang > '.$rang
					)
				);
			}
		}

		// On change enfin le nouveau rang maintenant qu'on a déplacé le reste !
		sql_updateq(
			'spip_selections_contenus',
			array('rang' => $nouveau_rang),
			'id_selections_contenu = '.$id_selections_contenu
		);
	}
}
