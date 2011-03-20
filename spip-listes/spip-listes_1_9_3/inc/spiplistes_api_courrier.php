<?php

// inc/spiplistes_api_courrier.php

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

/*
	Fonctions consacrees au traitement du contenu du courrier et tampon :
	- filtres, convertisseurs texte, charset, etc.
	
	Toutes les fonctions ici ont un nom commencant pas 'spiplistes_courrier'
	
	Voir base/spiplistes_upgrade.php pour definitions et descriptions des tables
	
	
*/


/**
 * passe propre() sur un texte puis nettoye les trucs rajoutes par spip sur du html
 * ca s'utilise pour afficher un courrier dans l espace prive
 * on l'applique au courrier avant de confirmer l'envoi
 * @return string
 */
function spiplistes_courrier_propre($texte){
	$temp_style = ereg("<style[^>]*>[^<]*</style>", $texte, $style_reg);
	if (isset($style_reg[0])) 
		$style_str = $style_reg[0]; 
	else 
		$style_str = "";
	$texte = ereg_replace("<style[^>]*>[^<]*</style>", "__STYLE__", $texte);
	//passer propre si y'a pas de html (balises fermantes)
	if( !preg_match(',</?('._BALISES_BLOCS.')[>[:space:]],iS', $texte) ) 
	$texte = propre($texte); // pb: enleve aussi <style>...  
	$texte = spiplistes_courrier_propre_bloog($texte); //nettoyer les spip class truc en trop
	$texte = ereg_replace("__STYLE__", $style_str, $texte);
	//les liens avec double debut #URL_SITE_SPIP/#URL_ARTICLE
	$texte = ereg_replace($GLOBALS['meta']['adresse_site']."/".$GLOBALS['meta']['adresse_site'], $GLOBALS['meta']['adresse_site'], $texte);
	$texte = spiplistes_liens_absolus ($texte);
	
	return $texte;
}



/**
 * Enleve les enluminures Spip pour la bloogletter
 * Vincent CARON
 * @return string
 * @todo revoir certaines regles (<p><p></p></p> mal corrigé)
 */
function spiplistes_courrier_propre_bloog($texte) {
	
	static $eol = PHP_EOL;
	
	spiplistes_debug_log ("spiplistes_courrier_propre_bloog()");

	// eliminer les paragraphes vides
	$texte = preg_replace ('@<p class="spip">\s*</p>@i', '', $texte);
	
	// limiter les tabulations verticales 
	$texte = preg_replace ('@\v{3,}@', $eol, $texte);
	
	
	// div imbrique dans un p
	$texte = preg_replace ('@<p class="spip">\s*<div([^>]*)>@i', '<div\\2>' , $texte);
	$texte = preg_replace ('@</div>\s*</p>@i' , '</div>' , $texte);
	
	// style imbrique dans un p
	$texte = preg_replace ('@<p class="spip">\s*<style([^>]*)>@i', '<style>' , $texte);
	$texte = preg_replace ('@</style>\s*</p>@i', '</style>', $texte);
	
	
	// h3 imbrique dans un p
	$texte = preg_replace ('@<p class="spip">\s*<h3 class="spip">@i', '<h3>', $texte);
	$texte = preg_replace ('@</h3>\s*</p>@i', '</h3>', $texte);
	
	// h2 imbrique dans un p
	$texte = preg_replace ('@<p class="spip">\s*<h2>@i', '<h2>' , $texte);
	$texte = preg_replace ('@</h2>\s*</p>@i', '</h2>' , $texte);
	
	// h1 imbrique dans un p
	$texte = preg_replace ('@<p class="spip">\s*<h1>@i', '<h1>' , $texte);
	$texte = preg_replace ('@</h1>\s*</p>@i' , '</h1>' , $texte);
	
	// tableaux imbriques dans p
	$texte = preg_replace ('@<p class="spip">\s*<table@i' , '<table' , $texte);
	$texte = preg_replace ('@</table>\s*</p>@i' , '</table>' , $texte);
	
	// TD imbriques dans p
	$texte = preg_replace ('@<p class="spip">\s*</td@i' , '</td' , $texte);
	
	// p imbriques dans p
	$texte = preg_replace ('@<p class="spip">\s*<p@i' , '<p' , $texte);
	
	// DIV imbriques dans p
	$texte = preg_replace ('@<p class="spip">\s*<div@i' , '<div' , $texte);
	$texte = preg_replace ('@</div>\s*</p>@i' , '</div>' , $texte);
	
	// correction url ?
	$texte = preg_replace ('@\.php3&nbsp;\?@', '.php3?', $texte);
	$texte = preg_replace ('@\.php&nbsp;\?@', '.php?', $texte);
	
	return ($texte);
} 



