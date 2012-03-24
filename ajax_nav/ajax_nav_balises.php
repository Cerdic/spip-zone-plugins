<?php
include_spip('lib/phpQuery/phpQuery/phpQuery');

function balise_RECUPERER_FOND ($p) {
	$p->code = "calculer_balise_RECUPERER_FOND(@\$Pile[0])";
	return $p;
}

function calculer_balise_RECUPERER_FOND ($env) {
	/* spip_log('DEBUG'); */
	// On cherche le type d'objet correspondant a la page. Si #ENV contient une cle 'page',
	// c'est la bonne valeur.
	if (isset($env['page'])) {
		/* spip_log('page renseignee dans env'); */
		$fond = $env['page'];
		// Sinon, il faut ruser avec les id_objets pour trouver sur quelle page on est.
	} else if (isset($env['id_article'])) {
		/* spip_log('id_article trouve'); */
		$fond = 'article';
		// Si env contient un cle de type id_objet, on teste si objet est dans la liste des pages a
		// ajaxifier. Si oui, c'est que c'est un type d'objet valide. Sinon, ca l'est peut-etre, mais
		// pas moyen de savoir, alors on oublie, c'est de toute facon pas important pour nous,
		// puisqu'on en aura besoin	que pour les pages ajax.
	} else if (isset($GLOBALS['meta']['ajax_nav_config'])) {
		$pages_ajax = unserialize($GLOBALS['meta']['ajax_nav_config']);
		$pages_ajax = preg_split('/ /', $pages_ajax['pagesToAjaxify']);
		foreach ($pages_ajax as $i => $page_ajax) {
			// le cas des articles est deja traite
			if ($page_ajax !== 'article') {
				foreach ($env as $parametre => $valeur) {
					if (preg_replace('/^id_/', '', $parametre) === $page_ajax) {
						/* spip_log('trouve dans la liste des pages ajax'); */
						$fond = $page_ajax;
					}
				}
			}
		}
	}
	// si rien de tout ca n'a fonctionne, on est probablement sur le sommaire, qui a ma connaissance
	// est le seul a ne rien renseigner.
	if ( ! isset($fond) ) {
		// on verifie que vraiment rien n'est renseigne.
		foreach ($env as $parametre => $valeur) {
			if (preg_match('/^id_/', $parametre)) {
				/* spip_log('trouve malgre tout'); */
				$fond = preg_replace('/^id_/', '', $parametre);
			}
		}
		if ( ! isset($fond) ) {
			/* spip_log('rien a faire, c\'est le sommaire'); */
			$fond = 'sommaire';
		}
	}

	return $fond;
}

function balise_EXTRAIRE_INFOS_PAGE ($p) {
  $objet = interprete_argument_balise(1, $p);
  $id_objet = interprete_argument_balise(2, $p);
  $p->code = 'calculer_balise_EXTRAIRE_INFOS_PAGE(' . $objet .', ' . $id_objet . ')';
  return $p;
}

function calculer_balise_EXTRAIRE_INFOS_PAGE($objet, $id_objet) {
  if ($id_objet) {
    $params = array('id_'.$objet => $id_objet);
  } else {
    $params = array();
  }
  $page = recuperer_fond($objet, $params);

  // Ceci devrait marcher, mais en fait non, probablement parce que dans la spec HTML, body
  // ne peut pas avoir d'attribut class...

  //    phpQuery::newDocumentHTML($page);
  //    $bodyClass = pq('body')->attr('class');

  // ...mais comme SPIP utilise les class sur le body par defaut (le vilain!), on fait un
  // truc moche comme ca :
  $bodyClass = preg_replace('/(\r\n|\n|\r)/m', '', $page);
  if (preg_match('/.*<body[^>]class=[\'\"]([^\'\"]*).*/', $bodyClass)) {
    $bodyClass = preg_replace('/.*<body[^>]class=[\'\"]([^\'\"]*).*/', '$1', $bodyClass);
  } else {
    $bodyClass = '';
  }

  phpQuery::newDocumentHTML($page);
  $title = pq('title');
  $title = $title->html();

  $lang = pq('html');
	$lang = $lang->attr('lang');

  $tableau_resultat = serialize(array('body_classes'	=> $bodyClass,
																			'title'		=> $title,
																			'lang'    => $lang,
																			));

  return "$tableau_resultat";
}

function balise_GET_BY_ID ($p) {
  $get_by_id = interprete_argument_balise(1, $p);
  $objet = interprete_argument_balise(2, $p);
  $id_objet = interprete_argument_balise(3, $p);
  $p->code = 'calculer_balise_GET_BY_ID(' . $get_by_id . ', ' . $objet .', ' . $id_objet . ')';
  return $p;
}

function calculer_balise_GET_BY_ID($get_by_id, $objet, $id_objet) {
  if ($id_objet) {
    $params = array('id_'.$objet => $id_objet);
  } else {
    $params = array();
  }
  $page = recuperer_fond($objet, $params);
  phpQuery::newDocumentHTML($page);
  $bloc = pq('#' . $get_by_id);
  $bloc = $bloc->html();

  return "$bloc";
}

?>