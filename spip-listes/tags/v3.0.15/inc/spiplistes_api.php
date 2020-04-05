<?php
/**
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/

if(!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/utils');
include_spip('inc/filtres');    /* email_valide() */
include_spip('inc/acces');      /* creer_uniqid() */
include_spip('inc/charsets');
include_spip('inc/mail');

include_spip('base/abstract_sql');

//include_spip('inc/spiplistes_api_abstract_sql'); // obsolete
include_spip('inc/spiplistes_api_globales');

/**
 * Fonction de compatibilite de php4 / php5 pour http_build_query
 * 
 * @param mixed $data un array de contexte
 * @param string $prefix
 * @param string $sep
 * @param int $key
 * @return un query string a passer a une url 
 */
function spiplistes_http_build_query($data,$prefix=null,$sep='',$key='')
{
	if(!function_exists('http_build_query')) {
	    function http_build_query($data,$prefix=null,$sep='',$key='') {
	        $ret = array();
	            foreach((array)$data as $k => $v) {
	                $k    = urlencode($k);
	                if(is_int($k) && $prefix != null) {
	                    $k    = $prefix.$k;
	                };
	                if(!empty($key)) {
	                    $k    = $key."[".$k."]";
	                };
	
	                if(is_array($v) || is_object($v)) {
	                    array_push($ret,http_build_query($v,"",$sep,$k));
	                }
	                else {
	                    array_push($ret,$k."=".urlencode($v));
	                };
	            };
	
	        if(empty($sep)) {
	            $sep = ini_get("arg_separator.output");
	        };
	        return implode($sep, $ret);
	    };
	};
	return http_build_query($data);
}

/**
 * function privee
 * multi_queries mysql n'est pas en mesure de le faire en natif :-(
 * A tranformer le jour ou mysql gerera correctement le multi_query
 * Et a transformer en transanction quand spip utilisera innodb ou autre table transactionnelle
 * @param $queries : requetes separees par des ';'
 */
function __exec_multi_queries($queries) {
	$queries = trim($queries);
	if (substr($queries, -1, 1) == ';') {
		$queries = substr($queries, 0, strlen($queries)-1);
	}
	$_queries = explode(';', $queries);
	while( list(,$val) = each($_queries)) {
		$res = spip_query($val);
	}
	return $res;
}

/**
 * @version CP-20080508
 * renvoie OK ou ERR entre crochet
 * sert principalement pour les log
 * @return string
 */
function spiplistes_str_ok_error ($statut) {
	return("[".(($statut != false) ? "OK" : "ERR")."]");
}

//
function spiplistes_singulier_pluriel_str_get ($var, $str_sing, $str_plur, $returnvar = true) {
	$result = "";
	if($var) {
		$result = (($returnvar) ? $var : "") . " " . (($var > 1) ? $str_plur : $str_sing);
	}
	return($result);
}

/**
 * @version CP-20080508
 * @return int
 */
function spiplistes_sql_compter ($table, $sql_whereq) {
	$sql_result = intval(sql_countsel($table, $sql_whereq));
	return($sql_result);
}

/**
 * @version CP-20080511
 */
function spiplistes_courriers_statut_compter ($statut='tous') {
	$sql_where = spiplistes_listes_sql_where_or(
		($statut == 'tous')
		? _SPIPLISTES_COURRIERS_STATUTS
		: $statut
		);
	return(spiplistes_sql_compter('spip_courriers', $sql_where));
}


/**
 * Date/time du serveur SQL
 * @version CP-20091207
 * @return string|bool
 */
function spiplistes_sql_now ()
{
	if($result = sql_fetsel('NOW() as maintenant'))
	{
		$result = $result['maintenant'];
	}
	return($result);
}

/**
 * @version CP-20080510
 */
function spiplistes_courriers_casier_premier ($sql_select, $sql_whereq) {
	return(sql_select(
			$sql_select, "spip_courriers", $sql_whereq." LIMIT 1"
		)
	);
}

/**
 ******************************************************************************
	Les fonctions spiplistes_abonnements_*() concernent les abonnements
	
	Table cible : spip_auteurs_listes
	
 ******************************************************************************
 */


/**
 * Abonner un id_auteur a une id_liste
 * ou une liste de listes ($id_liste est un tableau de (id)listes)
 * avec la date d'inscription
 * @version CP-20110619 20110822
 * @param int $id_auteur
 * @param int|array $id_listes
 * @return array id_listes ajoutees
 */
function spiplistes_abonnements_ajouter ($id_auteur, $id_listes, $statut = FALSE) {
	
	if(($id_auteur = intval($id_auteur)) > 0)
	{
		$sql_table = 'spip_auteurs_listes';
		$sql_noms = '(id_auteur,id_liste,date_inscription)';
		/**
		 * Note les abos de cet auteur
		 */
		$curr_abos = spiplistes_abonnements_listes_auteur($id_auteur);
		$real_id_listes = array();
		$sql_valeurs = array();
		
		if (!is_array($id_listes)) $id_listes = array($id_listes);
		
		if (is_array($id_listes))
		{
			foreach ($id_listes as $id) {
				if (
				   (($id = intval($id)) > 0)
				   // si pas encore abonne'
					&& (!$curr_abos || !in_array($id, $curr_abos)))
				{
					$sql_valeurs[] = '('.$id_auteur.','.$id.',NOW())';
					$real_id_listes[] = $id;
				}
			}
			if (count($sql_valeurs))
			{
				$sql_valeurs = implode(',', $sql_valeurs);
			
				if (sql_insert($sql_table, $sql_noms, $sql_valeurs) === false)
				{
					spiplistes_sqlerror_log ('spiplistes_abonnements_ajouter()');
				}
				else {
					spiplistes_log_api ('SUBSCRIBE id_auteur #'
									   . $id_auteur
									   . ' to id_liste '
									   . "#" . implode(',#', $real_id_listes)
									   );
				}
				/**
				 * Si statut, modifier pour tous les abonnements de ce compte.
				 * Non liée à la requette précédente pour raison historique.
				 */
				if ($statut && ($statut == 'a_valider' || $statut == 'valide'))
				{
					if (sql_updateq ($sql_table,
									array('statut' => $statut),
									'id_auteur='.$id_auteur) === FALSE)
					{
						spiplistes_sqlerror_log ('spiplistes_abonnements_ajouter()');
					}
					else {
						spiplistes_log_api ('UPDATE statut id_auteur #'
										   . $id_auteur
										   . ' to ' . $statut
										   );
					}
				}
				
			}
		}
	}
	return($real_id_listes);
} // spiplistes_abonnements_ajouter()

/**
 * desabonner des listes
 * @version CP-20071016
 */
function spiplistes_abonnements_desabonner_statut ($id_auteur, $listes_statuts) {
	if(($id_auteur = intval($id_auteur)) && count($listes_statuts)) {
		$sql_where = "statut=".implode(" OR statut=", array_map("sql_quote", $listes_statuts));
		$sql_result = sql_select("id_liste", "spip_listes", $sql_where);
		$listes = array();
		while($row = sql_fetch($sql_result)) {
			$listes[] = intval($row['id_liste']);
		}
		if(count($listes)) {
			$sql_where = " id_auteur=".sql_quote($id_auteur)." AND id_liste IN (" . implode(",", $listes) . ")";
			sql_delete("spip_auteurs_listes", $sql_where);
		}
		return ( spiplistes_format_abo_modifier($id_auteur) );
	}
	return(false);
}


/**
 * supprimer des abonnes de la table des abonnements
 * @version CP-20080512
 */