/****
 * titre : spiplistes_courrier_version_texte
 * d'apres Clever Mail (-> NHoizey), mais en mieux.
****/

/**
 * @param $in string, contenu html du courrier a envoyer
 * @return string, version texte seul (ascii) du courrier
 **/
function spiplistes_courrier_version_texte($in) {

	$eol = PHP_EOL;
	
	// Nettoyage des liens des notes de bas de page
	$out = preg_replace("@<a href=\"#n(b|h)[0-9]+-[0-9]+\" name=\"n(b|h)[0-9]+-[0-9]+\" class=\"spip_note\">([0-9]+)</a>@"
						, "\\3", $in);
	
	// Supprimer tous les liens internes
	$patterns = array("/\<a href=['\"]#(.*?)['\"][^>]*>(.*?)<\/a>/ims");
	$replacements = array("\\2");
	$out = preg_replace($patterns,$replacements, $out);
	
	// Supprime feuille style
	$out = preg_replace("/<style[^>]*>[^<]*<\/style>/", '', $out);
	
	// les puces
	// @see http://www.spip.net/fr_article1825.html
	if (isset($GLOBALS['puce'])) {
		$out = str_replace($GLOBALS['puce'], $eol.'-', $out);
	}
	
	// Remplace tous les liens	
	$patterns = array("/\<a href=['\"](.*?)['\"][^>]*>(.*?)<\/a>/ims");
	$replacements = array("\\2 (\\1)");
	$out = preg_replace($patterns,$replacements, $out);
	
	$_traits = str_repeat('-', 40);
	$_points = str_repeat('.', 20);
	
	$out = preg_replace('/<h1[^>]*>/', '_SAUT__SAUT_'.$_traits.'_SAUT_', $out);
	$out = str_replace('</h1>', '_SAUT__SAUT_'.$_traits.'_SAUT__SAUT_', $out);
	$out = preg_replace('/<h2[^>]*>/', '_SAUT__SAUT_'.$_points.' ', $out);
	$out = str_replace('</h2>', ' '.$_points.'_SAUT__SAUT_', $out);
	$out = preg_replace('/<h3[^>]*>/', '_SAUT__SAUT_*', $out);
	$out = str_replace('</h3>', '*_SAUT__SAUT_', $out);
	
	// Les notes de bas de page
	$out = str_replace('<p class="spip_note">', $eol, $out);
	$out = preg_replace('/<sup>([0-9]+)<\/sup>/', '[\\1]', $out);
	
	// etrange parfum de regex dans un str_replace ?
	// @todo: a verifier
	//$out = str_replace('<p[^>]*>', $eol.$eol, $out);
	
	//$out = str_replace('<br /><img class=\'spip_puce\' src=\'puce.gif\' alt=\'-\' border=\'0\'>', "\n".'-', $out);
	$out = preg_replace('/<li[^>]>/', $eol.'-', $out);
	//$out = str_replace('<li>', "\n".'-', $out);
	
	
	// accentuation du gras -
	// <b>texte</b> -> *texte*
	$out = preg_replace('/<b[^>|r]*>/','*' ,$out);
	$out = str_replace ('</b>','*' ,$out);
	
	// accentuation du gras -
	// <strong>texte</strong> -> *texte*
	$out = preg_replace('/<strong[^>]*>/','*' ,$out);
	$out = str_replace ('</strong>','*' ,$out);
	
	
	// accentuation de l'italique
	// <i>texte</i> -> *texte*
	$out = preg_replace('/<i[^>|mg]*>/','*' ,$out);
	$out = str_replace ('</i>', '*', $out);
	
	$out = str_replace('&oelig;', 'oe', $out);
	$out = str_replace('&nbsp;', ' ', $out);
	$out = filtrer_entites($out);
	
	//attention, trop brutal pour les logs irc <@RealET>
	$out = supprimer_tags($out);
	
	$out = str_replace('\x0B', '', $out); 
	$out = str_replace("\t", '', $out) ;
	$out = preg_replace('/[ ]{3,}/', '', $out);
	
	// espace en debut de ligne
	$out = preg_replace("/(\r\n|\n|\r)[ ]+/m", $eol, $out);
	
	// Bring down number of empty lines to 2 max
	// sauts de ligne >= 3 reduits a 2
	$out = preg_replace("/(\r\n|\n|\r){3,}/m", $eol.$eol, $out);
	
	//retablir les saut de ligne
	//Réduire les > 3 à 3
	$out = preg_replace('/(_SAUT_){4,}/m', '_SAUT__SAUT__SAUT_', $out);
	$out = str_replace('_SAUT_', $eol, $out);
	
	//saut de lignes en debut et fin de texte
	$out = $eol.$eol.trim($out).$eol.$eol;
	
	// Faire des lignes de 75 caracteres maximum
	$out = wordwrap($out);
	
	return $out;

} // end spiplistes_courrier_version_texte()

