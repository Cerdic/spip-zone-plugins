<?php

function chercher_squelette($fond, $id_rubrique, $lang) {
	global $contexte;
	$ext = $GLOBALS['extension_squelette'];

	// Accrocher un squelette de base dans le chemin
	if (!$base = find_in_path("$fond.$ext")) {
		// erreur webmaster : $fond ne correspond a rien
		erreur_squelette(_T('info_erreur_squelette2',
			array('fichier'=>$fond)),
			$dossier);
		return '';
	}
  
	// supprimer le ".html" pour pouvoir affiner par id_rubrique ou par langue
	$squelette = substr($base, 0, - strlen(".$ext"));
  
	$fonds = unserialize(lire_meta('SquelettesMots:fond_pour_groupe'));
	if (is_array($fonds) && (list($id_groupe,$table,$id_table) = $fonds[$fond])) {
		$trouve = false;
		if (($id = $contexte[$id_table]) && ($n = sql_mot_squelette($id,$id_groupe,$table,$id_table))) {
			if ($squel = find_in_path("$fond==$n.$ext")) {
				$squelette = substr($squel, 0, - strlen(".$ext"));
				$trouve = true;
			}
		} 
		if((!$trouve) && ($n = sql_mot_squelette($id_rubrique,$id_groupe,'rubriques','id_rubrique',true))) {	
			if ($squel = find_in_path("$fond-$n.$ext")) {
				$squelette = substr($squel, 0, - strlen(".$ext"));
			}
		}
	}
	// On selectionne, dans l'ordre :
	// fond=10
	$f = "$fond=$id_rubrique";
	if (($id_rubrique > 0) AND ($squel=find_in_path("$f.$ext")))
		$squelette = substr($squel, 0, - strlen(".$ext"));
	else {
		// fond-10 fond-<rubriques parentes>
		while ($id_rubrique > 0) {
			$f = "$fond-$id_rubrique";
			if ($squel=find_in_path("$f.$ext")) {
				$squelette = substr($squel, 0, - strlen(".$ext"));
				break;
			}
			else {
				$id_rubrique = sql_parent($id_rubrique);
             		}
		}
	}

	// Affiner par lang
	if ($lang) {
		lang_select($lang);
		$f = "$squelette.$lang";
		if (@file_exists("$f.$ext"))
		  $squelette = $f;
	}
	
	return $squelette;
}

function sql_mot_squelette($id,$id_groupe,$table,$id_table,$recurse=false) {
	$select1 = array('titre');
	$from1 = array('spip_mots AS mots',
					"spip_mots_$table AS lien");
	while($id > 0) {
  		$where1 = array("$id_table=$id",
						'mots.id_mot=lien.id_mot',
						"id_groupe=$id_groupe");
		$r = spip_abstract_fetch(spip_abstract_select($select1,$from1,$where1));
		if ($r) {
		  include_ecrire("inc_charsets");

		  return transliteration(preg_replace('["\'.] ','_',extraire_multi($r['titre'])));	
		}
		if(!recurse) return '';
		$id = sql_parent($id);
        }
	return '';
}

?>