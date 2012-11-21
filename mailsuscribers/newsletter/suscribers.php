<?php
/**
 * Plugin mailsuscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/mailsuscribers');
include_spip('mailsuscribers_fonctions');

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
 *   liste d'utiisateurs, chacun decrit par un array dans le meme format que newsletter/suscriber
 */
function newsletter_suscribers_dist($listes = array(),$options = array()){

	$select = "email,nom,listes,lang,'on' AS status,jeton";
	$where = array('statut='.sql_quote('valide'));
	$limit = "";
	if ($listes AND is_array($listes)){
		$sous_where = array();
		foreach ($listes as $l){
			$l = mailsuscribers_normaliser_nom_liste($l);
			$sous_where[] = "listes REGEXP ".sql_quote('(,|^)'.$l.'(,|$)');
		}
		if (count($sous_where)){
			$sous_where = "(".implode(" OR ",$sous_where).")";
			$where[] = $sous_where;
		}
	}

	// si simple comptage
	if (isset($options['count']) AND $options['count'])
		return sql_countsel("spip_mailsuscribers",$where);

	if (isset($options['limit']) AND $options['limit'])
		$limit = $options['limit'];

	// selection, par date
	// ca permet ainsi que les derniers inscrits (en cours de diffusion) se retrouvent dans le dernier lot
	// et premier inscrits, premiers servis
	$rows = sql_allfetsel($select,"spip_mailsuscribers",$where,"","date",$limit);
	$rows = array_map('mailsuscribers_informe_suscriber',$rows);

	return $rows;
}

/**
 * Informer un suscriber : ici juste l'url unsuscribe a calculer
 * @param array $infos
 * @return array mixed
 */
function mailsuscribers_informe_suscriber($infos){
	$infos['listes'] = explode(',',$infos['listes']);
	$infos['url_unsuscribe'] = mailsuscriber_url_unsuscribe($infos['email'],$infos['jeton']);
	unset($infos['jeton']);
	return $infos;
}