/*
 * Ajouter les abonnes d'une liste a un envoi
 * @param : $id_courrier : reference d'un envoi
 * @param $id_liste : reference d'une liste
 * @return bool
 */
function spiplistes_courrier_remplir_queue_envois ($id_courrier, $id_liste, $id_auteur = 0) {
	$id_courrier = intval($id_courrier);
	$id_liste = intval($id_liste);
	
	spiplistes_debug_log("API: remplir courrier: #$id_courrier, liste: #$id_liste, auteur: #$id_auteur");
	
	if($id_courrier > 0) {
	
		$statut_q = sql_quote('a_envoyer');
		$id_courrier_q = sql_quote($id_courrier);
		$sql_valeurs = "";
	
		if($id_liste > 0) {
			// prendre la liste des abonnes a cette liste
			$ids_abos = spiplistes_listes_liste_abo_ids($id_liste);
			if(count($ids_abos)) {
				$sql_where_q = "(".implode(",", array_map("sql_quote", $ids_abos)).")";
				$sql_result = sql_select('id_auteur', 'spip_auteurs', "id_auteur IN $sql_where_q", ''
					, array('id_auteur'));
				$ids_auteurs = array();
				while($row = sql_fetch($sql_result)) {
					$ids_auteurs[] = intval($row['id_auteur']);
				}
				foreach($ids_abos as $ii) {
					// l'auteur n'existe plus, le desabonner !
					if(!in_array($ii, $ids_auteurs)) {
						spiplistes_abonnements_auteur_desabonner($ii, 'toutes');
					}
				}
				if(count($ids_auteurs) > 0) {
					// remplir la queue d'envois
					foreach($ids_auteurs as $ii) {
						$sql_valeurs .= "(".sql_quote($ii).",$id_courrier_q, $statut_q, NOW()),";
					}
					$sql_valeurs = rtrim($sql_valeurs, ",");			
				}
			}
		}
		else if(($id_auteur = intval($id_auteur)) > 0) {
			// envoi mail test
			$sql_valeurs = "(".sql_quote($id_auteur).",$id_courrier_q, $statut_q, NOW())";
		}
		if(!empty($sql_valeurs)) {
			sql_insert(
				'spip_auteurs_courriers'
				,	"("
					. "id_auteur,id_courrier,statut,maj"
					. ")"
				,	$sql_valeurs
			);
			$nb_etiquettes = spiplistes_courriers_en_queue_compter(
				array(
					"id_courrier=".sql_quote($id_courrier)
					, "statut=".sql_quote('a_envoyer')
				)
			);
			if($nb_etiquettes && ($id_liste > 0)) {
				spiplistes_courrier_modifier(
					$id_courrier
					, array('total_abonnes' => sql_quote($nb_etiquettes))
					);
			}
			return(true);
		}
	}
	else {
		spiplistes_debug_log("ERR: spiplistes_courrier_remplir_queue_envois($id_courrier, $id_liste, $id_auteur) valeur nulle ?");
	}
	return (false);
}

