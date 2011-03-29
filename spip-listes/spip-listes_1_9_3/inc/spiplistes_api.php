<?php

// inc/spiplistes_api.php

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

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if(!defined('_ECRIRE_INC_VERSION')) return;

include_spip ("inc/utils");
include_spip ("inc/filtres");    /* email_valide() */
include_spip ("inc/acces");      /* creer_uniqid() */
include_spip('inc/charsets');

include_spip('base/abstract_sql');

include_spip('inc/spiplistes_api_abstract_sql');
include_spip('inc/spiplistes_api_globales');

/**
 * Fonction de compatibilite de php4 / php5 pour http_build_query
 * 
 * @return un query string a passer a une url 
 * @param object $data un array de contexte
 * @param object $prefix[optional]
 * @param object $sep[optional]
 * @param object $key[optional]
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

/* function privee
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

//CP-20080508: renvoie OK ou ERR entre crochet
// sert principalement pour les log
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

//CP-20080508
function spiplistes_sql_compter ($table, $sql_whereq) {
	$sql_result = intval(sql_countsel($table, $sql_whereq));
	return($sql_result);
}

// CP-20080511
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
 * CP-20091207
 * @return string or bool
 */
function spiplistes_sql_now ()
{
	if($result = sql_fetsel('NOW() as maintenant'))
	{
		$result = $result['maintenant'];
	}
	return($result);
}

// CP-20080510
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
 * CP-20080324 : abonner un id_auteur a une id_liste
// CP-20080508 : ou une liste de listes ($id_liste est un tableau de (id)listes)
// CP-20090111: ajouter la date d'inscription
/*
 * @return array id_listes ajoutees
 */
function spiplistes_abonnements_ajouter ($id_auteur, $id_liste) {
	
	$r_id_listes = false;
	
	if(($id_auteur = intval($id_auteur)) > 0) {
		$sql_table = "spip_auteurs_listes";
		$sql_noms = "(id_auteur,id_liste,date_inscription)";
		
		$curr_abos_auteur = spiplistes_abonnements_listes_auteur($id_auteur);
		$r_id_listes = array();
		
		if(is_array($id_liste)) {
			// si une seule liste demandee
			$sql_valeurs = array();
			$msg = array();
			foreach($id_liste as $id) {
				if(
				   (($id = intval($id)) > 0)
				   // si pas encore abonne'
					&& !in_array($id, $curr_abos_auteur)
				  )
				{
					$sql_valeurs[] = "($id_auteur,$id,NOW())";
					$msg[] = $id;
				}
			}
			if(count($sql_valeurs)) {
				$sql_valeurs = implode(",", $sql_valeurs);
			}
		}
		else if(
			// si une seule liste demandee, et si pas encore abonne'
			(($id_liste = intval($id_liste)) > 0)
			&& (!$curr_abos || !in_array($id_liste, $curr_abos))
			)
		{
			$sql_valeurs = " ($id_auteur,$id_liste,NOW())";
			$msg = array($id_liste);
			$r_id_listes[] = $id_liste;
		}
		if($sql_valeurs) {
			$msg = "#" . implode(",#", $msg);
			if(sql_insert($sql_table, $sql_noms, $sql_valeurs) === false) {
				spiplistes_sqlerror_log ("spiplistes_abonnements_ajouter()");
			}
			else {
				spiplistes_log_api("subscribe id_auteur #$id_auteur to id_liste $msg");
			}
		}
	}
	return($r_id_listes);
}

// desabonner des listes (CP-20071016)
// $listes_statuts : array (statuts des listes,..)
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
		return(spiplistes_format_abo_modifier($id_auteur));
	}
	return(false);
}


//CP-20080512 : supprimer des abonnes de la table des abonnements
function spiplistes_abonnements_auteurs_supprimer ($auteur_statut) {
	$auteur_statut = "statut=".sql_quote($auteur_statut);
	if(spiplistes_spip_est_inferieur_193()) { 
		$result = sql_delete("spip_auteurs_listes", 
					"id_auteur IN (SELECT id_auteur FROM spip_auteurs WHERE $auteur_statut)");
	} else {
		// Sur les precieux conseils de MM :
		// passer la requete en 2 etapes pour assurer portabilite sql
		$selection =
			sql_select("id_auteur", "spip_auteurs", $auteur_statut,'','','','','',false);
		$sql_result = sql_delete("spip_auteurs_listes", "id_auteur IN ($selection)");
		if ($sql_result === false) {
			spiplistes_sqlerror_log("abonnements_auteurs_supprimer");
		}
	}
	return($result);
}

