<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et MAJ plugin
 *
 * @param string $nom_meta_base_version
 * 	Nom de la meta d'installation du plugin
 * @param float $version_cible
 * 	Version vers laquelle mettre à jour
 */
function typoenluminee_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_titres_enlumines',array())
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Abandon de l'ancienne écriture des intertitres
 *
 * {1{...}1} {2{...}2} {3{...}3} {4{...}4} {5{...}5} en intertitres avec étoiles :
 * {{{...}}} {{{**...}}} {{{***...}}} {{{****...}}} {{{*****...}}}
 *
 */
function maj_titres_enlumines() {
	$anciens_titres = sql_allfetsel('id_article, texte', 'spip_articles', 'texte LIKE "%{1{%" OR texte LIKE "%{2{%" OR texte LIKE "%{3{%" OR texte LIKE "%{4{%" OR texte LIKE "%{5{%"');
	foreach ($anciens_titres as $cle => $article) {
		$id_article = $article['id_article'];
		$article['texte'] = preg_replace('/({1{)(.*)(}1})/Uims', '{{{\\2}}}', $article['texte']);
		$article['texte'] = preg_replace('/({2{)(.*)(}2})/Uims', '{{{**\\2}}}', $article['texte']);
		$article['texte'] = preg_replace('/({3{)(.*)(}3})/Uims', '{{{***\\2}}}', $article['texte']);
		$article['texte'] = preg_replace('/({4{)(.*)(}4})/Uims', '{{{****\\2}}}', $article['texte']);
		$article['texte'] = preg_replace('/({5{)(.*)(}5})/Uims', '{{{*****\\2}}}', $article['texte']);
		$article['texte'] = trim($article['texte']);
		sql_updateq('spip_articles', array('texte' => $article['texte']), 'id_article='.intval($article['id_article']));
		if (time() >= _TIME_OUT) {
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
