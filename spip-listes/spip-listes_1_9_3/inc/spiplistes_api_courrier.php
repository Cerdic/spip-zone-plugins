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


/*
	function spiplistes_courrier_propre($texte)
	passe propre() sur un texte puis nettoye les trucs rajoutes par spip sur du html
	ca s'utilise pour afficher un courrier dans l espace prive
	on l'applique au courrier avant de confirmer l'envoi
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
	$texte = liens_absolus($texte);
	
	return $texte;
}

/*
 * complete caracteres manquants dans HTML -> ISO
 * @return la chaine transcrite
 * @param $texte le texte a transcrire
 * @param $charset le charset souhaite'. Normalement 'iso-8859-1' (voir page de config)
 * @param $is_html flag. Pour ne pas transcrire completement la version html
 * @see http://fr.wikipedia.org/wiki/ISO_8859-1
 * @see http://www.w3.org/TR/html401/sgml/entities.html
 */
function spiplistes_translate_2_charset ($texte, $charset='AUTO', $is_html = false) {
	
	$texte = charset2unicode($texte);
	$texte = unicode2charset($texte, $charset);
	if($charset != "utf-8") {
		$remplacements = array(
			"&#8217;" => "'"	// quote
			, "&#8220;" => '"' // guillemets
			, "&#8221;" => '"' // guillemets
			)
			;
		if(!$is_html) {
			$remplacements = array_merge(
				$remplacements
				, array(
							// Latin Extended
					  '&#255;' => chr(255) // 'ÿ' // yuml inconnu php ?
					, '&#338;' => "OE"  // OElig
					, '&#339;' => "oe"  // oelig
					, '&#352;' => "S"  // Scaron
					, '&#353;' => "s"  // scaron
					, '&#376;' => "Y"  // Yuml
						// General Punctuation
					, '&#8194;' => " " // ensp
					, '&#8195;' => " " // emsp
					, '&#8201;' => " " // thinsp
					, '&#8204;' => " " // zwnj
					, '&#8205;' => " " // zwj
					, '&#8206;' => " " // lrm
					, '&#8207;' => " " // rlm
					, '&#8211;' => "-" // ndash
					, '&#8212;' => "--" // mdash
					, '&#39;' => "'" // apos
					, '&#8216;' => "'" // lsquo
					, '&#8217;' => "'" // rsquo
					, '&#8218;' => "'" // sbquo
					, '&#8220;' => '"' // ldquo
					, '&#8221;' => '"' // rdquo
					, '&#8222;' => '"' // bdquo
					, '&#8224;' => "+" // dagger
					, '&#8225;' => "++" // Dagger
					, '&#8240;' => "0/00" // permil
					, '&#8249;' => "." // lsaquo
					, '&#8250;' => "." // rsaquo
						// sans oublier
					, '&#8364;' => "euros"  // euro
				)
			);
		}
		$texte = strtr($texte, $remplacements);
	}
	return($texte);
}


/****
 * titre : spiplistes_courrier_propre_bloog
 * Enleve les enluminures Spip pour la bloogletter
 Vincent CARON
****/