/*
 * CP-20080330 : renvoie la liste des abonnements pour id_auteur
 * @return tableau d'id_listes. La valeur est titre si $avec_titre true, sinon id_liste
 * @param $id_auteur int
 * @param $avec_titre bool
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

// CP-20080324 : desabonner un id_auteur d'une id_liste
// CP-20080508 : ou de toutes les listes si $id_liste = 'toutes'
// CP-20090111: ou tous les abonnes si id_auteur == 'tous'
// CP-20090410: ou une serie si array
function spiplistes_abonnements_auteur_desabonner ($id_auteur, $id_liste = false)
{
	//spiplistes_debug_log("spiplistes_abonnements_auteur_desabonner ($id_auteur, $id_liste)");
	
	$result = false;
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
			$msg2 = ' des listes';
		}
		else if(($id_liste = intval($id_liste)) > 0)
		{
			$sql_where[] = 'id_liste='.$id_liste;
			$msg2 = ' de la liste #'.$id_liste;
		}
		if(($result = sql_delete($sql_table, $sql_where)) === false)
		{
			spiplistes_debug_log ('ERR sql_delete: abonnements_auteur_desabonner()');
		}
		else {
			spiplistes_log_api('desabonne '.$msg1.' '.$msg2);
		}
	}
	return ($result);
}

//CP-20080512 : supprimer des abonnements
function spiplistes_abonnements_supprimer ($sql_whereq) {
	return(sql_delete('spip_auteurs_listes', $sql_whereq));
}


//CP-20080508 : dans la table des abonnements
function spiplistes_abonnements_compter ($sql_whereq = "") {
	return(spiplistes_sql_compter("spip_auteurs_listes", $sql_whereq));
}

/*
 * Compter les abonnements qui n'ont plus d'abonnes
 * @return array id_auteur
 */
function spiplistes_abonnements_zombies () {
	// SELECT id_auteur FROM spip_auteurs_listes WHERE id_auteur
	//	IN (SELECT id_auteur FROM spip_auteurs WHERE statut='5poubelle')
	$sql_select = "id_auteur";
	$sql_from = "spip_auteurs";
	$sql_where = "statut=".sql_quote('5poubelle');
	$selection = 
		(spiplistes_spip_est_inferieur_193())
		? "SELECT $sql_select FROM $sql_from WHERE $sql_where"
		: sql_select($sql_select, $sql_from, $sql_where,'','','','','',false)
		;
	$sql_from = "spip_auteurs_listes";
	$sql_result = sql_select(
		$sql_select
		, $sql_from
		, "$sql_select IN (".$selection.")"
		);
	if($sql_result === false) {
		spiplistes_sqlerror_log("spiplistes_abonnements_zombies");
	}
	$result = array();
	while($row = sql_fetch($sql_result)) {
		$result[] = $row[$sql_select];
	}
	return($result);

}

/**
 ******************************************************************************
	Les fonctions spiplistes_abonnements_*() concernent les listes
	
	Table cible : spip_listes
	
 ******************************************************************************
 */

//CP-20080508 : dans la table des listes
function spiplistes_listes_compter ($sql_whereq = "") {
	return(spiplistes_sql_compter("spip_listes", $sql_whereq));
}

//CP-20081228
//Donner le nombre d'abonnés (pour une liste) 
//qui ont un vrai format de réception (html ou texte)
// et une adresse mail valide
function spiplistes_listes_vrais_abos_compter ($id_liste) {
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
			spiplistes_sqlerror_log("spiplistes_listes_vrais_abos_compter()");
		}
		if($row = sql_fetch($sql_result)) {
			$result = $row['nb'];
		}
	}
	return($result);
}