function spiplistes_abonnements_auteurs_supprimer ($auteur_statut) {
	$auteur_statut = "statut=".sql_quote($auteur_statut);
		// Sur les precieux conseils de MM :
		// passer la requete en 2 etapes pour assurer portabilite sql
		$selection =
			sql_select("id_auteur", "spip_auteurs", $auteur_statut,'','','','','',false);
		$sql_result = sql_delete("spip_auteurs_listes", "id_auteur IN ($selection)");
		if ($sql_result === false) {
			spiplistes_sqlerror_log("abonnements_auteurs_supprimer");
		}
	return($result);
}

/**
 * renvoie la liste des abonnements pour id_auteur
 * @version CP-20080330
 * @param int $id_auteur
 * @param bool $avec_titre
 * @return tableau d'id_listes. La valeur est titre si $avec_titre true, sinon id_liste
 */
function spiplistes_abonnements_listes_auteur ($id_auteur, $avec_titre = false) {
	$result = array();
	$sql_select = array("abo.id_liste");
	$sql_from = array("spip_auteurs_listes AS abo");
	$sql_where = array();
	if($avec_titre) {
		$sql_select[] = "list.titre";
		$sql_from[] = "spip_listes AS list";
		$sql_where[] = "abo.id_liste=list.id_liste";
	}
	$sql_where[] = "abo.id_auteur=".sql_quote($id_auteur);
	$sql_result = sql_select (
		$sql_select
		, $sql_from
		, $sql_where
		);
	if ($sql_result === false) {
		spiplistes_sqlerror_log("spiplistes_abonnements_listes_auteur");
	}
	else {
		while ($row = sql_fetch($sql_result)) {
			$result[$row['id_liste']] = ($avec_titre ? $row['titre'] : $row['id_liste']);
		}
	}
	return($result);
}

/**
 * Desabonner un id_auteur si int
 * ou d'une série d'auteurs si array
 * ou tous les auteurs si 'tous'
 * d'une id_liste si int
 * ou des listes indiqués si array
 * ou de toutes les listes si 'toutes'
 * 
 * @version CP-20090410 20110822
 * @param int|string|array $id_auteur
 * @param int|string|array $id_liste
 * @return bool
 */
function spiplistes_abonnements_auteur_desabonner ($id_auteur, $id_liste = FALSE)
{
	//spiplistes_debug_log("spiplistes_abonnements_auteur_desabonner ($id_auteur, $id_liste)");
	
	$result = FALSE;
	$msg1 = $msg2 = '';
	$sql_where = array();
	
	if($id_auteur == 'tous') {
		$sql_where[] = 'id_auteur>0';
		$msg1 = $id_auteur;
	}
	else if(is_array($id_auteur)) {
		$ii = implode(',', $id_auteur);
		$sql_where[] = 'id_auteur IN ('.$ii.')';
		$msg1 = 'id_auteur #'.$ii;
	}
	else if(($id_auteur = intval($id_auteur)) > 0) {
		$sql_where[] = 'id_auteur='.sql_quote($id_auteur);
		$msg1 = 'id_auteur #'.$id_auteur;
	}
	
	if(count($sql_where) && $id_liste)
	{
		$sql_table = 'spip_auteurs_listes';
		
		if($id_liste == 'toutes')
		{
			$msg2 = 'de toutes les listes';
		}
		else if (is_array ($id_liste) && count($id_liste))
		{
			$ids = implode(',', $id_liste);
			$sql_where[] = 'id_liste IN ('.$ids.')';
			$msg2 = 'des listes #'.$ids;
		}
		else if(($id_liste = intval($id_liste)) > 0)
		{
			$sql_where[] = 'id_liste='.$id_liste;
			$msg2 = 'de la liste #'.$id_liste;
		}
		if(($result = sql_delete($sql_table, $sql_where)) === FALSE)
		{
			spiplistes_log ('ERR sql_delete: abonnements_auteur_desabonner()');
		}
		else {
			spiplistes_log_api('UNSUBSCRIBE '.$msg1.' '.$msg2);
		}
	}
	return ($result);
}

/**
 * supprimer des abonnements
 * @version CP-20080512
 */
function spiplistes_abonnements_supprimer ($sql_whereq) {
	return(sql_delete('spip_auteurs_listes', $sql_whereq));
}

/**
 * dans la table des abonnements
 * @version CP-20080508
 * @return int
 */
function spiplistes_abonnements_compter ($sql_whereq = "") {
	return(spiplistes_sql_compter("spip_auteurs_listes", $sql_whereq));
}

/**
 * Donne le nombre d'abonnés (pour une liste)
 * qui ont un vrai format de réception (html ou texte)
 * et une adresse mail valide
 * @version CP-20081228
 * @return int
 */
function spiplistes_abonnements_vrais_compter ($id_liste) {
	if($id_liste = intval($id_liste)) {
		// SELECT COUNT(l.id_auteur) AS nb
		// FROM spip_auteurs_listes AS l, spip_auteurs_elargis AS f, spip_auteurs AS a
		// WHERE l.id_liste=$id_liste
		//	AND l.id_auteur=f.id_auteur 
		//	AND (l.id_auteur=a.id_auteur AND LENGTH(a.email) > 3)
		//	AND (f.`spip_listes_format`='html' OR f.`spip_listes_format`='texte')
		$sql_select = array('COUNT(l.id_auteur) AS nb');
		$sql_from = array('spip_auteurs_listes AS l', 'spip_auteurs_elargis AS f', 'spip_auteurs AS a');
		$sql_where = array(
			"l.id_liste=$id_liste"
			, "l.id_auteur=f.id_auteur"
			, "(l.id_auteur=a.id_auteur AND LENGTH(a.email) > 3)"
			, "(f.`spip_listes_format`='html' OR f.`spip_listes_format`='texte')"
			);
		$sql_result = sql_select($sql_select, $sql_from, $sql_where);
		if($sql_result === false) {
			spiplistes_sqlerror_log("spiplistes_abonnements_vrais_compter()");
		}
		if($row = sql_fetch($sql_result)) {
			$result = $row['nb'];
		}
	}
	return($result);
}

/**
 * Inventaire des abonnements.
 * Tableau dont l'index est l'ID de la liste
 * et la valeur un tableau des ID abonnés
 * @param string|array $sql_where
 * @version CP-20110510
 * @return array|bool
 */
function spiplistes_abonnements_lister ($sql_where = '') {
	
	if(($sql_result = sql_select('id_auteur,id_liste'
								, 'spip_auteurs_listes'
								, $sql_where
								)
		) !== FALSE)
	{
		$listes = array();
		
		while($row = sql_fetch($sql_result))
		{
			$ii = $row['id_liste'];
			
			if(!isset($listes[$ii]))
			{
				$listes[$ii] = array();
			}
			$listes[$ii][] = $row['id_auteur'];
		}
		return ($listes);
	}
	else {
		spiplistes_sqlerror_log('spiplistes_abonnements_lister ()');
	}
	return (FALSE);
}

/**
 ******************************************************************************
	Les fonctions spiplistes_listes_*() concernent les listes
	
	Table cible : spip_listes
	
 ******************************************************************************
 */

/**
 * dans la table des listes
 * @version CP-20080508
 */
function spiplistes_listes_compter ($sql_whereq = "") {
	return(spiplistes_sql_compter("spip_listes", $sql_whereq));
}

/**
 * @version CP-20080501
 */
function spiplistes_listes_liste_modifier ($id_liste, $array_set) {
	return(
		sql_update(
			'spip_listes'
			, $array_set
			, "id_liste=".sql_quote($id_liste)." LIMIT 1"
		)
	);
}

