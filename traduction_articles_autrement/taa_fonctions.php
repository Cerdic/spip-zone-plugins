<?php

// http://doc.spip.org/@puce_statut_article_dist
function puce_statut_article($id, $statut, $id_rubrique, $type='article', $ajax = false) {
	global $lang_objet;
	
	static $coord = array('publie' => 2,
			      'prepa' => 0,
			      'prop' => 1,
			      'refuse' => 3,
			      'poubelle' => 4);

	$lang_dir = lang_dir($lang_objet);
	if (!$id) {
	  $id = $id_rubrique;
	  $ajax_node ='';
	} else	$ajax_node = " id='imgstatut$type$id'";


	$inser_puce = puce_stat($statut, " width='9' height='9' style='margin: 1px;'$ajax_node");

	if (!autoriser('publierdans', 'rubrique', $id_rubrique)
	OR !_ACTIVER_PUCE_RAPIDE)
		return $inser_puce;

	$titles = array(
			  "blanche" => _T('texte_statut_en_cours_redaction'),
			  "orange" => _T('texte_statut_propose_evaluation'),
			  "verte" => _T('texte_statut_publie'),
			  "rouge" => _T('texte_statut_refuse'),
			  "poubelle" => _T('texte_statut_poubelle'));

	$clip = 1+ (11*$coord[$statut]);

	if ($ajax){
		return 	"<span class='puce_article_fixe'>"
		. $inser_puce
		. "</span>"
		. "<span class='puce_article_popup' id='statutdecal$type$id' style='margin-left: -$clip"."px;'>"
		  . afficher_script_statut($id, $type, -1, 'puce-blanche.gif', 'prepa', $titles['blanche'])
		  . afficher_script_statut($id, $type, -12, 'puce-orange.gif', 'prop', $titles['orange'])
		  . afficher_script_statut($id, $type, -23, 'puce-verte.gif', 'publie', $titles['verte'])
		  . afficher_script_statut($id, $type, -34, 'puce-rouge.gif', 'refuse', $titles['rouge'])
		  . afficher_script_statut($id, $type, -45, 'puce-poubelle.gif', 'poubelle', $titles['poubelle'])
		  . "</span>";
	}

	$nom = "puce_statut_";

	if ((! _SPIP_AJAX) AND $type != 'article') 
	  $over ='';
	else {

	  $action = generer_url_ecrire('puce_statut',"",true);
	  $action = "if (!this.puce_loaded) { this.puce_loaded = true; prepare_selec_statut('$nom', '$type', '$id', '$action'); }";
	  $over = "\nonmouseover=\"$action\"";
	}

	return 	"<span class='puce_article' id='$nom$type$id' dir='$lang_dir'$over>"
	. $inser_puce
	. '</span>';
}

function puce_stat($statut, $atts='') {
	switch ($statut) {
		case 'publie':
			$img = 'puce-verte.gif';
			$alt = _T('info_article_publie');
			return http_img_pack($img, $alt, $atts);
		case 'prepa':
			$img = 'puce-blanche.gif';
			$alt = _T('info_article_redaction');
			return http_img_pack($img, $alt, $atts);
		case 'prop':
			$img = 'puce-orange.gif';
			$alt = _T('info_article_propose');
			return http_img_pack($img, $alt, $atts);
		case 'refuse':
			$img = 'puce-rouge.gif';
			$alt = _T('info_article_refuse');
			return http_img_pack($img, $alt, $atts);
		case 'poubelle':
			$img = 'puce-poubelle.gif';
			$alt = _T('info_article_supprime');
			return http_img_pack($img, $alt, $atts);
	}
	return http_img_pack($img, $alt, $atts);
}


// Provisoire, en attendant la solution des surcharge du plugin afficher objet
function filtre_test_syndic_article_miroir($id){
	global $my_sites;
	if (isset($my_sites[$id]['miroir']) AND $my_sites[$id]['miroir'] == 'oui')
		return ' ';
	return '';
}


/**
 * {where}
 * tout simplement
 *
 * @param <type> $idb
 * @param <type> $boucles
 * @param <type> $crit
 */
function critere_where_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if (isset($crit->param[0]))
		$_where = calculer_liste($crit->param[0], array(), $boucles, $boucle->id_parent);
	else
		$_where = '@$Pile[0]["where"]';

	if ($crit->cond)
		$_where = "(($_where) ? ($_where) : '')";

	if ($crit->not)
		$_where = "array('NOT',$_where)";

	$boucle->where[]= $_where;
}