//CP-20080509: upadte sql sur un courrier
/*
 * Modifier un courrier
 * @return true ou false
 * @param $id_courrier 
 * @param $sql_set_array les valeurs à modifier. ex.: array('col1' => 'val1')
 * @param $quote si true, les valeurs seront quote' par sql_updateq
 */
function spiplistes_courrier_modifier ($id_courrier, $sql_set_array, $quote = true) {
	$id_courrier = intval($id_courrier);
	$sql_update = $quote ? "sql_updateq" : "sql_update";
	$result = 
		($id_courrier > 0)
		?
			$sql_update(
				"spip_courriers"
				, $sql_set_array
				, 'id_courrier='.sql_quote($id_courrier).' LIMIT 1'
			)
		: false
		;
	return($result);
}

//CP-20080509: changer le statut d'un courrier
function spiplistes_courrier_statut_modifier ($id_courrier, $new_statut) {
	return(spiplistes_courrier_modifier($id_courrier, array('statut' => $new_statut)));
}

// CP-20080329
function spiplistes_courrier_supprimer_queue_envois ($sql_where_key, $sql_where_value) {
	$sql_where = $sql_where_key."=".sql_quote($sql_where_value);
	switch($sql_where_key) {
		case 'id_courrier':
			$result = sql_delete("spip_auteurs_courriers", $sql_where);
			break;
		case 'statut':
			if(spiplistes_spip_est_inferieur_193()) { 
				$result = sql_delete("spip_auteurs_courriers"
					, "id_courrier IN (SELECT id_courrier FROM spip_courriers WHERE $sql_where)");	
			} else {
				// Sur les precieux conseils de MM :
				// passer la requete en 2 etapes pour assurer portabilite sql
				$selection =
					sql_select("id_courrier", "spip_courriers", $sql_where,'','','','','',false);
				$result = sql_delete("spip_auteurs_courriers", "id_courrier IN ($selection)");
			}
			break;
	}
	return($result);
}

function spiplistes_courrier_attacher_documents($id_courrier, $id_temp) {
	if(($id_courrier > 0) && ($id_temp < 0)) {
		return(
			sql_updateq(
				'spip_documents_liens'
				, array('id_objet' => sql_quote($id_courrier))
				, 'id_objet='.sql_quote($id_temp).' AND objet='.sql_quote('courrier')
		));
	}
	return(false);
}

// CP-20080329
function spiplistes_courrier_supprimer ($sql_where_key, $sql_where_value) {
	return(sql_delete("spip_courriers", $sql_where_key."=".sql_quote($sql_where_value)));
}
//CP-20080519
function spiplistes_courriers_premier ($id_courrier, $sql_select_array) {
	return(sql_fetsel($sql_select_array, 'spip_courriers', "id_courrier=".sql_quote($id_courrier), '', '', 1));
}

// renvoie id_auteur du courier (CP-20071018)
function spiplistes_courrier_id_auteur_get ($id_courrier) {
	if(($id_courrier = intval($id_courrier)) > 0) {
		if($sql_result = sql_select('id_auteur', 'spip_courriers', "id_courrier=".sql_quote($id_courrier), '', '', 1)) {
			if($row = spip_fetch_array($sql_result)) {
				return($row['id_auteur']);
			}
		}
	}
	return(false);
}

//CP-20080509: renvoie somme des abonnes en cours d envoi
function spiplistes_courriers_total_abonnes ($id_courrier = 0) {
	$id_courrier = intval($id_courrier);
	$sql_where = "statut=".sql_quote(_SPIPLISTES_COURRIER_STATUT_ENCOURS);
	if($id_courrier > 0) {
		$sql_where .= " AND id_courrier=".sql_quote($id_courrier);
	}
	return(
		sql_getfetsel(
			'SUM(total_abonnes)'
			, 'spip_courriers'
			, $sql_where
		)
	);
}