// CP-20080501
function spiplistes_listes_liste_modifier ($id_liste, $array_set) {
	return(
		sql_update(
			'spip_listes'
			, $array_set
			, "id_liste=".sql_quote($id_liste)." LIMIT 1"
		)
	);
}

// CP-20080501
function spiplistes_listes_liste_supprimer ($id_liste) {
	$sql_where = "id_liste=".sql_quote(intval($id_liste));
	return(
		sql_delete('spip_listes', $sql_where." LIMIT 1")
		&& spiplistes_mod_listes_supprimer("tous", $id_liste)
		&& sql_delete('spip_auteurs_listes', $sql_where)
	);
}

//CP-20080512
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

//CP-20080602
// renvoie tableau de id_auteurs abonnes a une liste
function spiplistes_listes_liste_abo_ids ($id_liste) {
	$sql_result = sql_select('id_auteur', 'spip_auteurs_listes', "id_liste=".sql_quote($id_liste), '', array('id_auteur'));
	$ids_abos = array();
	while($row = sql_fetch($sql_result)) {
		$ids_abos[] = intval($row['id_auteur']);
	}
	return($ids_abos);
}

/**
 * Compter les abonnes.
 * @param $id_liste int. Si > 0, abonnes a cette liste,
 * 	sinon, nombre total d'abonnements (nb lignes dans la table)
 * @param $preciser. Si true, renvoie tableau total et formats
 * @return int ou array
 * */
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
		$selection = 
			(spiplistes_spip_est_inferieur_193())
			? 'SELECT id_auteur FROM spip_auteurs_listes '
				. (!empty($sql_whereq) ? 'WHERE '.$sql_whereq : '')
			: sql_select('id_auteur', 'spip_auteurs_listes', $sql_whereq,'','','','','',false)
			;
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

//CP-20080509: renvoie email emetteur d'une liste
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
	// si pas d'adresse moderateur, va chercher adresse par defaut
	if(!$result || empty($result)) {
		$result = spiplistes_email_from_default();
	}
	return($result);
}

//CP-20080511
function spiplistes_listes_liste_fetsel ($id_liste, $keys = "*") {
	$id_liste = intval($id_liste);
	return(sql_fetsel($keys, "spip_listes", "id_liste=".sql_quote($id_liste)." LIMIT 1"));
}

//CP-20081116
function spiplistes_listes_liste_statut ($id_liste) {
	return(spiplistes_listes_liste_fetsel($id_liste, 'statut'));
}

// CP-20080505 : renvoie array sql_where des listes publiees
function spiplistes_listes_sql_where_or ($listes) {
	return("statut=".implode(" OR statut=", array_map("sql_quote", explode(";", $listes))));
}

//taille d'une chaine sans saut de lignes ni espaces ni punct
function spiplistes_strlen($out){
	$out = preg_replace("/([[:space:]]|[[:punct:]])+/", "", $out);
	return (strlen($out));
}

//CP-20080508 : dans la queue d'envoi des courriers
function spiplistes_courriers_en_queue_compter ($sql_whereq = "") {
	// demande le nombre de courriers dans la queue
	// avec etat vide (si etat non vide, 
	// c'est que la meleuse est en train de l'envoyer)
	return(spiplistes_sql_compter("spip_auteurs_courriers", $sql_whereq));
}

// CP-20080510
function spiplistes_courriers_en_queue_modifier ($array_set, $sql_whereq) {
	return(
		sql_update(
			'spip_auteurs_courriers'
			, $array_set
			, $sql_whereq
		)
	);
}


// CP-20080510
function spiplistes_courriers_en_queue_supprimer ($sql_whereq) {
	if(($result = sql_delete('spip_auteurs_courriers', $sql_whereq)) === false) {
		spiplistes_sqlerror_log("courriers_en_queue_supprimer");
	}
	return($result);
}

// CP-20080621
// la premiere etiquette sur le tas
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


// suspend les abonnements d'un compte
function spiplistes_format_abo_suspendre ($id_auteur) {
	return(spiplistes_format_abo_modifier($id_auteur));
}

