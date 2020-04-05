<?php

/**
 * Créer un article à partir d'un fichier au format odt
 *
 * @author cy_altern
 * @license GNU/LGPL
 *
 * @package plugins
 * @subpackage odt2spip
 * @category import
 *
 * @version $Id$
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Création de l'article et redirection vers celui-ci
 *
 * Le fichier .odt est envoyé par un formulaire, ainsi que des informations sur
 * la rubrique dans laquelle créer l'article, un flag qui indique s'il faut joindre
 * le document à l'article créé, etc..
 * Cette fonction s'assure avant tout que l'utilisateur peut y ajouter un article.
 * Le fichier .odt est traité et transformé en article.
 * En fin de traitement, on est redirigé vers l'article qui vient d'être créé.
 *
 * {@internal
 * Un répertoire temporaire, spécifique à l'utilisateur en cours, est utilisé et
 * créé si nécessaire. Il est supprimé en fin de traitement.
 * Le format odt correspond à une archive .zip, et regroupe le contenu dans un fichier
 * content.xml : ce fichier est transformé par XSLT afin de générer un texte
 * utilisant les balises SPIP pour sa mise en forme.
 * }}
 *
 */
function action_odt2spip_importe($fichier = null, $arg = null) {
	if (is_null($arg)) {
		$arg = _request('arg');
	}

	// arg : id_rubrique=XXX ou id_article=YYY
	$id_article = $id_rubrique = false;
	list($objet, $id_objet) = explode('=', $arg);
	if ($objet === 'id_rubrique') {
		$id_rubrique = intval($id_objet);
		$objet = 'rubrique';
		$creer_objet = 'article';
	} else {
		$id_article = intval($id_objet);
		$objet = 'article';
		$creer_objet = false;
	}

	include_spip('inc/securiser_action');

	if (
		($id_rubrique and !autoriser('creerarticledans', 'rubrique', $id_rubrique))
		or ($id_article and !autoriser('modifier', 'article', $id_article))
	) {
		die(_T('avis_non_acces_page'));
	}

	include_spip('inc/odt2spip');
	try {
		$fichier = odt2spip_deplacer_fichier_upload('fichier_odt');
	} catch (\Exception $e) {
		die();
	}

	list($id, $erreurs) = odt2spip_integrer_fichier(
		$fichier,
		$objet,
		$id_objet,
		$creer_objet,
		array(
			'attacher_fichier' => _request('attacher_odt'),
		)
	);

	if (!$id) {
		die($erreurs);
	}

	if (is_null(_request('redirect'))) {
		include_spip('inc/headers');
		redirige_par_entete(generer_url_entite($id, $creer_objet ? $creer_objet : $objet));
	}
}
