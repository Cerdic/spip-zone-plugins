<?php
/**
 * Pipelines du plugin Coordonnees
 *
 * @plugin     Coordonnees
 * @copyright  2013
 * @author     Marcimat / Ateliers CYM
 * @licence    GNU/GPL
 * @package    SPIP\Coordonnees\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) { return;
}

/*
 * function coordonnees_ieconfig_metas
 *
 * export de configuration avec le plugin ieconfig
 *
 * @param $table
 */
function coordonnees_ieconfig_metas($table) {
	$table['coordonnees']['titre'] = _T('paquet-coordonnees:coordonnees_nom');
	$table['coordonnees']['icone'] = 'prive/themes/spip/images/addressbook-16.png';
	$table['coordonnees']['metas_serialize'] = 'contacts_et_organisations';

	return $table;
}
/**
 * Affichage des coordonnées (adresses, mails, numéros)
 * sur la page de visualisation des objets associes
 * Surcharge possible avec 'prive/squelettes/contenu/coordonnees_fiche_nom-objet-associe.html'
**/
function coordonnees_afficher_complement_objet($flux) {
	$texte = '';
	$exec = isset($flux['args']['exec']) ? $flux['args']['exec'] : _request('exec');
	$e = trouver_objet_exec($exec);
	$type = $flux['args']['type'];

	$id_objet = $flux['args']['id'];
	$editable = (in_array(table_objet_sql($type), lire_config('coordonnees/objets', array())) ? 1 : 0);
	$has = false;
	if (!$editable) {
		if (sql_countsel('spip_adresses_liens', 'objet='.sql_quote($type).' AND id_objet='.intval($id_objet))
		  or sql_countsel('spip_numeros_liens', 'objet='.sql_quote($type).' AND id_objet='.intval($id_objet))
		  or sql_countsel('spip_emails_liens', 'objet='.sql_quote($type).' AND id_objet='.intval($id_objet))
		) {
			$has = true;
		}
	}

	$coordonnees_fiche_objet = 'prive/squelettes/contenu/coordonnees_fiche_'.$type;
	if(!find_in_path($coordonnees_fiche_objet.'.html')){
			$coordonnees_fiche_objet = 'prive/squelettes/contenu/coordonnees_fiche_objet';
	}

	if (!$e['edition'] and ($editable or $has)) {
		$texte .= recuperer_fond(
			$coordonnees_fiche_objet,
			array(
			'objet' => $type,
			'id_objet' => intval($flux['args']['id']),
			'editable' => $editable,
			),
			array('ajax'=>'coordonnees')
		);
	}

	if ($texte) {
		if ($p=strpos($flux['data'], '<!--afficher_fiche_objet-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}


/**
 * Liaisons avec les objets
 * sur la page de visualisation des coordonnées
**/
function coordonnees_affiche_gauche($flux) {
	$texte = '';
	$exec = isset($flux['args']['exec']) ? $flux['args']['exec'] : _request('exec');
	$e = trouver_objet_exec($exec);
	if (!$e['edition']
		and $type = $e['type']
		and in_array($type, array('adresse','email','numero'))
		and $id_coordonnee = $flux['args']["id_${type}"]
	) {
		$texte .= recuperer_fond(
			"prive/squelettes/contenu/utilisations_${type}",
			array(
			"id_${type}" => intval($id_coordonnee)
			),
			array('ajax'=>true)
		);
	}

	if ($texte) {
		$flux['data'] .= $texte;
	}

	return $flux;
}


/**
 * Optimiser la base de donnees en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @param int $n
 * @return int
 */
function coordonnees_optimiser_base_disparus($flux) {
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('adresse'=>'*', 'numero'=>'*', 'email'=>'*'), '*');
	return $flux;
}
