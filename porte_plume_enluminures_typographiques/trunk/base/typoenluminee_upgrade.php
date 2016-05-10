<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/meta');

/**
 * Installation/maj des tables gis
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function typoenluminee_upgrade($nom_meta_base_version, $version_cible) {
	$current_version = '0.0';
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)) {
		// installation
		if (version_compare($current_version, '0.0', '<=')) {
			maj_titres_enlumines();
			ecrire_meta($nom_meta_base_version, $current_version = $version_cible, 'non');
		}
	}
}

/**
 * Abandon de l'ancienne écriture des intertitres
 *
 * {1{...}1} {2{...}2} {3{...}3} {4{...}4} {5{...}5} en intertitres avec étoiles :
 * {{{...}}} {{{**...}}} {{{***...}}} {{{****...}}} {{{*****...}}}
 *
 */
function maj_titres_enlumines() {
	$anciens_titres = sql_allfetsel('id_article,texte', 'spip_articles', 'texte LIKE "%{1{%" OR texte LIKE "%{2{%" OR texte LIKE "%{3{%" OR texte LIKE "%{4{%" OR texte LIKE "%{5{%"');
	foreach ($anciens_titres as $cle => $article) {
		$id_article = $article['id_article'];
		$article['texte'] = preg_replace('/({1{)(.*)(}1})/Uims', '{{{\\2}}}', $article['texte']);
		$article['texte'] = preg_replace('/({2{)(.*)(}2})/Uims', '{{{**\\2}}}', $article['texte']);
		$article['texte'] = preg_replace('/({3{)(.*)(}3})/Uims', '{{{***\\2}}}', $article['texte']);
		$article['texte'] = preg_replace('/({4{)(.*)(}4})/Uims', '{{{****\\2}}}', $article['texte']);
		$article['texte'] = preg_replace('/({5{)(.*)(}5})/Uims', '{{{*****\\2}}}', $article['texte']);
		$article['texte'] = trim($article['texte']);
		sql_updateq('spip_articles', array('texte' => $article['texte']), 'id_article=' . intval($article['id_article']));
		if (time() >= _UPGRADE_TIME_OUT) {
			return;
		}
	}
}



/**
 * Desinstallation
 *
 * @param string $nom_meta_base_version
 */
function typoenluminee_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	effacer_meta($nom_meta_base_version);
}
