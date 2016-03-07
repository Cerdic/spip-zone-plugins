<?php
/**
 * Déplace le rang d'une étape
 *
 * @plugin     Itinéraires
 * @copyright  2016
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Itineraires\Etapes
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_deplacer_itineraires_etape_dist($arg=null) {
	if (is_null($arg)) {
		// DEMI sécurité : s'il y a un hash, on teste la sécurité
		if (_request('hash')) {
			$securiser_action = charger_fonction('securiser_action', 'inc');
			$arg = $securiser_action();
		}
		// Sinon, on prend l'arg direct
		else {
			$arg = _request('arg');
		}
	}
	
	// Argument de la forme "123-haut" ou "123-bas" ou "123-3" (rang précis)
	list($id_itineraires_etape, $deplacement) = explode('-', $arg);
	
	// Il faut pouvoir modifier l'étape et que le déplacement soit un truc valide
	if (
		$id_itineraires_etape = intval($id_itineraires_etape)
		and autoriser('modifier', 'itineraires_etape', $id_itineraires_etape)
		and (
			in_array($deplacement, array('haut', 'bas'))
			or ($nouveau_rang = intval($deplacement) and $nouveau_rang >= 0)
		)
	) {
		// On cherche le parent
		$id_parent = sql_getfetsel('id_itineraire', 'spip_itineraires_etapes', 'id_itineraires_etape = '.$id_itineraires_etape);
		
		// On cherche le rang de l'étape en question
		$rang = sql_getfetsel('rang', 'spip_itineraires_etapes', 'id_itineraires_etape = '.$id_itineraires_etape);
		
		// On cherche le rang le plus grand du même parent
		$dernier_rang = sql_getfetsel('rang', 'spip_itineraires_etapes', 'id_itineraire = '.$id_parent, '', 'rang desc', '0,1');
		
		// On teste maintenant les différents cas
		if ($deplacement === 'bas') {
			// Si c'était tout en bas, on remonte en haut
			if ($rang >= $dernier_rang) {
				$nouveau_rang = 1;
				// On décale tous les rangs vers le bas
				sql_update(
					'spip_itineraires_etapes',
					array('rang' => 'rang + 1'),
					'id_itineraire = '.$id_parent
				);
			}
			else {
				$nouveau_rang = $rang + 1;
				// On échange avec l'étape qui avait ce rang là
				sql_updateq(
					'spip_itineraires_etapes',
					array('rang' => $rang),
					array(
						'id_itineraire = '.$id_parent,
						'rang = '.$nouveau_rang
					)
				);
			}
		}
		elseif ($deplacement === 'haut') {
			// Si c'était tout en haut, on redescend tout en bas
			if ($rang <= 1) {
				$nouveau_rang = $dernier_rang;
				// On décale tous les rangs vers le haut
				sql_update(
					'spip_itineraires_etapes',
					array('rang' => 'rang - 1'),
					'id_itineraire = '.$id_parent
				);
			}
			else {
				$nouveau_rang = $rang - 1;
				// On échange avec l'étape qui avait ce rang là
				sql_updateq(
					'spip_itineraires_etapes',
					array('rang' => $rang),
					array(
						'id_itineraire = '.$id_parent,
						'rang = '.$nouveau_rang
					)
				);
			}
		}
		elseif ($nouveau_rang) {
			// Si le nouveau rang est inférieur au rang actuel, on décale tous vers le bas entre les deux
			if ($nouveau_rang < $rang) {
				sql_update(
					'spip_itineraires_etapes',
					array('rang' => 'rang + 1'),
					array(
						'id_itineraire = '.$id_parent,
						'rang >= '.$nouveau_rang,
						'rang < '.$rang
					)
				);
			}
			// Sinon l'inverse
			elseif ($nouveau_rang > $rang) {
				sql_update(
					'spip_itineraires_etapes',
					array('rang' => 'rang - 1'),
					array(
						'id_itineraire = '.$id_parent,
						'rang <= '.$nouveau_rang,
						'rang > '.$rang
					)
				);
			}
		}
		
		// On change enfin le nouveau rang maintenant qu'on a déplacé le reste !
		sql_updateq(
			'spip_itineraires_etapes',
			array('rang' => $nouveau_rang),
			'id_itineraires_etape = '.$id_itineraires_etape
		);
	}
}