/**
 * @version CP-20080501
 */
function spiplistes_listes_liste_supprimer ($id_liste) {
	$sql_where = "id_liste=".sql_quote(intval($id_liste));
	return(
		sql_delete('spip_listes', $sql_where." LIMIT 1")
		&& spiplistes_mod_listes_supprimer("tous", $id_liste)
		&& sql_delete('spip_auteurs_listes', $sql_where)
	);
}

/**
 * @version CP-20080512
 */
function spiplistes_listes_liste_creer ($statut, $lang, $titre, $texte, $pied_page) {
	global $connect_id_auteur;

	if($id_liste = sql_insertq('spip_listes', array(
			  'statut' => $statut
			, 'lang' => $lang
			, 'titre' => $titre
			, 'texte' => $texte
			, 'pied_page' => $pied_page
			)
		)
	) { 
		$id_liste = intval($id_liste);
		$id_auteur = intval($connect_id_auteur);
		spiplistes_mod_listes_supprimer("tous", $id_liste);
		spiplistes_mod_listes_ajouter($id_auteur, $id_liste);
		spiplistes_abonnements_auteur_desabonner("tous", $id_liste);
		spiplistes_abonnements_ajouter($id_auteur, $id_liste);
		return($id_liste);
	}
	return(false);
}

/**
 * Renvoie tableau de id_auteurs abonnes a une liste
 * 	ou FALSE si erreur
 * 
 * @version CP-20080602 20110824
 * @param int $id_liste
 * @param string $sql_where quoted condition
 * @return bool|array
 */
function spiplistes_listes_liste_abo_ids ($id_liste, $sql_where = '') {
	
	$ids_abos = array();
	$sql_where = 'id_liste='.sql_quote($id_liste)
		. ($sql_where ? ' AND '.$sql_where : '');

	if (($sql_result = sql_select('id_auteur',
							 'spip_auteurs_listes',
							 $sql_where,
							 '',
							 array('id_auteur'))) === FALSE)
	{
		spiplistes_sqlerror_log ('spiplistes_listes_liste_abo_ids ()');
		return (FALSE);
	}
	else
	{
		while ($row = sql_fetch($sql_result)) {
			$ids_abos[] = intval($row['id_auteur']);
		}
	}
	return ($ids_abos);
}

/**
 * Compter les abonnes.
 * @param $id_liste int. Si > 0, abonnes a cette liste,
 * 	sinon, nombre total d'abonnements (nb lignes dans la table)
 * @param $preciser. Si true, renvoie tableau total et formats
 * @return int|array
 */
function spiplistes_listes_nb_abonnes_compter ($id_liste = 0, $preciser = false)
{
	$id_liste = intval($id_liste);
	$sql_whereq = (
				   ($id_liste > 0)
				   ? 'id_liste='.sql_quote($id_liste)
				   : ''
				   );
	$total = spiplistes_sql_compter('spip_auteurs_listes', $sql_whereq);
	
	if($preciser)
	{
		$selection = sql_select('id_auteur', 'spip_auteurs_listes', $sql_whereq,'','','','','',false);
		$sql_result = sql_select(
			"`spip_listes_format` AS f, COUNT(*) AS n"
			, 'spip_auteurs_elargis'
			, 'id_auteur IN ('.$selection.')'
			, "`spip_listes_format`"
		);
		if($sql_result === false)
		{
			spiplistes_sqlerror_log('listes_nb_abonnes_compter');
		}
		$formats = array('html' => 0, 'texte' => 0);
		$keys = array_keys($formats);
		while($row = sql_fetch($sql_result))
		{
			if(in_array($row['f'], $keys)) {
				$formats[$row['f']] += $row['n'];
			}
		}
		return(array($total, $formats['html'], $formats['texte']));
	}
	return($total);
}

/**
 * renvoie email emetteur d'une liste
 * @version CP-20111021
 */
function spiplistes_listes_email_emetteur ($id_liste = 0) {
	$id_liste = intval($id_liste);
	if($id_liste > 0) {
		$result = 
			sql_getfetsel(
				"email_envoi"
				, "spip_listes"
				, "id_liste=".sql_quote($id_liste)." LIMIT 1"
			);
		if($result === false) {
			spiplistes_sqlerror_log("listes_email_emetteur");
		}
	}
	return($result);
}

/**
 * @version CP-20080511
 */
function spiplistes_listes_liste_fetsel ($id_liste, $keys = "*") {
	$id_liste = intval($id_liste);
	return(sql_fetsel($keys, "spip_listes", "id_liste=".sql_quote($id_liste)." LIMIT 1"));
}

/**
 * @version CP-20081116
 */
function spiplistes_listes_liste_statut ($id_liste) {
	return(spiplistes_listes_liste_fetsel($id_liste, 'statut'));
}

/**
 * renvoie array sql_where des listes publiees
 * @version CP-20080505
 */
function spiplistes_listes_sql_where_or ($listes) {
	return("statut=".implode(" OR statut=", array_map("sql_quote", explode(";", $listes))));
}

/**
 * taille d'une chaine sans saut de lignes ni espaces ni punct
 */
function spiplistes_strlen($out){
	$out = preg_replace("/([[:space:]]|[[:punct:]])+/", "", $out);
	return (strlen($out));
}

/**
 * dans la queue d'envoi des courriers
 * @version CP-20080508
 */
function spiplistes_courriers_en_queue_compter ($sql_whereq = "") {
	// demande le nombre de courriers dans la queue
	// avec etat vide (si etat non vide, 
	// c'est que la meleuse est en train de l'envoyer)
	return(spiplistes_sql_compter("spip_auteurs_courriers", $sql_whereq));
}

/**
 * @version CP-20080510
 */
function spiplistes_courriers_en_queue_modifier ($array_set, $sql_whereq) {
	return(
		sql_update(
			'spip_auteurs_courriers'
			, $array_set
			, $sql_whereq
		)
	);
}


/**
 * @version CP-20080510
 */
function spiplistes_courriers_en_queue_supprimer ($sql_whereq) {
	if(($result = sql_delete('spip_auteurs_courriers', $sql_whereq)) === false) {
		spiplistes_sqlerror_log("courriers_en_queue_supprimer");
	}
	return($result);
}

/**
 *l a premiere etiquette sur le tas
 * @version CP-20080621
 */
function spiplistes_courriers_en_queue_premier ($select, $where) {
	return(
		sql_getfetsel(
				  $select
				, 'spip_auteurs_courriers'
				, $where
				, '', '', 1
			)
	);
}


/**
 ******************************************************************************
	Les fonctions spiplistes_format_abo_*() concernent les 
	formats de reception des abos
	
	Table cible : spip_auteurs_elargis
	
	Table historique utilisee par d'autres plugins.
	
	Cette table contient le format par defaut de l'abonne'.
	Le format final, reel, est dans le champ 'format' de
	la table des abonnements (spip_auteurs_listes).
	
	Ce format est attache' a l'abonnement. Ainsi, un abonne'
	peut s'inscrire au format HTML a' la liste 1
	et au format TEXTE a la liste 2.
	
 ******************************************************************************
 */


/**
 * suspend les abonnements d'un compte
 */
function spiplistes_format_abo_suspendre ($id_auteur) {
	return(spiplistes_format_abo_modifier($id_auteur));
}

/**
 * compter les formats (les abonnes ayant de'fini un format)
 * @version CP-20110529
 * @return int
 */
