<?php
/**
 * Utilisations de pipelines par Date de modification manuelle
 *
 * @plugin     Date de modification manuelle
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Date_modif_manuelle\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Retourne la liste des objets sur lesquels on doit
 * ajouter une gestion de date de modification manuelle
 *
 * @return array Liste de type d'objet
 **/
function date_modif_manuelle_objets_valides() {
	// TODO: Mettre en configuration
	return array('article');
}

/**
 * Ajouter la gestion de notre date au formulaire dater (chargement)
 *
 * @param array $flux
 * @return array $flux
 **/
function date_modif_manuelle_formulaire_charger($flux){
	if ($flux['args']['form'] != 'dater') {
		return $flux;
	}

	$objet    = $flux['data']['objet'];
	$id_objet = $flux['data']['id_objet'];

	if (!in_array($objet, date_modif_manuelle_objets_valides())) {
		return $flux;
	}

	$_id_objet = id_table_objet($objet);
	$table = table_objet($objet);
	$trouver_table = charger_fonction('trouver_table','base');
	$desc = $trouver_table($table);

	if (!$desc) {
		return false;
	}

	if (!isset($desc['field']['date_modif_manuelle'])) {
		return false;
	}

	$date = sql_getfetsel('date_modif_manuelle', $desc['table'], "$_id_objet=" . intval($id_objet));

	$annee = $mois = $jour = $heure = $minute = 0;
	if ($regs = recup_date($date, false)) {
		$annee  = $regs[0];
		$mois   = $regs[1];
		$jour   = $regs[2];
		$heure  = $regs[3];
		$minute = $regs[4];
	}

	// données pour la date
	$flux['data']['_label_date_modif_manuelle']   = _T('date_modif_manuelle:label_date_modif_manuelle');
	$flux['data']['aucune_date_modif_manuelle'] = intval($annee) ? false : true;
	$flux['data']['afficher_date_modif_manuelle'] = $date;

	$date_texte  = dater_formater_saisie_jour($jour, $mois, $annee);
	$heure_texte = "$heure:$minute";
	$flux['data']['date_modif_manuelle_jour']  = ($date_texte == '0000') ? '' : $date_texte;
	$flux['data']['date_modif_manuelle_heure'] = ($heure_texte == '00:00') ? '' : $heure_texte;

	return $flux;
}


/**
 * Ajouter la gestion de notre date au formulaire dater (verifications)
 *
 * @param array $flux
 * @return array $flux
 **/
function date_modif_manuelle_formulaire_verifier($flux) {
	if ($flux['args']['form'] != 'dater'){
		return $flux;
	}

	$objet    = $flux['args']['args'][0];
	$id_objet = $flux['args']['args'][1];

	if (!in_array($objet, date_modif_manuelle_objets_valides())) {
		return $flux;
	}

	$k = 'date_modif_manuelle';

	if ($v = _request($k . "_jour") AND !dater_recuperer_date_saisie($v)) {
		$flux['data'][$k] = _T('format_date_incorrecte');
	}
	elseif ($v=_request($k."_heure") AND !dater_recuperer_heure_saisie($v)) {
		$flux['data'][$k] = _T('format_heure_incorrecte');
	}

	return $flux;
}


/**
 * Ajouter la gestion de notre date au formulaire dater (traitements)
 *
 * @param array $flux
 * @return array $flux
 **/
function date_modif_manuelle_formulaire_traiter($flux) {
	if ($flux['args']['form'] != 'dater'){
		return $flux;
	}

	$objet    = $flux['args']['args'][0];
	$id_objet = $flux['args']['args'][1];

	if (!in_array($objet, date_modif_manuelle_objets_valides())) {
		return $flux;
	}

	if (_request('changer')){
		$_id_objet = id_table_objet($objet);
		$table = table_objet($objet);
		$trouver_table = charger_fonction('trouver_table', 'base');
		$desc = $trouver_table($table);

		if (!$desc)
			return array('message_erreur' => _L('erreur')); #impossible en principe

		$set = array();

		if (!_request('date_modif_manuelle_jour')) {
			$set['date_modif_manuelle'] = sql_format_date(0,0,0,0,0,0);
		} else {
			if (!$d = dater_recuperer_date_saisie(_request('date_modif_manuelle_jour'))) {
				$d = array(date('Y'),date('m'),date('d'));
			}
			if (!$h = dater_recuperer_heure_saisie(_request('date_modif_manuelle_heure'))) {
				$h = array(0,0);
			}
			$set['date_modif_manuelle'] = sql_format_date($d[0], $d[1], $d[2], $h[0], $h[1]);
		}

		include_spip('action/editer_objet');
		objet_modifier($objet, $id_objet, $set);
	}

	set_request('date_modif_manuelle_jour');
	set_request('date_modif_manuelle_heure');

	return $flux;
}




/**
 * Ajouter la gestion de notre date au formulaire dater (vue)
 *
 * @param array $flux
 * @return array $flux
 **/
function date_modif_manuelle_formulaire_fond($flux){
	if ($flux['args']['form'] != 'dater') {
		return $flux;
	}

	$env = $flux['args']['contexte'];

	if (!in_array($env['objet'], date_modif_manuelle_objets_valides())) {
		return $flux;
	}

	if (!$id_objet = $env['id_objet']) {
		return $flux;
	}

	// insertion des saisies HTML
	if (($p = strpos($flux['data'], "<!--extra-->")) !== false){
		$input = recuperer_fond('prive/formulaires/inc-dater-modif_manuelle', $env);
		$flux['data'] = substr_replace($flux['data'], $input, $p, 0);
	}

	return $flux;
}
