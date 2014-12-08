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
	static $count = null;

	$select = "email,nom,listes,lang,'on' AS status,jeton";
	$where = array('statut='.sql_quote('valide'));
	$limit = "";

	// si pas de liste precisee : liste newsletter par defaut (newsletter::newsletter)
	if (!$listes OR !is_array($listes)){
		$listes = array(mailsubscribers_normaliser_nom_liste());
	}

	// si simple comptage d'une seule liste, faisons plus rapidement pour eviter les regexp sur une grosse base
	// on en profite pour tout compter pour ne le faire qu'une fois
	if (isset($options['count']) AND $options['count'] AND count($listes)==1){
		if (is_null($count)
			AND !_request('var_mode')
		  AND isset($GLOBALS['meta']['newsletter_subscribers_count'])
		  AND $c = unserialize($GLOBALS['meta']['newsletter_subscribers_count']))
			$count = $c;
		if (is_null($count)){
			$rows = sql_allfetsel("listes,count(id_mailsubscriber) as n","spip_mailsubscribers",$where,"listes");
			foreach($rows as $row){
				$ls = explode(",",$row["listes"]);
				$ls = array_filter($ls);
				$ls = array_unique($ls);
				foreach($ls as $l){
					if (!isset($count[$l])) $count[$l] = 0;
					$count[$l] += $row['n'];
				}
			}
			ecrire_meta("newsletter_subscribers_count",serialize($count));
		}
		$liste = reset($listes);
		return (isset($count[$liste])?$count[$liste]:0);
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

	// si simple comptage de plusieurs listes, on arrive ici
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