function spiplistes_formats_compter ($sql_where) {
	
	$result = sql_fetsel('COUNT(id_auteur) as n', 'spip_auteurs_elargis', $sql_where);
	if ($result) {
		$result = $result['n'];
	}
	return ($result);
}

/**
 * Ajouter un ou + format de réception par défaut
 *
 * @version CP-20110819
 * @param int|array $ids_auteur
 * @param string $format
 * @staticvar string $sql_table 
 * @return bool
 */
function spiplistes_format_abo_ajouter ($ids_auteur, $format = 'non')
{
	static $sql_table = 'spip_auteurs_elargis';
	$msg = array();
	
	if (is_int($ids_auteur)) { $ids_auteur = array($ids_auteur); }
	
	if (is_array($ids_auteur) && count($ids_auteur))
	{
		$sql_values = array();
		$sql_format = sql_quote($format);
		
		foreach ($ids_auteur as $id)
		{
			if ($id > 0)
			{
				$sql_values[] = '('.$id.','.$sql_format.')';
				$msg[] = $id;
			}
		}
		if (count ($sql_values)) {
			$sql_noms = '(id_auteur,`spip_listes_format`)';
			$sql_values = implode (',', $sql_values);
			if (($result = sql_insert($sql_table, $sql_noms, $sql_values)) === FALSE)
			{
				spiplistes_sqlerror_log('spiplistes_format_abo_ajouter()');
			}
			else {
				$msg = implode (',', $msg);
				if (strlen ($msg) > 30) { $msg = substr ($msg, 0, 30).'...'; }
				spiplistes_log_api('INSERT FORMAT '.$format.' TO '.$msg);
			}
		}
	}
	return ($result);
}

/**
 * Supprimer le format d'un ou plusieurs id_auteur
 * donné en argument (int ou tableau).
 * Si $arg == 'tous', supprimer tous les formats
 * 
 * @version CP-20110816
 * @param int|array|string $arg
 * @return bool
 */
function spiplistes_format_abo_supprimer ($arg)
{
	static $sql_table = 'spip_auteurs_elargis';
	$result = $sql_where = FALSE;
	
	if (is_string($arg) && ($id_auteur == 'tous'))
	{
		$sql_where = 'id_auteur>0';
	}
	else if (is_int ($arg) && ($arg > 0))
	{
		$arg = array($arg);
	}
	
	if (is_array($arg) && count($arg))
	{
		$sql_where = 'id_auteur IN ('.implode(',', $arg).')';
	}
	
	if ($sql_where)
	{
		if (($result = sql_delete($sql_table, $sql_where)) === FALSE)
		{
			spiplistes_sqlerror_log('format_abo_supprimer()');
		}
		else {
			spiplistes_log_api('DELETE FORMAT WHERE '.$sql_where);
		}
	}
	return ($result);
}

/**
 * Modifier le format abonne
 * 
 * si id_auteur est int, la fonction verifie si existant.
 * 	-> le modifie si existe
 * 	sinon, le crée.
 * Si c'est un tableau, il doit contenir les id_auteur
 *  L'appel via un INT est plus long que via un array.
 *  Si l'appel a déjà vérifié si le compte existe, il vaut mieux
 *  passer ce id via un array.
 * sinon, 'tous' pour modifier globalement (uniquement ceux ayant deja un format)
 * @version CP-20110819
 * @param int|array|string $id_auteur
 * @return bool
 *
 * @todo supprimer cette particularité insert si int ?
 * {@link spiplistes_format_abo_ajouter()}
 */
function spiplistes_format_abo_modifier ( $id_auteur, $format = 'non' ) {
	
	static $sql_table = 'spip_auteurs_elargis';

	if ( $format = spiplistes_format_valide($format) )
	{
		$sql_champs = array('`spip_listes_format`' => sql_quote($format));
		
		if ( $id_auteur == 'tous' ) {
			// appliquer le meme format a tous les abos
			$sql_result = sql_update($sql_table, $sql_champs, 1);
			$action = 'UPDATE';
			$log_ids = 'ALL';
		}
		else if ( is_array ($id_auteur) && count ($id_auteur) )
		{
			$ids = implode(',', $id_auteur);
			$sql_where = 'id_auteur IN ('.$ids.')';
			$sql_result = sql_update($sql_table, $sql_champs, 1);
			$action = 'UPDATE';
			$log_ids = $ids;
			if (strlen ($log_ids) > 30) { $log_ids = substr ($log_ids, 0, 30).'...'; }
			$log_ids = 'ids auteur '.$log_ids;
		}
		else if(($id_auteur = intval($id_auteur)) > 0)
		{
			if( ( $cur_format = spiplistes_format_abo_demande ( $id_auteur ) ) !== FALSE )
			{
				if ( !$cur_format )
				{
					// si inexistant faire un insert 
					$sql_champs = array(
						'id_auteur' => $id_auteur,
						'spip_listes_format' => $format
					);
					$sql_result = sql_insertq ( $sql_table, $sql_champs );
					$action = "insert";
				}
				else
				{
					// sinon update
					$sql_where = 'id_auteur='.sql_quote($id_auteur).' LIMIT 1'; 
					$sql_result = sql_update($sql_table, $sql_champs, $sql_where);
					$action = 'UPDATE';
				}
				$log_ids = 'id_auteur #'.$id_auteur;
			}
		}
		
		if($sql_result === FALSE) {
			spiplistes_sqlerror_log("spiplistes_format_abo_modifier() $action $id_auteur");
		}
		else {
			spiplistes_log_api("$action FORMAT '$format' FOR $log_ids");
		}
	}
	return($sql_result);
}

/**
 * Renvoie le format de réception par défaut d'un auteur.
 * Ou de tous si 'tous' en paramètre.
 *
 * @version CP-20110817
 * @param int|string $id_auteur
 * @return string|array le format, ou tableau (id => format) si 'tous' transmis en paramètre.
 * @todo à optimiser
 */
function spiplistes_format_abo_demande ($id_auteur) {
	static $sql_select = "id_auteur as id,`spip_listes_format` AS fmt";
	static $sql_from = 'spip_auteurs_elargis';
	$result = FALSE;
	
	if ($id_auteur == 'tous')
	{
		$sql_where = $sql_limit = '';
	}
	else if (($id_auteur = intval($id_auteur)) > 0)
	{
		$sql_where = 'id_auteur='.sql_quote($id_auteur);
		$sql_limit = 1;
	}
	else { $id_auteur = NULL; }
	
	if ($id_auteur)
	{
		if(($sql_result = sql_select($sql_select, $sql_from, $sql_where,
									 '', '', $sql_limit)) !== FALSE) {
			if (is_int($id_auteur))
			{
				$row = sql_fetch($sql_result);
				$result = $row['fmt'];
			}
			else
			{
				while($row = sql_fetch($sql_result)) {
					$result[$row['id']] = $row['fmt'];
				}
				$r = implode(',', $result);
			}
		}
		else {
			spiplistes_sqlerror_log('spiplistes_format_abo_demande()');
		}
	}
	
	return($result);
}

/**
 * liste des formats autorises
 * 	($idx == 'array') array (index et sa valeur identique) 
 * 	($idx == 'quoted') la valeur est sql_quote'
 * 	($idx == 'sql_where') string ligne sql_where formatee avec OR
 * @version CP-20090111
 * @param string $idx
 * @return string|array
 */
