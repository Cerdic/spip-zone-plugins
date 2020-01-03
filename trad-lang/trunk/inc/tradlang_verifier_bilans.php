<?php
/**
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 *
 * @package SPIP\Tradlang\
 */

if (!defined('_ECRIRE_INC_VERSION')){
	return;
}

/**
 * Fonction de vérification de la concordance des bilans de chaque langue d'un module
 *
 * @param int $id_tradlang_module
 *    Le nom du module
 * @param string $langue_mere
 *    La langue mère du module
 * @param bool $invalider
 */
function inc_tradlang_verifier_bilans_dist($id_tradlang_module, $langue_mere, $invalider = true){

	/**
	 * Quelle est le total de la langue mère
	 */
	$total = sql_countsel('spip_tradlangs', 'id_tradlang_module=' . intval($id_tradlang_module) . ' AND lang=' . sql_quote($langue_mere) . " AND statut='OK'");

	/**
	 * Les infos du module
	 */
	$row_module = sql_fetsel('*', 'spip_tradlang_modules', 'id_tradlang_module=' . intval($id_tradlang_module));
	$module = $row_module['module'];

	/**
	 * Les différentes langues du module
	 */
	$langues = sql_allfetsel('DISTINCT lang', 'spip_tradlangs', 'id_tradlang_module=' . intval($id_tradlang_module));
	$langues = array_column($langues, 'lang');

	$tradlang_verifier_langue_base = charger_fonction('tradlang_verifier_langue_base', 'inc');
	/**
	 * Vérification de chaque langue
	 */
	foreach ($langues as $lang){
		$bilan = false;
		$where = 'id_tradlang_module=' . intval($id_tradlang_module) . ' AND lang = ' . sql_quote($lang);

		$nbs = array();
		foreach (['OK', 'RELIRE', 'MODIF', 'NEW'] as $s){
			$nbs[$s] = sql_countsel('spip_tradlangs', "$where AND statut=" . sql_quote($s));
		}
		$total_lang = array_sum($nbs);

		// si les totaux ne matchent pas, reverifier tout
		if ($total_lang!=$total){
			if ($total_lang>$total){
				spip_log("La langue $lang du mondule $module (#$id_tradlang_module) a trop de chaines $total_lang vs " . json_encode($nbs), 'bilan.' . _LOG_ERREUR);
			} elseif ($total_lang<$total) {
				spip_log("La langue $lang du mondule $module (#$id_tradlang_module) n'a pas assez de chaines $total_lang vs " . json_encode($nbs), 'bilan.' . _LOG_ERREUR);
			}

			$tradlang_verifier_langue_base($module, $lang);
			foreach (['OK', 'RELIRE', 'MODIF', 'NEW'] as $s){
				$nbs[$s] = sql_countsel('spip_tradlangs', "$where AND statut=" . sql_quote($s));
			}
		}

		$infos_bilan = array(
			'id_tradlang_module' => $id_tradlang_module,
			'module' => $module,
			'lang' => $lang,
			'chaines_total' => $total,
			'chaines_ok' => $nbs['OK'],
			'chaines_relire' => $nbs['RELIRE'],
			'chaines_modif' => $nbs['MODIF'],
			'chaines_new' => $nbs['NEW']
		);

		if (sql_countsel('spip_tradlangs_bilans', 'id_tradlang_module=' . intval($id_tradlang_module) . ' AND lang=' . sql_quote($lang))) {
			sql_updateq('spip_tradlangs_bilans', $infos_bilan, 'id_tradlang_module=' . intval($id_tradlang_module) . ' AND lang=' . sql_quote($lang));
		}
		else {
			sql_insertq('spip_tradlangs_bilans', $infos_bilan);
		}
	}

	if ($invalider){
		include_spip('inc/invalideur');
		suivre_invalideur('1');
	}
}
