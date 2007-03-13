<?php

/*
 * corrige les notes
 * Auteur : fil@rezo.net
 * © 2005-2006 - Distribue sous licence GNU/GPL
 *
 */

function action_corriger_notes() {
	if ($GLOBALS['auteur_session']['statut'] == '0minirezo') {
		include_spip('revision_nbsp');
		$id_article = intval(_request('id_article'));
		$s = spip_query("SELECT texte FROM spip_articles WHERE id_article=$id_article");
		$t=spip_fetch_array($s);
		if ($c = notes_automatiques($t['texte'])) {
			include_spip('inc/modifier');
			modifier_contenu('article', $id_article,
				array(
					'champs' => array('texte')
				),
				array('texte' => $c)
			);
		}
	}

	redirige_par_entete(
		generer_url_ecrire('articles', 'id_article='.$id_article, '&'));
}

?>