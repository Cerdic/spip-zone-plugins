<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/mailsubscribers');
include_spip('mailsubscribers_fonctions');

/**
 * Renvoi les inscrits a une ou plusieurs listes
 * si plusieurs listes sont demandee, c'est un OU qui s'applique (renvoie les inscrits a au moins une des listes)
 *
 * @param array $listes
 *   listes de diffusion. 'newsletter' si non precise
 * @param array $options
 *   count : si true renvoyer le nombre de resultats au lieu de la liste (perf issue, permet de tronconner)
 *   limit : ne recuperer qu'un sous ensemble des inscrits "10,20" pour recuperer 20 resultats a partir du 10e (idem SQL)
 * @return int|array
 *   liste d'utilisateurs, chacun decrit par un array dans le meme format que newsletter/subscriber
 */
function newsletter_subscribers_dist($listes = array(),$options = array()){

	$select = "email,nom,listes,lang,'on' AS status,jeton";
	$where = array('statut='.sql_quote('valide'));
	$limit = "";

	// si pas de liste precisee : liste newsletter par defaut (newsletter::newsletter)
	if (!$listes OR !is_array($listes)){
		$listes = array(mailsubscribers_normaliser_nom_liste());
	}

	$sous_where = array();
	foreach ($listes as $l){
		$l = mailsubscribers_normaliser_nom_liste($l);
		$sous_where[] = "listes REGEXP ".sql_quote('(,|^)'.$l.'(,|$)');
	}
	if (count($sous_where)){
		$sous_where = "(".implode(" OR ",$sous_where).")";
		$where[] = $sous_where;
	}

	// si simple comptage
	if (isset($options['count']) AND $options['count'])
		return sql_countsel("spip_mailsubscribers",$where);

	if (isset($options['limit']) AND $options['limit'])
		$limit = $options['limit'];

	// selection, par date
	// ca permet ainsi que les derniers inscrits (en cours de diffusion) se retrouvent dans le dernier lot
	// et premier inscrits, premiers servis
	$rows = sql_allfetsel($select,"spip_mailsubscribers",$where,"","date",$limit);
	$rows = array_map('mailsubscribers_informe_subscriber',$rows);

	return $rows;
}
