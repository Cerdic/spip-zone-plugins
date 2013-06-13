<?php
/*
 * Plugin spip|twitter
 * (c) 2009-2013
 *
 * envoyer et lire des messages de Twitter
 * distribue sous licence GNU/LGPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Affichage du formulaire de microblog
 *
 * @param array $flux
 * @return array
 */
function microblog_affiche_milieu($flux){
	if ($exec = $flux['args']['exec']
		AND // SPIP 3
				((include_spip('base/objets')
				AND function_exists('trouver_objet_exec')
				AND $e = trouver_objet_exec($exec)
				AND $e['type']=='article'
				AND $e['edition']!==true
				AND $id_article = $flux['args']['id_article']
				AND include_spip('inc/config')
				AND $cfg = lire_config('microblog')
		    )
			OR // SPIP 2.x
				($exec=='articles'
				AND $id_article = $flux['args']['id_article']
				AND $cfg = @unserialize($GLOBALS['meta']['microblog'])
				)
			)
		AND
			($cfg['evt_publierarticles'] OR $cfg['evt_proposerarticles'])
		AND $cfg['invite']
		){
		$deplie = false;
		$ids = 'formulaire_editer_microblog-article-' . $id_article;
		include_spip("inc/presentation"); // bouton_block_depliable et al non dispo en SPIP 3 sinon
		$bouton = bouton_block_depliable(_T('twitter:titre_microblog'), $deplie, $ids);
		$out = debut_cadre('e', find_in_path('microblog-24.gif','themes/spip/images/'),'',$bouton, '', '', true);
		$out .= recuperer_fond('prive/editer/microblog', array_merge($_GET, array('objet'=>'article','id_objet'=>$id_article)));
		$out .= fin_cadre();
		if ($p = strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$out,$p,0);
		else
			$flux['data'] .= $out;
	}

	return $flux;
}