//CP2008111 supprimer le format d'un id_auteur
// CP-20090111: si $id_auteur == 'tous', supprimer tous les formats
function spiplistes_format_abo_supprimer ($id_auteur) {
	$sql_table = "spip_auteurs_elargis";
	if(($id_auteur = intval($id_auteur)) > 0) {
		$sql_where = "id_auteur=$id_auteur";
		$msg = "id_auteur #$id_auteur";
	}
	else if ($id_auteur == 'tous') {
		$sql_where = "id_auteur>0";
		$msg = "ALL";
	}
	if($sql_where) {
		if(($result = sql_delete("spip_auteurs_elargis", $sql_where)) === false) {
			spiplistes_sqlerror_log("format_abo_supprimer()");
		}
		else {
			spiplistes_log_api("delete format for $msg");
		}
	}
	return($result);
}


// modifier le format abonne
// si id_auteur, celui-ci uniquement
// sinon, 'tous' pour modifier globalement (uniquement ceux ayant deja un format)
function spiplistes_format_abo_modifier ($id_auteur, $format = 'non') {

	if($format = spiplistes_format_valide($format)) {
		$sql_table = "spip_auteurs_elargis";
		$sql_champs = array('`spip_listes_format`' => sql_quote($format));
		if($id_auteur == 'tous') {
			// appliquer le meme format a tous les abos
			$sql_result = sql_update($sql_table, $sql_champs, 1);
			$action = "update";
		}
		else if(($id_auteur = intval($id_auteur)) > 0) {
			if(($cur_format = spiplistes_format_abo_demande($id_auteur)) !== false) {
				// si pas d'erreur sql
				if(!$cur_format) {
					// si inexistant faire un insert 
					$sql_champs = array(
						'id_auteur' => $id_auteur
						, '`spip_listes_format`' => $format
					);
					$sql_result = sql_insertq($sql_table, $sql_champs);
					$action = "insert";
				} else {
					// sinon update
					$sql_where = "id_auteur=".sql_quote($id_auteur)." LIMIT 1"; 
					$sql_result = sql_update($sql_table, $sql_champs, $sql_where);
					$action = "update";
				}
			}
		}
		if($sql_result === false) {
			spiplistes_sqlerror_log("spiplistes_format_abo_modifier() $action $id_auteur");
		}
		else {
			$id_auteur = ($id_auteur == 'tous') ? "ALL" :  "id_auteur #$id_auteur";
			spiplistes_log_api("$action format to '$format' for $id_auteur");
		}
	}
	return($sql_result);
}

// renvoie le format d'abonnement d'un auteur
function spiplistes_format_abo_demande ($id_auteur) {
	$id_auteur = intval($id_auteur);
	$result = false;
	$sql_where = "id_auteur=".sql_quote($id_auteur);
	if($id_auteur > 0) {
		if(!spiplistes_spip_est_inferieur_193()) {
			$result = sql_getfetsel("`spip_listes_format`", "spip_auteurs_elargis", $sql_where, '', '', 1);
		} else {
			/*
			$result = sql_fetsel("`spip_listes_format` AS format", "spip_auteurs_elargis", $sql_where);
			$result = $result['format'];
			*/
			if(($sql_result = sql_select("`spip_listes_format` AS format", "spip_auteurs_elargis", $sql_where, '', '', 1)) !== false) {
				$row = sql_fetch($sql_result);
				$result = $row['format'];
				spiplistes_log_api("current format for id_auteur #$id_auteur = $result ($sql_where)");
			}
			else {
				spiplistes_sqlerror_log("spiplistes_format_abo_demande()");
			}
		}
		/* Code a valider. Si ok, supprimer ci-dessus.
		$GLOBALS['mysql_rappel_nom_base'] = false;
		$result = sql_getfetsel("spip_listes_format", "spip_auteurs_elargis", "id_auteur=".sql_quote($id_auteur));
		$result = spiplistes_format_valide($result);
		/**/
	}
	return($result);
}

/*
 * CP-20090111
 * liste des formats autorises
 * @return 
 * 	($idx == 'array') array (index et sa valeur identique) 
 * 	($idx == 'quoted') la valeur est sql_quote'
 * 	($idx == 'sql_where') string ligne sql_where formatee avec OR
 * @param $idx string[optional]
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
 ******************************************************************************
	Les fonctions spiplistes_mod_listes_*() concernent les moderateurs
	
	Table cible : spip_auteurs_mod_listes
	
 ******************************************************************************
 */