function spiplistes_formats_autorises ($idx = 'array') {
	static $formats;
	if(!$formats) {
		$ii = explode(";", _SPIPLISTES_FORMATS_ALLOWED);
		$formats = array('array' => array_combine($ii, $ii));
		$formats['quoted'] = array_map("sql_quote", $formats['array']);
		$formats['sql_where'] = "(`spip_listes_format`=" . implode(" OR `spip_listes_format`=", $formats['quoted']).")";
	}
	return($formats[$idx]);
}

/**
 * Donne le format de reception par defaut
 *
 * Le format de réception de courrier est définissable
 * par la page de configuration du plugin.
 * @version CP-20110508
 * @return string
 */
function spiplistes_format_abo_default () {
	$defaut = spiplistes_pref_lire('opt_format_courrier_defaut');
	if (
		($defaut != 'html')
		&& ($defaut != 'texte')
	) {
		$defaut = _SPIPLISTES_FORMAT_DEFAULT;
	}
	return ($defaut);
}

/**
 * Inventaire des formats de réception par défaut.
 * 
 * Tableau dont l'index est l'ID de l'auteur
 * et la valeur le format de réception par défaut.
 * 
 * @param string|array $sql_where
 * @version CP-20110510
 * @return array|bool
 */
function spiplistes_formats_defaut_lister ($sql_where = '') {
	
	if(
		($sql_result = sql_select('id_auteur,`spip_listes_format` AS format'
									, 'spip_auteurs_elargis'
							, $sql_where
									)
	) !== FALSE )
	{
		$auteurs = array();
	
		while($row = sql_fetch($sql_result)) {
			$auteurs[$row['id_auteur']] = $row['format'];
		}
		return ($auteurs);
	}
	else {
		spiplistes_sqlerror_log('spiplistes_formats_defaut_lister ()');
	}
	return (FALSE);
}

/**
 ******************************************************************************
	Les fonctions spiplistes_mod_listes_*() concernent les moderateurs
	
	Table cible : spip_auteurs_mod_listes
	
 ******************************************************************************
 */

/**
 * renvoie ID du moderateur de la liste
 * ou de toutes les listes si $id_liste = 'toutes'
 * -> result du style: array[id_liste] => array(id_auteur, ...)
 * @version CP-20080608
 * @param int $id_liste
 * @return bool|array
 */
function spiplistes_mod_listes_get_id_auteur ($id_liste) {
	$sql_from = 'spip_auteurs_mod_listes';
	$sql_where = 
		($id_liste == "toutes")
		? ""
		: "id_liste=".sql_quote(intval($id_liste))
		;
	if($sql_result = sql_select("*", $sql_from, $sql_where)) {
		$result = array();
		while($row = sql_fetch($sql_result)) {
			$ii = $row['id_liste'];
			if(!isset($result[$ii])) {
				$result[$ii] = array();
			}
			$result[$ii][] = $row['id_auteur'];
		}
		return($result);
	}
	return(false);
}

/**
 * supprime un ou + moderateurs d'une liste
 * ou tous les moderateurs si $id_auteur == 'tous'
 * @version CP-20090111
 * @param bool|string $id_auteur
 * @param int $id_liste
 * @return bool
 */
function spiplistes_mod_listes_supprimer ($id_auteur, $id_liste) {
	if(($id_auteur = intval($id_auteur)) > 0) {
		$sql_where = array("id_auteur=$id_auteur");
		$msg = "id_auteur #$id_auteur";
	} else if($id_auteur == "tous") {
		$sql_where = array("id_auteur>0");
		$msg = "ALL";
	}
	if($sql_where && (($id_liste = intval($id_liste)) > 0)) {
		$sql_where[] = "id_liste=$id_liste";
		if(($result = sql_delete('spip_auteurs_mod_listes', $sql_where)) !== false) {
			spiplistes_log_api("delete moderator #$id_auteur from id_liste #$id_liste");
		}
		else {
			spiplistes_sqlerror_log("mod_listes_supprimer()");
		}
	}
	return($result);
}

/**
 * Ajouter un modérateur à une liste
 * @version CP-20080512
 * @param int $id_auteur
 * @param int $id_liste
 * @return bool
 */
function spiplistes_mod_listes_ajouter ($id_auteur, $id_liste) {
	if(($id_liste = intval($id_liste)) > 0) {
		$result =
			sql_insertq('spip_auteurs_mod_listes'
				, array(
					  'id_auteur' => $id_auteur
					, 'id_liste' => $id_liste
					)
			);
		if($result !== false) {
			spiplistes_log_api("insert moderator id_auteur #$id_auteur to id_liste #$id_liste");
		}
		else {
			spiplistes_sqlerror_log("mod_listes_ajouter");
		}
	}
	return($result);
}

/**
 * Donne le nombre d emodérateurs d'une liste
 * @version CP-20080610
 * @param int $id_liste
 * @return bool|int
 */
function spiplistes_mod_listes_compter ($id_liste) {
	$n = sql_fetch(sql_select("COUNT(*) AS n", "spip_auteurs_mod_listes", "id_liste=".sql_quote($id_liste)));
	return(($n && $n['n']) ? $n['n'] : false);
}

/**
 * Renvoie tableau id_liste des listes moderees par l'auteur
 * @version CP-20080620
 * @return bool|array
 */
function spiplistes_mod_listes_id_auteur ($id_auteur) {
	$result = false;
	if($sql_result = sql_select('id_liste', 'spip_auteurs_mod_listes', 'id_auteur='.sql_quote($id_auteur))) {
		$result = array();
		while($row = sql_fetch($sql_result)) {
			$result[] = $row['id_liste'];
		}
	}
	else if($sql_result === false) {
		spiplistes_sqlerror_log("mod_listes_id_auteur");
	}
	return($result);
}

/**
 * Passe propre() sur un texte puis nettoie les trucs rajoutes par spip sur du html
 * @param string $texte
 * @return string
 * Remplace spiplistes_courrier_propre() qui est a supprimer apres verif.
 */
function spiplistes_texte_propre ($texte) {
	spiplistes_debug_log ('spiplistes_texte_propre()');
	static $adresse_site;
	if (!$adresse_site) { $adresse_site = $GLOBALS['meta']['adresse_site']; }
	static $style_rev = '__STYLE__';
	
	if (preg_match ('@<style[^>]*>[^<]*</style>@'
							  , $texte
							  , $style_reg
							  )
		> 0
	) {
		$style_str = $style_reg[0];
	}
	else {
		$style_str = '';
	}
	$texte = preg_replace ('@<style[^>]*>[^<]*</style>@', $style_rev, $texte);
	
	//passer propre si y'a pas de html (balises fermantes)
	if( !preg_match(',</?('._BALISES_BLOCS.')[>[:space:]],iS', $texte) ) 
	$texte = propre($texte); // pb: enleve aussi <style>...  
	
	// Corrections complementaires
	$patterns = array();
	$replacements = array();
	// html
	$patterns[] = '#<br>#i';
	$replacements[] = '<br />';
	$patterns[] = '#<b>([^<]*)</b>#i';
	$replacements[] = '<strong>\\1</strong>';
	$patterns[] = '#<i>([^<]*)</i>#i';
	$replacements[] = '<em>\\1</em>';
	// spip class
	$patterns[] = '# class="spip"#';
	$replacements[] = '';	
	
	$texte = preg_replace($patterns, $replacements, $texte);

	// remettre en place les styles
	$texte = str_replace ($style_rev, $style_str, $texte);
	
	//les liens avec double debut #URL_SITE_SPIP/#URL_ARTICLE
	$texte = preg_replace (
				'@'
				. $adresse_site
					. '/'
					. $adresse_site
					. '@'
				, $adresse_site
				, $texte
				);
	$texte = spiplistes_liens_absolus ($texte);
	
	return ($texte);
}