/**
 * Compter les articles publies lies a un auteur, dans une boucle auteurs
 * pour la vue prive/liste/auteurs.html
 * 
 * @param <type> $idb
 * @param <type> $boucles
 * @param <type> $crit
 * @param <type> $left
 */
function critere_compteur_articles_filtres_dist($idb, &$boucles, $crit, $left=false){
	$boucle = &$boucles[$idb];

	$_statut = calculer_liste($crit->param[0], array(), $boucles, $boucle->id_parent);

	$not="";
	if ($crit->not)
		$not=", 'NOT'";
	$boucle->from['auteurs_articles'] = 'spip_auteurs_articles';
	$boucle->from_type['auteurs_articles'] = 'left';
	$boucle->join['auteurs_articles'] = array("'auteurs'","'id_auteur'");

	$boucle->from['articles'] = 'spip_articles';
	$boucle->from_type['articles'] = 'left';
	$boucle->join['articles'] = array("'auteurs_articles'","'id_article'","'id_article'","'(articles.statut IS NULL OR '.sql_in('articles.statut',sql_quote($_statut)$not).')'");

	$boucle->select[]= "COUNT(articles.id_article) AS compteur_articles";
	$boucle->group[] = 'auteurs.id_auteur';
}
/**
 * Compter les articles publies lies a un auteur, dans une boucle auteurs
 * pour la vue prive/liste/auteurs.html
 *
 * @param <type> $p
 * @return <type>
 */
function balise_COMPTEUR_ARTICLES_dist($p) {
	return rindex_pile($p, 'compteur_articles', 'compteur_articles_filtres');
}


/**
 * Calculer l'initiale d'un nom
 * 
 * @param <type> $nom
 * @return <type>
 */
function initiale($nom){
	return spip_substr(trim(strtoupper($nom)),0,1);
}

/**
 * Afficher l'initiale pour la navigation par lettres
 * 
 * @staticvar string $memo
 * @param <type> $url
 * @param <type> $initiale
 * @param <type> $compteur
 * @param <type> $debut
 * @param <type> $pas
 * @return <type>
 */
function afficher_initiale($url,$initiale,$compteur,$debut,$pas){
	static $memo = null;
	$res = '';
	if (!$memo 
		OR (!$initiale AND !$url)
		OR ($initiale!==$memo['initiale'])
		){
		$newcompt = intval(floor(($compteur-1)/$pas)*$pas);
		#var_dump("$initiale:$newcompt");
		if ($memo){
			$on = (($memo['compteur']<=$debut)
				AND (
						$newcompt>$debut OR ($newcompt==$debut AND $newcompt==$memo['compteur'])
						));
			$res = lien_ou_expose($memo['url'],$memo['initiale'],$on,'lien_pagination');
		}
		if ($initiale)
			$memo = array('initiale'=>$initiale,'url'=>parametre_url($url,'i',$initiale),'compteur'=>$newcompt);
	}
	return $res;
}

/**
 * Calculer l'url vers la messagerie :
 * - si l'auteur accepte les messages internes et que la messagerie est activee
 * et qu'il est en ligne, on propose le lien vers la messagerie interne
 * - sinon on propose un lien vers un email si possible
 * - sinon rien
 *
 * @staticvar string $time
 * @param int $id_auteur
 * @param date $en_ligne
 * @param string $statut
 * @param string $imessage
 * @param string $email
 * @return string
 */
function auteur_lien_messagerie($id_auteur,$en_ligne,$statut,$imessage,$email){
	static $time = null;
	if (!in_array($statut, array('0minirezo', '1comite')))
		return '';

	if (is_null($time))
		$time = time();
	$parti = (($time-strtotime($en_ligne))>15*60);

	if ($imessage != 'non' AND !$parti AND $GLOBALS['meta']['messagerie_agenda'] != 'non')
		return generer_action_auteur("editer_message","normal/$id_auteur");
	
	elseif (strlen($email) AND autoriser('voir', 'auteur', $id_auteur))
		return 'mailto:' . $email;

	else
		return '';

	return "<a href='$href' title=\""
	  .  _T('info_envoyer_message_prive')
	  . "\" class='message'>&nbsp;</a>";
}
?>