// renvoie ID du moderateur de la liste
// CP-20080608 : ou de toutes les listes si $id_liste = 'toutes'
// -> result du style: array[id_liste] => array(id_auteur, ...)
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

// CP-20080503: supprime un ou + moderateurs d'une liste
// CP-20090111: ou tous les moderateurs si $id_auteur == 'tous'
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

//CP-20080512
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

//CP-2080610
function spiplistes_mod_listes_compter ($id_liste) {
	$n = sql_fetch(sql_select("COUNT(*) AS n", "spip_auteurs_mod_listes", "id_liste=".sql_quote($id_liste)));
	return(($n && $n['n']) ? $n['n'] : false);
}

//CP-20080620
// renvoie tableau id_liste des listes moderees par l'auteur
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
 * passe propre() sur un texte puis nettoie les trucs rajoutes par spip sur du html
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

function spiplistes_titre_propre($titre){
	$titre = spiplistes_texte_propre($titre);
	$titre = substr($titre, 0, 128); // Au cas ou copie/colle
	return($titre);
}

/*
 * CP-20081128
 * Recherche les différentes versions de patron possibles
 * <patron>._texte.en patron texte anglais
 * <patron>._texte patron texte generique
 * <patron>.en patron anglais
 * <patron> patron generique
 * @return string le chemin du patron si patron trouve' ou FALSE si non trouve'
 * @param $path_patron string
 * @param $lang string
 * @param $chercher_texte bool si TRUE, chercher la version texte du patron
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


/*
 * CP-20090427
 * Assembler/calculer un patron
 * @return array le resultat html et texte seul dans un tableau
 * @param $patron string nom du patron
 * @param $contexte array
 */
function spiplistes_assembler_patron ($path_patron, $contexte) {

	include_spip('inc/distant');
	
	$patron_html = spiplistes_patron_find_in_path($path_patron, $contexte['lang'], false);
	$contexte['patron_html'] = $patron_html;
	
	$result_html =
		($patron_html && find_in_path('patron_switch.html'))
		? recuperer_fond('patron_switch', $contexte)
		: ''
		;
	
	// chercher si un patron version texte existe
	$patron_texte = spiplistes_patron_find_in_path($path_patron, $contexte['lang'], true);
	unset($contexte['patron_html']);
	$contexte['patron_texte'] = $patron_texte;
	$result_texte = '';
	$texte_ok = false;
	if ($patron_texte && ($patron_texte != $patron_html)) {
		if (find_in_path('patron_switch.html')) {
			$result_texte= recuperer_fond('patron_switch', $contexte);
			$texte_ok= true;
		}
	}
	// si version texte manque, la calculer
	// a partir de la version html
	if (!$texte_ok) {
		$result_texte= spiplistes_courrier_version_texte($result_html) . PHP_EOL ;
	}
	// eliminer les espaces pour un vrai calcul de poids
	$result_html = trim($result_html);
	$result_texte = trim($result_texte);
	$result = array ($result_html, $result_texte);
	
	return($result);
}

/* donne contenu tampon au format html (CP-20071013) et texte
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

function spiplistes_pied_page_assembler_patron ($id_liste, $lang = false) {
	
	$result = array("", "");
	
	if(($id_liste = intval($id_liste)) > 0)
	{
		$pied_patron = sql_getfetsel('pied_page', 'spip_listes', "id_liste=".sql_quote($id_liste), '','',1);
		
		$pied_patron =
			(!$pied_patron)
			// si patron vide (ancienne version de SPIP-Listes ?), appliquer le patron par defaut
			? _SPIPLISTES_PATRONS_PIED_DEFAUT
			: $pied_patron
			;
		if(strlen($pied_patron) > _SPIPLISTES_PATRON_FILENAMEMAX)
		{
			// probablement le contenu du pied (SPIP-Listes <= 1.9.2 ?)
			// rester compatible avec les anciennes version de SPIP-Listes
			// qui stoquaient le patron assemble' en base
			$pied_texte = spiplistes_courrier_version_texte($pied_html = $pied_patron);
			$result = array($pied_html, $pied_texte);
		}
		else if(strlen($pied_patron) && ($pied_patron != _SPIPLISTES_PATRON_PIED_IGNORE)) {
			
			if(!$lang) {
				$lang = spiplistes_listes_langue($id_liste) || $GLOBALS['spip_lang'];
			}
			$contexte = array('lang' => $lang);
			$result = spiplistes_assembler_patron (
				_SPIPLISTES_PATRONS_PIED_DIR . $pied_patron
				, $contexte
			);
		}
	}
	return ($result);
}

function spiplistes_format_valide ($format) {
	return(in_array($format, array("non", "texte", "html")) ? $format : false);
}

/**
 ******************************************************************************
	Les fonctions spiplistes_auteurs_*() concernent les auteurs
	
	Table cible : spip_auteurs
	
 ******************************************************************************
 */