function spiplistes_courrier_propre_bloog($texte) {

	$texte = ereg_replace("<p class=\"spip\">(\r\n|\n|\r)?</p>",'',$texte);
	$texte = eregi_replace("\n{3}", "\n", $texte);
	
	
	// div imbrique dans un p
	$texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<div([^>]*)>" , "<div\\2>" , $texte);
	$texte = eregi_replace( "<\/div>(\r\n|\n|\r| )*<\/p>" , "</div>" , $texte);
	
	// style imbrique dans un p
	$texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<style([^>]*)>" , "<style>" , $texte);
	$texte = eregi_replace( "<\/style>(\r\n|\n|\r| )*<\/p>" , "</style>" , $texte);
	
	
	// h3 imbrique dans un p
	$texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<h3 class=\"spip\">" , "<h3>" , $texte);
	$texte = eregi_replace( "<\/h3>(\r\n|\n|\r| )*<\/p>" , "</h3>" , $texte);
	
	// h2 imbrique dans un p
	$texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<h2>" , "<h2>" , $texte);
	$texte = eregi_replace( "<\/h2>(\r\n|\n|\r| )*<\/p>" , "</h2>" , $texte);
	
	// h1 imbrique dans un p
	$texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<h1>" , "<h1>" , $texte);
	$texte = eregi_replace( "<\/h1>(\r\n|\n|\r| )*<\/p>" , "</h1>" , $texte);
	
	
	// tableaux imbriques dans p
	$texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<(table|TABLE)" , "<table" , $texte);
	$texte = eregi_replace( "<\/(table|TABLE)>(\r\n|\n|\r| )*<\/p>" , "</table>" , $texte);
	
	// TD imbriques dans p
	$texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<(\/td|\/TD)" , "</td" , $texte);
	//$texte = eregi_replace( "<\/(td|TD)>(\r\n|\n|\r| )*<\/p>" , "</td>" , $texte);
	
	// p imbriques dans p
	$texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<(p|P)" , "<p" , $texte);
	//$texte = eregi_replace( "<\/(td|TD)>(\r\n|\n|\r| )*<\/p>" , "</td>" , $texte);
	
	// DIV imbriques dans p
	$texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<(div|DIV)" , "<div" , $texte);
	$texte = eregi_replace( "<\/(DIV|div)>(\r\n|\n|\r| )*<\/p>" , "</div>" , $texte);
	
	//$texte = PtoBR($texte);
	$texte = ereg_replace ("\.php3&nbsp;\?",".php3?", $texte);
	$texte = ereg_replace ("\.php&nbsp;\?",".php?", $texte);
	
	return $texte;
} // end spiplistes_courrier_propre_bloog()



/****
 * titre : spiplistes_courrier_version_texte
 * d'apres Clever Mail (-> NHoizey), mais en mieux.
****/

function spiplistes_courrier_version_texte($in) {

	// Nettoyage des liens des notes de bas de page
	$out = ereg_replace("<a href=\"#n(b|h)[0-9]+-[0-9]+\" name=\"n(b|h)[0-9]+-[0-9]+\" class=\"spip_note\">([0-9]+)</a>", "\\3", $in);
	
	// Supprimer tous les liens internes
	$patterns = array("/\<a href=['\"]#(.*?)['\"][^>]*>(.*?)<\/a>/ims");
	$replacements = array("\\2");
	$out = preg_replace($patterns,$replacements, $out);
	
	// Supprime feuille style
	$out = ereg_replace("<style[^>]*>[^<]*</style>", "", $out);
	
	// les puces
	$out = str_replace($GLOBALS['puce'], "\n".'-', $out);
	
	// Remplace tous les liens	
	$patterns = array("/\<a href=['\"](.*?)['\"][^>]*>(.*?)<\/a>/ims");
	$replacements = array("\\2 (\\1)");
	$out = preg_replace($patterns,$replacements, $out);
	
	$_traits = str_repeat('-', 40);
	$_points = str_repeat('.', 20);
	
	$out = ereg_replace("<h1[^>]*>", "_SAUT__SAUT_".$_traits."_SAUT_", $out);
	$out = str_replace("</h1>", "_SAUT__SAUT_".$_traits."_SAUT__SAUT_", $out);
	$out = ereg_replace("<h2[^>]*>", "_SAUT__SAUT_".$_points." ", $out);
	$out = str_replace("</h2>", " ".$_points."_SAUT__SAUT_", $out);
	$out = ereg_replace("<h3[^>]*>", "_SAUT__SAUT_*", $out);
	$out = str_replace("</h3>", "*_SAUT__SAUT_", $out);
	
	// Les notes de bas de page
	$out = str_replace("<p class=\"spip_note\">", "\n", $out);
	$out = ereg_replace("<sup>([0-9]+)</sup>", "[\\1]", $out);
	
	$out = str_replace("<p[^>]*>", "\n\n", $out);
	
	//$out = str_replace('<br /><img class=\'spip_puce\' src=\'puce.gif\' alt=\'-\' border=\'0\'>', "\n".'-', $out);
	$out = ereg_replace ('<li[^>]>', "\n".'-', $out);
	//$out = str_replace('<li>', "\n".'-', $out);
	
	
	// accentuation du gras -
	// <b>texte</b> -> *texte*
	$out = ereg_replace ('<b[^>|r]*>','*' ,$out);
	$out = str_replace ('</b>','*' ,$out);
	
	// accentuation du gras -
	// <strong>texte</strong> -> *texte*
	$out = ereg_replace ('<strong[^>]*>','*' ,$out);
	$out = str_replace ('</strong>','*' ,$out);
	
	
	// accentuation de l'italique
	// <i>texte</i> -> *texte*
	$out = ereg_replace ('<i[^>|mg]*>','*' ,$out);
	$out = str_replace ('</i>','*' ,$out);
	
	$out = str_replace('&oelig;', 'oe', $out);
	$out = str_replace("&nbsp;", " ", $out);
	$out = filtrer_entites($out);
	
	//attention, trop brutal pour les logs irc <@RealET>
	$out = supprimer_tags($out);
	
	$out = str_replace("\x0B", "", $out); 
	$out = ereg_replace("\t", "", $out) ;
	$out = ereg_replace("[ ]{3,}", "", $out);
	
	// espace en debut de ligne
	$out = preg_replace("/(\r\n|\n|\r)[ ]+/", "\n", $out);
	
//marche po
	// Bring down number of empty lines to 4 max
	$out = preg_replace("/(\r\n|\n|\r){3,}/m", "\n\n", $out);
	
	//retablir les saut de ligne
	$out = preg_replace("/(_SAUT_){3,}/m", "_SAUT__SAUT__SAUT_", $out);
	$out = preg_replace("/_SAUT_/", "\n", $out);
	//saut de lignes en debut de texte
	$out = preg_replace("/^(\r\n|\n|\r)+/", "\n\n", $out);
	//saut de lignes en debut ou fin de texte
	$out = preg_replace("/(\r\n|\n|\r)+$/", "\n\n", $out);
	
	// Faire des lignes de 75 caracteres maximum
	$out = wordwrap($out);
	
	return $out;

} // end spiplistes_courrier_version_texte()