/**
 * Renvoie le titre propre. Longeur limitée par défaut à 128 car.
 * @param string $titre
 * @return string
 */
function spiplistes_titre_propre($titre, $max = 128){
	$titre = spiplistes_texte_propre($titre);
	$titre = substr($titre, 0, $max); // Au cas ou copie/colle
	return($titre);
}

/**
 * Recherche les différentes versions de patron possibles
 * <patron>._texte.en patron texte anglais
 * <patron>._texte patron texte generique
 * <patron>.en patron anglais
 * <patron> patron generique
 * @version CP-20081128
 * @param $path_patron string
 * @param $lang string
 * @param $chercher_texte bool si TRUE, chercher la version texte du patron
 * @return string le chemin du patron si patron trouve' ou FALSE si non trouve'
 * @todo verifier presence de lang dans les appels a cette fonction
 */
function spiplistes_patron_find_in_path ($path_patron, $lang, $chercher_texte = false) {
	static $t = "_texte", $h = ".html";
	
	if(!$lang) {
		$lang = $GLOBALS['spip_lang'];
	}
	
	if(
		$chercher_texte 
		&& (find_in_path($path_patron . $t . "." . $lang . $h) || find_in_path($path_patron . $t . $h))
	) {
		return($path_patron . $t);
	}
	else if(find_in_path($path_patron . "." . $lang . $h) || find_in_path($path_patron . $h)) {
		return($path_patron);
	
	}
	return(false);
}

/**
 * Renvoie le nom du patron pour la composition des messages de gestion
 * (confirmation d'abonnement, modification, etc.)
 * @todo boite de sélection dans une page de configuration ?
 * @return string
 * */
function spiplistes_patron_message () {
	return ('standard');
}

/**
 * Incruster les styles inline
 * 
 * @version CP-20110510
 * @param string $texte_html
 * @return string
 */
function spiplistes_html_styles_inline ($texte_html) {
	
	if (strpos($texte_html, 'spip_documents_center') !== FALSE)
	{
		$pattern = array(
			"{<span class='spip_document_\d* spip_documents spip_documents_center'>}m"
		);
		$replacement = array(
			'<span style="display:block;text-align:center">'
		);
		$texte_html = preg_replace ($pattern, $replacement, $texte_html);
	}
	return ($texte_html);
}

/**
 * Assembler/calculer un patron
 * @version CP-20090427
 * @param $patron string nom du patron
 * @param $contexte array
 * @return array le resultat html et texte seul dans un tableau
 */
function spiplistes_assembler_patron ($path_patron, $contexte) {

	include_spip('inc/distant');
	
	
	//spiplistes_debug_log('Chemin patrons : '.$path_patron);
	
	$patron_html = spiplistes_patron_find_in_path($path_patron, $contexte['lang'], false);
	$contexte['patron_html'] = $patron_html;
	spiplistes_debug_log('CREATE html version USING '.$patron_html);
	
	$result_html =
		($patron_html && find_in_path('patron_switch.html'))
		? recuperer_fond('patron_switch', $contexte)
		: ''
		;
	
	/**
	 * Calculer le contenu texte à partir
	 * du {patron}_texte s'il existe
	 */
	$patron_texte = spiplistes_patron_find_in_path($path_patron, $contexte['lang'], true);
	unset($contexte['patron_html']);
	$contexte['patron_texte'] = $patron_texte;
	$result_texte = '';
	$texte_ok = false;
	if ($patron_texte && ($patron_texte != $patron_html))
	{
		spiplistes_debug_log('CREATE text version USING '.$patron_texte);
	
		if (find_in_path('patron_switch.html')) {
			if($result_texte = recuperer_fond('patron_switch', $contexte))
			{
				$result_texte = spiplistes_courrier_version_texte($result_texte);
			}
			$texte_ok = true;
		}
	}
	/**
	 * si {patron}_texte manque, ou vide,
	 * calculer a partir de la version html
	 */
	if (!$texte_ok) {
		$result_texte = spiplistes_courrier_version_texte($result_html);
	}
	// eliminer les espaces pour un vrai calcul de poids
	$result_html = trim($result_html);
	$result_texte = trim($result_texte);
	$result = array ($result_html, $result_texte);
	
	return($result);
}

/**
 * donne contenu tampon au format html (CP-20071013) et texte
 * @return array (string $html, string $texte)
 */
function spiplistes_tampon_assembler_patron () {
	//spiplistes_log_api("calculer tampon");
	$contexte = array();
	$path_patron = spiplistes_pref_lire('tampon_patron');
	//spiplistes_log_api($path_patron);
	if(!empty($path_patron))
	{
		foreach(explode(",", _SPIPLISTES_TAMPON_CLES) as $key) {
			$s = spiplistes_pref_lire($key);
			$contexte[$key] = (!empty($s) && ($s != 'non')) ? $s : '';
		}
		$result = spiplistes_assembler_patron(_SPIPLISTES_PATRONS_TAMPON_DIR . $path_patron, $contexte);
	}
	else {
		$result = array("", "");
	}
	return($result);
}

/**
 * Construire le contenu à partir du patron
 * appartenant à la liste donnée, ou du patron nommé.
 * @version CP-20110701
 * @param bool|int $id_liste
 * @param bool|string $lang
 * @param string $pied_patron
 * @return array (version html, version texte)
 */
function spiplistes_pied_page_assembler_patron ($id_liste = FALSE,
												$lang = FALSE,
												$pied_patron = FALSE)
{
	$result = array('', '');
	
	/**
	 * Si l'id_liste > 0, prendre le pied patron de la liste
	 * sauf si transmis en paramètre.
	 */
	if(
	   (($id_liste = intval($id_liste)) > 0)
		&& !$pied_patron)
	{
		$pied_patron = sql_getfetsel('pied_page', 'spip_listes',
									 'id_liste='.sql_quote($id_liste), '','',1);
		/**
		 * Si patron vide (ancienne version de SPIP-Listes ?),
		 * appliquer le patron par defaut
		 */
		if (!$pied_patron)
		{
			$pied_patron = _SPIPLISTES_PATRONS_PIED_DEFAUT;
		}
	}
	if (!empty($pied_patron)) {
		/**
		 * Dans les anciennes versions de SPIP-Listes,
		 * (SPIP-Listes <= 1.9.2 ?)
		 * le contenu du pied de page était dans le champ pied_page.
		 * Rester compatible avec les anciennes versions de SPIP-Listes
		 */
		if(strlen($pied_patron) > _SPIPLISTES_PATRON_FILENAMEMAX)
		{
			$pied_html = $pied_patron;
			$pied_texte = spiplistes_courrier_version_texte ($pied_html);
		}
		/**
		 * ou construire à partir du patron désigné
		 */
		else if ($pied_patron != _SPIPLISTES_PATRON_PIED_IGNORE) {
			list($pied_html, $pied_texte) = spiplistes_courriers_assembler_patron (
				_SPIPLISTES_PATRONS_PIED_DIR . $pied_patron
				, array('lang'=>$lang));
		}
		
		$result = array($pied_html, $pied_texte);
	}
	
	return ($result);
}

function spiplistes_format_valide ($format) {
	return(in_array($format, array('non', 'texte', 'html')) ? $format : false);
}

/**
 ******************************************************************************
	Les fonctions spiplistes_auteurs_*() concernent les auteurs
	
	Table cible : spip_auteurs
	
 ******************************************************************************
 */