// CP-20080503
// soit update cookie du cookie transmis
// soit update cookie de l'email transmis
function spiplistes_auteurs_cookie_oubli_updateq ($cookie_oubli, $where, $where_is_cookie = false) {
	if(is_string($where)) {
		$where = (($where_is_cookie) ? "cookie_oubli" : "email")
			. "=" . sql_quote($where) . " LIMIT 1";
	}
	return(sql_update('spip_auteurs', array('cookie_oubli' => sql_quote($cookie_oubli)), $where));
}

// CP-20080629
function spiplistes_date_heure_valide ($date_heure) {
	$date_array = recup_date($date_heure);
	if($date_array) {
		list($annee, $mois, $jour) = $date_array;
		list($heures, $minutes, $secondes) = recup_heure($date_heure);
		return(array($annee, $mois, $jour, $heures, $minutes, $secondes));
	}
	return(false);
}

//CP-200080519
// Nombre total d'auteurs (ou visiteur, ou perso) elligibles
// Nota: un compte 'nouveau' est un compte visiteur (inscription) qui ne s'est pas encore connecté
// Nota2: un compte créé via l'espace privé mais pas encore connecté
// n'a pas le statut 'nouveau' mais celui de son groupe
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

//CP-200080519
//Total des auteurs qui ne sont pas abonnes a une liste
function spiplistes_auteurs_non_abonnes_compter ()
{
	static $nb;
	if($nb === null)
	{
		$selection =
			(spiplistes_spip_est_inferieur_193())
			? 'SELECT id_auteur FROM spip_auteurs_listes GROUP BY id_auteur'
			: sql_select('id_auteur', 'spip_auteurs_listes', '','id_auteur','','','','',false)
		;
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
 * CP-20080511 20110315
 * Renvoie la selection pour un seul auteur
 * @return array OR false
 */
function spiplistes_auteurs_auteur_select ($select, $where = array())
{
	//$result = sql_select($select, 'spip_auteurs', $where, '', '', 1);
	$auteur = sql_fetsel($select, 'spip_auteurs', $where, '', '', 1);
	return($auteur);
}

//CP-20080511
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
 * CP-20080511
 * @return int OR boolean
 */
function spiplistes_auteurs_auteur_insertq ($champs_array) {
	$id_auteur = sql_insertq('spip_auteurs', $champs_array);
	return($id_auteur);
}

//CP-20090409
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
 * CP-20110315
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
 * CP-20110321
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
 * CP-20110321
 * @return string
 */
function spiplistes_texte_2_charset ($texte, $charset) {
	if ($charset && ($charset != $GLOBALS['meta']['charset'])) {
		include_spip('inc/charsets');
		$texte = unicode2charset(charset2unicode($texte), $charset);
	}
	return ($texte);
}

//CP-20080511
// CP-20090111: utiliser l'api pour pouvoir envoyer par smtp si besoin
function spiplistes_envoyer_mail ($to, $subject, $message, $from = false, $headers = '', $format = 'texte') {
	
	static $opt_simuler_envoi;

	// si desabo, plus de format ! donc forcer a texte
	$format = ($format == 'html') ? $format : 'texte';
	
	$charset = $GLOBALS['meta']['spiplistes_charset_envoi'];
	if(!$opt_simuler_envoi) {
		$opt_simuler_envoi = spiplistes_pref_lire('opt_simuler_envoi');
	}
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
	// @TODO: voir email_reply_to ?
	$reply_to = 'no-reply'.preg_replace("|.*(@[a-z.]+)|i", "$1", email_valide($from));
	
	if($opt_simuler_envoi == 'oui') {
		spiplistes_log("!!! MAIL SIMULATION MODE !!!");
		$result = true;
	}
	else {
		include_once(_DIR_PLUGIN_SPIPLISTES.'inc/spiplistes_mail.inc.php');
		
		$email_a_envoyer = array();
		
		$return_path = spiplistes_return_path($from);
		
		if(is_array($message))
		{
			if($format=='html' && isset($message[$format])) {
				$email_a_envoyer['html'] = new phpMail($to, $subject, $message['html'], $message['texte'], $charset);
				$email_a_envoyer['html']->From = $from ; 
				if($fromname) $email_a_envoyer['html']->FromName = $fromname ; 
				$email_a_envoyer['html']->AddCustomHeader("Errors-To: ".$return_path); 
				$email_a_envoyer['html']->AddCustomHeader("Reply-To: ".$from); 
				$email_a_envoyer['html']->AddCustomHeader("Return-Path: ".$return_path); 	
				$email_a_envoyer['html']->SMTPKeepAlive = true;
				$email_a_envoyer['html']->Body = $message['html']->Body;
				$email_a_envoyer['html']->AltBody = $message['html']->AltBody;
			}
			$message = $message['texte']->Body;
		}
		//$message = spiplistes_html_entity_decode ($message, $charset);
		$message = spiplistes_translate_2_charset ($message, $charset, true);
		
		//$email_a_envoyer['texte'] = new phpMail($to, $subject, '', html_entity_decode($message), $charset);
		$email_a_envoyer['texte'] = new phpMail($to, $subject, '', $message, $charset);
		$email_a_envoyer['texte']->From = $from ;
		if($fromname) $email_a_envoyer['html']->FromName = $fromname ; 
		$email_a_envoyer['texte']->AddCustomHeader('Errors-To: '.$return_path); 
		$email_a_envoyer['texte']->AddCustomHeader('Reply-To: '.$reply_to); 
		$email_a_envoyer['texte']->AddCustomHeader('Return-Path: '.$return_path); 
		$email_a_envoyer['texte']->SMTPKeepAlive = true;
		
		$result = $email_a_envoyer[$format]->send();
		
		$msg = "email from $from to $to";
		spiplistes_log(!$result ? "error: $msg not sent" : "$msg sent");
	}
	return($result);
}

function spiplistes_listes_statuts_periodiques () {
	static $s;
	if($s === null) {
		$s = explode(';', _SPIPLISTES_LISTES_STATUTS_PERIODIQUES);
	}
	return ($s);
}

/*
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

/*
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
 **/
function spiplistes_pref_lire_defaut ($key, $default)
{
	$value = spiplistes_pref_lire($key);
	if(!$value) {
		$value = $default;
	}
	return($value);
}

function spiplistes_str_auteurs ($nb)
{
	$result =
		($nb > 0)
		? _T('spiplistes:' . (($nb > 1) ? '_n_auteurs_' : '_1_auteur_'), array('n' => $nb))
		: 'erreur param'
		;
	return($result);
}

function spiplistes_str_abonnes ($nb) 
{
	$result =
		($nb > 0)
		? _T('spiplistes:' . (($nb > 1) ? '_n_abonnes_' : '1_abonne'), array('n' => $nb))
		: _T('spiplistes:aucun_abo')
		;
	return($result);
}

function spiplistes_str_abonnements ($nb) 
{
	$result =
		($nb > 0)
		? _T('spiplistes:' . (($nb > 1) ? '_n_abos_' : '_1_abo_'), array('n' => $nb))
		: _T('spiplistes:aucun_abonmt')
		;
	return($result);
}


function spiplistes_str_listes ($nb) 
{
	$result =
		($nb > 0)
		? _T('spiplistes:' . (($nb > 1) ? 'n_listes' : '1_liste'), array('n' => $nb))
		: 'erreur param'
		;
	return($result);
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