/*
 * CP-20081124
 * Assembler/calculer un patron
 * @return array le resultat html et texte seul dans un tableau
 * @param $patron string nom du patron
 * @param $contexte array
 * @param $ignorer bool
 */
function spiplistes_courriers_assembler_patron ($path_patron, $contexte, $ignorer = false) {

	if($ignorer) {
		$result = array("", "");
	}
	else {
		$result = spiplistes_assembler_patron($path_patron, $contexte);
	}
	
	return($result);
}


/*
 * CP-20081130
 * Calculer une balise a-la-SPIP pour le titre d'un courrier.
 * Pour le moment, uniquement #DATE et 2 filtres sont autorises 
 * @return le titre calcule'
 * @param $titre string
 *
 * CP-20110203
 * Reecriture
 * Possibilites plusieurs #DATE, avec ou sans parametres
 */
function spiplistes_calculer_balise_titre ($titre) {

	static $i_envelop = array('a', 'b', 'c', 'd');
	
	$now = $date = date('Y-m-d H:i:s');

	// longue comme un jour sans pain
	$pattern = '='
		// rechercher le bracket de gauche
		. '(?P<a>\[)'
		// le texte entre le bracket et la parenthese
		. '(?P<texte_avant>[^\(]*)'
		// la parenthese
		. '(?P<b>\()?\s*'
		// la balise DATE
		. '(?P<balise>#DATE)'
		// le ou les filtres de la balise
		. '(?P<filtres>(\s*\|\s*\w+\s*'
			// les parametres du filtre
			. '(?P<params>(\{[^\}]*})?)'
		. ')*)\s*'
		// la parenthese de droite
		. '(?P<c>\))?'
		// le texte entre la parenthese et le bracket
		. '(?P<texte_apres>[^\]]*)'
		// le bracket de droite
		. '(?P<d>\])'
		. '='
		;
		
	$recherche = array();
	$remplace = array();
	
	if($nres = intval(preg_match_all($pattern, $titre, $matches))) {

		for($ii = 0; $ii < $nres; $ii++)
		{
					
			$envelop = '';
			
			foreach($i_envelop as $aa) {
				$envelop .= (isset($matches[$aa][$ii])) ? $matches[$aa][$ii] : '';
				
			}
			
			// balise avec filtres
			if($envelop == '[()]')
			{
				$date = $now;
				
				$filtres = trim($matches['filtres'][$ii]);
				
				$params = trim($matches['params'][$ii]);
				$params = strlen($params) ? trim($params, '’\'{}') : '';
					
				$filtres = explode('|', $filtres);
				
				foreach($filtres as $ce_filtre)
				{
					$ce_filtre = trim($ce_filtre);
					if(strlen($ce_filtre))
					{
						$filtre = $ce_filtre;
						
						// tout les filtres demandent parametre
						// mais si ajout d'autres plus tard...
						//
						$params = false;
						if(($pos = strpos($ce_filtre, '{')) !== false)
						{
							$filtre = substr($ce_filtre, 0, $pos);
							
							$params = substr($ce_filtre, $pos);
							$params = strlen($params) ? trim($params, '’\'{}') : '';
						}
						switch($filtre) {
							case 'affdate':
								$v = $params;
								$v = preg_replace('=[^dDjlNSwzWFmMntLoYyaABgGhHiseIOPTZcrU\: \-]=', '', $v);
								$date = date($v);
								break;
							case 'plus':
								$v = intval($params);
								$date = plus($date, $v);
								break;
							case 'jour':
							case 'mois':
							case 'annee':
							case 'nom_mois':
							case 'ucfirst':
							case 'saison':
								$date = $filtre($date);
								break;
						}
					}
							
				}

				$recherche[] = $pattern;
				$remplace[] = $matches['texte_avant'][$ii] . $date . $matches['texte_apres'][$ii];
			}
		}
		
		if(count($remplace))
		{
			// remplacer les balises avec filtres
			$titre = preg_replace($recherche, $remplace, $titre, 1);
		}
		
		// reste des balises sans filtre ?
		if(strpos($titre, $s = '#DATE') !== false)
		{
			$titre = str_replace($s, $now, $titre);
		}
	}
	return($titre);
}
