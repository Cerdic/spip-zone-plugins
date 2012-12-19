<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Renvoyer le contenu d'une newsletter par son id
 *
 * @param int|string $id
 * @return array|bool
 *   string sujet
 *   string html
 *   string texte
 */
function newsletter_content_dist($id){
	// generer une version a jour (ne fera rien si deja cuite)
	$generer_newsletter = charger_fonction("generer_newsletter","action");
	$generer_newsletter($id);

	// fixer les images et autre
	$fixer_newsletter = charger_fonction("fixer_newsletter","action");
	$fixer_newsletter($id);

	// recuperer les messages
	$corps = sql_fetsel('titre as sujet,html_email as html,texte_email as texte','spip_newsletters','id_newsletter='.intval($id));
	if (!$corps)
		return false;
	if (!$corps['texte'] AND !$corps['html'])
		return false;

	// si pas de version texte : la generer a partir de la version HTML
	if (!$corps['texte']){
		include_spip("inc/newsletters");
		$corps['texte'] = newsletters_html2text($corps['html']);
	}

	return $corps;
}