/**
 * @version CP-20080503
 * soit update cookie du cookie transmis
 * soit update cookie de l'email transmis
 */
function spiplistes_auteurs_cookie_oubli_updateq ($cookie_oubli,
												  $where,
												  $where_is_cookie = false)
{
	$result = FALSE;
	
	if(is_string($where) && !empty($where))
	{
		$where = (($where_is_cookie) ? 'cookie_oubli' : 'email')
			. '=' . sql_quote($where) . ' LIMIT 1';
		
		$result = sql_update('spip_auteurs',
							 array('cookie_oubli' => sql_quote($cookie_oubli)),
							 $where);
	}
	return ($result);
}

/**
 * Création d'un auteur à partir de l'email
 * Renvoie l'auteur sous forme d'un array
 * ou FALSE si erreur.
 * Dans la foulée, crée le format.
 * 
 * @version CP-20110823
 * @return bool|array
 */
function spiplistes_auteurs_create_from_mail ($email, $statut = '6forum', $format = 'non')
{
	$login = spiplistes_login_from_email ($email);
	$pass = creer_pass_aleatoire ();
	$auteur = array (
		'nom' => ucfirst ($login),
		'email' => $email,
		'login' => $login,
		'pass' => md5($pass),
		'statut' => $statut,
		'htpass' => generer_htpass($pass),
		'cookie_oubli' => creer_uniqid()
	);
	if (!$id_auteur = spiplistes_auteurs_auteur_insertq ($auteur)) {
		return (FALSE);
	}
	else {
		spiplistes_log_api('CREATE AUTEUR #'.$id_auteur);
		$auteur['id_auteur'] = $id_auteur;
		spiplistes_format_abo_modifier ($id_auteur, $format);
	}
	return ($auteur);
}

/**
 * @version CP-20080629
 * soit update cookie du cookie transmis
 * soit update cookie de l'email transmis
 */
function spiplistes_date_heure_valide ($date_heure) {
	$date_array = recup_date($date_heure);
	if($date_array) {
		list($annee, $mois, $jour) = $date_array;
		list($heures, $minutes, $secondes) = recup_heure($date_heure);
		return(array($annee, $mois, $jour, $heures, $minutes, $secondes));
	}
	return(false);
}

/**
 * Nombre total d'auteurs (ou visiteur, ou perso) elligibles
 * Nota: un compte 'nouveau' est un compte visiteur (inscription) qui ne s'est pas encore connecté
 * Nota2: un compte créé via l'espace privé mais pas encore connecté
 * n'a pas le statut 'nouveau' mais celui de son groupe
 * @version CP-200080519
 */
function spiplistes_auteurs_elligibles_compter ()
{
	static $nb;
	if(!$nb)
	{
		$sql_where = array(
			  'statut!='.sql_quote('5poubelle')
			, 'statut!='.sql_quote('nouveau')
			);
		$nb = sql_countsel('spip_auteurs', $sql_where);
	}
	return($nb);
}

/**
 * Total des auteurs qui ne sont pas abonnes a une liste
 * @version CP-200080519
 */
function spiplistes_auteurs_non_abonnes_compter ()
{
	static $nb;
	if($nb === null)
	{
		$selection =sql_select('id_auteur', 'spip_auteurs_listes', '','id_auteur','','','','',false);
		$sql_where = array(
			  'statut!='.sql_quote('5poubelle')
			, 'statut!='.sql_quote('nouveau')
			, 'id_auteur NOT IN ('.$selection.')'
			);
		$nb = sql_countsel('spip_auteurs', $sql_where);
	}
	return($nb);
}

/**
 * Renvoie la selection pour un seul auteur
 * @version CP-20080511 20110315
 * @return array OR false
 */
function spiplistes_auteurs_auteur_select ($select, $where = array())
{
	//$result = sql_select($select, 'spip_auteurs', $where, '', '', 1);
	$auteur = sql_fetsel($select, 'spip_auteurs', $where, '', '', 1);
	return($auteur);
}

/**
 * Modifie le statut d'un auteur (--> 5poubelle)
 * @version CP-20080511
 * @param string $sql_where du style 'id_auteur=12345'
 * @return bool
 */
function spiplistes_auteurs_auteur_delete ($sql_where) {
	// détruire ou mettre à la poubelle ?
	// SPIP ne détruit pas lui !
	// dans le doute...
	// if(($result = sql_delete('spip_auteurs', $sql_where." LIMIT 1")) === false) {
	if(($result = sql_update(
					"spip_auteurs"
					, array('statut' => sql_quote('5poubelle'))
					, $sql_where . " LIMIT 1"
				)) === false) {
		spiplistes_sqlerror_log("auteurs_auteur_delete");
	}
	return($result);
}

/**
 * @version CP-20080511
 * @return int|bool
 */
function spiplistes_auteurs_auteur_insertq ($champs_array) {
	$id_auteur = sql_insertq('spip_auteurs', $champs_array);
	return($id_auteur);
}

/**
 * @version CP-20090409
 */
function spiplistes_auteurs_auteur_valider ($id_auteur, $as_redact = false) {
	if($id_auteur = intval($id_auteur)) {
		if(($result = sql_update(
						"spip_auteurs_listes"
						, array('statut' => sql_quote('valide'))
						, "id_auteur=$id_auteur LIMIT 1"
					)) === false) {
			spiplistes_sqlerror_log("auteurs_auteur_valider");
		}
		else {
			
		}
	}
	return($result);
}

/**
 * @version CP-20110315
 * @return bool
 */
function spiplistes_auteurs_auteur_statut_modifier ($id_auteur, $statut)
{
	spiplistes_debug_log ('modification status for auteur #'.$id_auteur);
	$result = sql_update(
					'spip_auteurs'
					, array('statut' => sql_quote($statut))
					, 'id_auteur='.$id_auteur.' LIMIT 1'
				);
	return ($result);
}

/**
 * @version CP-20110321
 * Retourne une version texte pure du nom du site
 * @return string
 */
function spiplistes_nom_site_texte ($lang = '') {
	
	static $nom_site;
	$lang = trim ($lang);
	if (empty($lang)) {
		$lang = $GLOBALS['meta']['langue_site'];
	}
	
	if ($nom_site === null) 
	{
		$nom_site = array();
	}
	if (!isset($nom_site[$lang]))
	{
		$n = strip_tags(html_entity_decode(extraire_multi($GLOBALS['meta']['nom_site'])));
		
		// incorrect avec utf-8. Abime les diacritiques
		//$n = preg_replace ('@\s*@', ' ', $n);
		
		$nom_site[$lang] = trim($n);
	}
	return ($nom_site[$lang]);
}

/**
 * @version CP-20110321
 * @return string
 */
function spiplistes_texte_2_charset ($texte, $charset) {
	if ($charset && ($charset != $GLOBALS['meta']['charset'])) {
		include_spip('inc/charsets');
		$texte = unicode2charset(charset2unicode($texte), $charset);
	}
	return ($texte);
}

/**
 * Compose le contenu du message via un patron
 * Les patrons de messages sont dans ~/patrons/messages_abos
 * @param string $objet
 * @param string $patron
 * @param array $contexte
 * @return array ( message html, message texte)
 */