/*
 * Ajouter les abonnes d'une liste a un envoi
 * @param : $id_courrier : reference d'un envoi
 * @param $id_liste : reference d'une liste
 */
function spiplistes_courrier_remplir_queue_envois ($id_courrier, $id_liste, $id_auteur = 0) {
	$id_courrier = intval($id_courrier);
	$id_liste = intval($id_liste);
	
spiplistes_log("API: remplir courrier: #$id_courrier, liste: #$id_liste, auteur: #$id_auteur", _SPIPLISTES_LOG_DEBUG);
	
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
spiplistes_log("ERR: spiplistes_courrier_remplir_queue_envois($id_courrier, $id_liste, $id_auteur) valeur nulle ?"
		, _SPIPLISTES_LOG_DEBUG);
	return(false);
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
 */
function spiplistes_calculer_balise_titre ($titre) {

	// longue comme un jour sans pain 
	$pattern = "=((?P<a>\[)?(?P<texte_avant>[^(\[]*)(?P<b>\()?\s*(?P<balise>#DATE)(?P<filtres>(\s*\|\s*\w+\s*{?\s*\'?\w+\'?\s*}?)*)\s*(?P<c>\))?(?P<texte_apres>[^(\]]*)(?P<d>\])?)=";
	
	if (preg_match($pattern, $titre, $match)) {
		
		if($match['balise'] == "#DATE") {
			
			$date = date('Y-m-d H:i:s');
			
			$texte_avant = isset($match['texte_avant']) ? $match['texte_avant'] : "";
			$texte_apres = isset($match['texte_apres']) ? $match['texte_apres'] : "";

			$envelop = "";
			foreach(array('a', 'b', 'c', 'd') as $ii) {
				$envelop .= (isset($match[$ii])) ? $match[$ii] : "";
			}
			
			if($envelop == "[()]") {
				$filtres = explode('|', $match['filtres']);
				foreach($filtres as $filtre) {
					$filtre = trim($filtre);
					if(preg_match("=(\w+)\s*(\{)?\s*(\'?\w*\'?)?\s*(\})?=", $filtre, $match)) {
						switch($match[1]) {
							case 'affdate':
								$v = $match[3];
								$v = preg_replace("=[^dDjlNSwzWFmMntLoYyaABgGhHiseIOPTZcrU\: \-]=", "", $v);
								$date = date($v);
								break;
							case 'plus':
								$v = intval($match[3]);
								$date += $v;
								break;
						}
					}
				}
			}
			
			$titre = preg_replace($pattern, $texte_avant.$date.$texte_apres, $titre);
		}
	}
	return($titre);
}

?>