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
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/*
	Fonctions consacrées au traitement du contenu du courrier et tampon :
	- filtres, convertisseurs texte, charset, etc.
	
	Toutes les fonctions ici ont un nom commencant pas 'spiplistes_courrier'
	
	Voir base/spiplistes_upgrade.php pour définitions et descriptions des tables
	
	
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
	//les liens avec double début #URL_SITE_SPIP/#URL_ARTICLE
	$texte = ereg_replace($GLOBALS['meta']['adresse_site']."/".$GLOBALS['meta']['adresse_site'], $GLOBALS['meta']['adresse_site'], $texte);
	$texte = liens_absolus($texte);
	
	return $texte;
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
 * d'après Clever Mail (-> NHoizey), mais en mieux.
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
	
	return $out;

} // end spiplistes_courrier_version_texte()

/*
	donne contenu tampon au format texte (CP-20071013)
	tampon_patron: nom du tampon (fichier, sans extension)
	tampon_html: contenu html converti en texte si pas de contenu
*/
function spiplistes_courrier_tampon_texte ($tampon_patron, $tampon_html) {
	$contexte_patron = array();
	$result = false;
	foreach(explode(",", _SPIPLISTES_TAMPON_CLES) as $key) {
		$contexte_patron[$key] = __plugin_lire_key_in_serialized_meta($key, _SPIPLISTES_META_PREFERENCES);
	}
	$f = _SPIPLISTES_PATRONS_TAMPON_DIR.$tampon_patron;
	if (find_in_path($f."_texte.html")){
		$result = recuperer_fond($f, $contexte_patron);
	}
	if(!$result) {
		$result = spiplistes_courrier_version_texte($tampon_html);
	}
	return($result);
}

/*
 * Ajouter les abonnes d'une liste a un envoi
 * @param : $id_courrier : reference d'un envoi
 * @param $id_liste : reference d'une liste
 */
function spiplistes_courrier_remplir_queue_envois ($id_courrier, $id_liste) {
	$id_courrier = intval($id_courrier);
	$id_liste = intval($id_liste);
spiplistes_log("API: remplir courrier $id_courrier, liste : $id_liste", _SPIPLISTES_LOG_DEBUG);
	if(($id_courrier > 0) && ($id_liste > 0)) {
	
		// prendre la liste des abonnés à cette liste
		$sql_result = sql_select('id_auteur'
			, 'spip_auteurs_listes'
			, "id_liste=".sql_quote($id_liste))
			;
	
		// remplir la queue d'envois
		$i = sql_quote($id_courrier);
		$s = sql_quote("a_envoyer");
		$sql_valeurs = "";
		while($row = sql_fetch($sql_result)) {
			$sql_valeurs .= "(".sql_quote(intval($row['id_auteur'])).",$i,$s,NOW()),";
		}
		$sql_valeurs = rtrim($sql_valeurs, ",");
		sql_insert(
			'spip_auteurs_courriers'
			,	"("
				. "id_auteur,id_courrier,statut,maj"
				. ")"
			,	$sql_valeurs
		);
		
		// Compter le nombre de destinaires
		$row = sql_fetch(sql_select(
			"COUNT(id_auteur) AS n"
			, "spip_auteurs_courriers"
			, "id_courrier=".sql_quote($id_courrier)." AND statut=".sql_quote('a_envoyer')
			)
		);
		
		if($row && $row['n']) {
			if(!sql_updateq('spip_courriers'
				, array('total_abonnes' => sql_quote($row['n']))
				, "id_courrier="._q($id_courrier)
				)) {
spiplistes_log("ERR: spiplistes_courrier_remplir_queue_envois($id_courrier, $id_liste) / sql_updateq"
		, _SPIPLISTES_LOG_DEBUG);
				return(false);
			}
		}
		return(true);
	}
spiplistes_log("ERR: spiplistes_courrier_remplir_queue_envois($id_courrier, $id_liste) valeur nulle ?"
		, _SPIPLISTES_LOG_DEBUG);
	return(false);
}

//CP-20080509: upadte sql sur un courrier
function spiplistes_courrier_modifier ($id_courrier, $sql_set_array, $quote = true) {
	$id_courrier = intval($id_courrier);
	$sql_update = $quote ? "sql_updateq" : "sql_update";
	$result = 
		($id_courrier > 0)
		?
			$sql_update(
				"spip_courriers"
				, $sql_set_array
				, "id_courrier=".sql_quote($id_courrier)." LIMIT 1"
			)
		: false
		;
	spiplistes_log("API: modifier courrier #$id_courrier "
		.spiplistes_str_ok_error($result), _SPIPLISTES_LOG_DEBUG);
	return($result);
}

//CP-20080509: changer le statut d'un courrier
function spiplistes_courrier_statut_modifier ($id_courrier, $new_statut) {
	$id_courrier = intval($id_courrier);
	$result = 
		($id_courrier > 0)
		?
			spiplistes_courrier_modifier(
				$id_courrier
				, array('statut' => $new_statut)
			)
		: false
		;
	spiplistes_log("API: Modifier statut courrier #$id_courrier : $new_statut ".spiplistes_str_ok_error($result));
	return($result);
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
				// Sur les précieux conseils de MM :
				// passer la requete en 2 etapes pour assurer portabilite sql
				$selection =
					sql_select("id_courrier", "spip_courriers", $sql_where,'','','','','',false);
				$result = sql_delete("spip_auteurs_courriers", "id_courrier IN ($selection)");
			}
			break;
	}
	return($result);
}

// CP-20080329
function spiplistes_courrier_supprimer ($sql_where_key, $sql_where_value) {
	return(sql_delete("spip_courriers", $sql_where_key."=".sql_quote($sql_where_value)));
}

?>