function spiplistes_preparer_message ($objet, $patron, $contexte) {
	
	/**
	 * Si pas de format, forcer a texte
	 */
	if ( $contexte['format'] != 'html' ) {
		$contexte['format'] = 'texte';
	}
	$format = $contexte['format'];

	$contexte['patron'] = $patron;
	$path_patron = _SPIPLISTES_PATRONS_MESSAGES_DIR . $patron;
	
	list($message_html, $message_texte) = spiplistes_assembler_patron($path_patron, $contexte);

	$charset = $GLOBALS['meta']['spiplistes_charset_envoi'];
	
	if($charset != $GLOBALS['meta']['charset'])
	{
		include_spip('inc/charsets');
		
		if($format == 'html') {
			$message_html = unicode2charset(charset2unicode($message_html), $charset);
		}
		$message_texte = spiplistes_translate_2_charset ($message_texte, $charset);
	}
	$message_html = ($format == 'html')
		? "<html>\n\n<body>\n\n" . $message_html	. "\n\n</body></html>"
		: ''
		;
	
	return( array($message_html, $message_texte) );
}

/**
 * Envoyer un message en tenant compte des prefs SPIP-Listes
 * (SMTP ou mail(), simuler l'envoi,....)
 *
 * Le message ($message) peut être
 * - soit un string (au format texte)
 * - soit un array ('html' => $contenu_html, 'texte' => $contenu_texte)
 * 
 * @param string $to
 * @param string $subject
 * @param string|array $message
 * @param string|bool $from
 * @param string $headers
 * @param string $format
 * @staticvar string|bool $opt_simuler_envoi
 * @version CP-20120726
 */
function spiplistes_envoyer_mail (
								  $to,
								  $subject,
								  $message,
								  $from = false,
								  $headers = '',
								  $format = 'texte'
								  ) {
	
	static $opt_simuler_envoi;
	if(!$opt_simuler_envoi) {
		$opt_simuler_envoi = spiplistes_pref_lire('opt_simuler_envoi');
	}

	if ($format != 'html') { $format = 'texte'; }
	
	/**
	 * Si le message à transmettre est string
	 * il est considéré comme au format texte
	 */
	if (is_string ($message)) {
		$format = 'texte';
		$message = array(
			'html' => '',
			'texte' => $message
			);
	}
	
	$charset = $GLOBALS['meta']['spiplistes_charset_envoi'];
	
	if (!$from)
	{
		$from = spiplistes_email_from_default();
	}
	if(strpos($from, '<') === false) {
		$fromname = spiplistes_nom_site_texte();
		
		if ($charset != $GLOBALS['meta']['charset']){
			include_spip('inc/charsets');
			$fromname = unicode2charset(charset2unicode($fromname),$charset);
		}
	}
	/**
	 * @TODO: voir email_reply_to ?
	 */
	$reply_to = 'no-reply'.preg_replace("|.*(@[a-z.]+)|i", "$1", email_valide($from));
	
	if($opt_simuler_envoi == 'oui') {
		spiplistes_log('!!! MAIL SIMULATION MODE !!!');
		$result = true;
	}
	else {
		if ( !class_exists('phpMail') ) {
			include_once (_DIR_PLUGIN_SPIPLISTES.'inc/spiplistes_mail.inc.php');
		}
		
		$email_a_envoyer = array();
		
		$return_path = spiplistes_return_path ($from);
		
		if ( $format != 'html' ) { $message['html'] = ''; }
		$email_a_envoyer = new phpMail($to, $subject, $message['html'], $message['texte'], $charset);
		$email_a_envoyer->From = $from ;
		$email_a_envoyer->AddCustomHeader('Errors-To: '.$return_path); 
		$email_a_envoyer->AddCustomHeader('Reply-To: '.$reply_to); 
		$email_a_envoyer->AddCustomHeader('Return-Path: '.$return_path); 
		$email_a_envoyer->SMTPKeepAlive = true;
		if($fromname) $email_a_envoyer->FromName = $fromname ; 

		//if ($result = $email_a_envoyer->send())
		//{
		//	$email_a_envoyer->SmtpClose();
		//}
		
		$headers = array (
			'Errors-To: '.$return_path ,
			'Reply-To: '.$reply_to ,
			'Return-Path: '.$return_path
		);
		
		$result = envoyer_mail (
			$to ,
			$subject ,
			$message ,
			$from ,
			$headers
		);
		
		spiplistes_debug_log ('EMAIL FROM '.$from.' TO '.$to.' : '.($result ? 'OK' : 'ERROR'));
	}
	return ($result);
}

function spiplistes_listes_statuts_periodiques () {
	static $s;
	if($s === null) {
		$s = explode(';', _SPIPLISTES_LISTES_STATUTS_PERIODIQUES);
	}
	return ($s);
}

/**
 * Creation du login a partir de l email donne'
 * @return string or false if error
 * @param $mail string
 */
function spiplistes_login_from_email ($mail) {
	
	$result = false;

	if($mail = email_valide($mail)) {
		
		// partie gauche du mail
		$left = substr($mail, 0, strpos($mail, "@"));
		
		// demande la liste des logins pour assurer unicite
		$sql_result = sql_select('login', 'spip_auteurs');
		$logins_base = array();
		while($row = sql_fetch($sql_result)) {
			$logins_base[] = $row['login'];
		}
		// creation du login
		for ($ii = 0; $ii < _SPIPLISTES_MAX_LOGIN_NN; $ii++) {
			$login = $left . (($ii > 0) ? $ii : "");
			if(!in_array($login, $logins_base))
			{
				$result = $login;
				break;
			}
		}	
	}
	return($result);
}

/**
 * Donne la langue de la liste
 */
function spiplistes_listes_langue ($id_liste) {
	if(($id_liste = intval($id_liste)) > 0) {
		return(
			sql_getfetsel(
				'lang'
				, "spip_listes"
				, "id_liste=".sql_quote($id_liste)." LIMIT 1"
			)
		);
	}
	return(false);
}

/*
 */
function spiplistes_return_path ($from) {
	return(spiplistes_pref_lire_defaut ('email_return_path_defaut', $from));
}

/**
 * Lire la valeur de $key dans les prefs (meta)
 * Si erreur (manquante) appliquer $defaut
 */
function spiplistes_pref_lire_defaut ($key, $default)
{
	$value = spiplistes_pref_lire($key);
	if(!$value) {
		$value = $default;
	}
	return($value);
}

/**
 * Renvoie la chaine interprétée au singulier ou au pluriel
 *
 * @version CP-20110817
 * @param int $nb
 * @param string $str_un index de la chaine de lang, singulier
 * @param string $str_pluriel index de la chaine de lang, pluriel
 * @param string $str_aucun index de la chaine de lang, aucun
 * @param string $idx préfixe de la chaine de lang
 * @return string
 */
function spiplistes_str_sing_pluriel ($nb, $str_un, $str_pluriel,
									  $str_aucun = FALSE,
									  $idx = 'spiplistes:'
									  ) {
	$nb = intval($nb);
	$result =
		($nb > 0)
		? _T($idx . (($nb > 1) ? $str_pluriel : $str_un), array('n' => $nb))
		: ($str_aucun ? _T($idx . $str_aucun) : '')
		;
	return ($result);
}

function spiplistes_str_auteurs ($nb) {
	return (spiplistes_str_sing_pluriel ($nb, '_1_auteur_', '_n_auteurs_'));
}

function spiplistes_str_abonnes ($nb) {
	return (spiplistes_str_sing_pluriel ($nb, '1_abonne', '_n_abonnes_', 'aucun_abo'));
}

function spiplistes_str_abonnements ($nb) {
	return (spiplistes_str_sing_pluriel ($nb, '_1_abo_', '_n_abos_', 'aucun_abonmt'));
}

function spiplistes_str_listes ($nb) {
	return (spiplistes_str_sing_pluriel ($nb, '1_liste', 'n_listes'));
}


/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
