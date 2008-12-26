<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */


include_spip('inc/forms');
include_spip("inc/charsets");
include_spip("inc/presentation");
include_spip('inc/autoriser');

function csv_champ($champ) {
	$champ = preg_replace(',[\s]+,', ' ', $champ);
	$champ = str_replace(',",', '""', $champ);
	return '"'.$champ.'"';
}

function forms_formater_ligne_csv($ligne,$delim=',') {
	$out = "";
	foreach($ligne as $val){
		if (is_array($val))
			foreach($val as $v) $out .= csv_champ($v).$delim;
		else
			$out .= csv_champ($val).$delim;
	}
	$out = substr($out,0,strlen($out)-strlen($delim))."\r\n";
	return $out;
}

function forms_formater_ligne($ligne,$format,$separateur){
	if (function_exists($f = "forms_formater_ligne_$format"))
		return $f($ligne,$separateur);
	else
		return forms_formater_ligne_csv($ligne,$separateur);
}

function forms_formater_reponse($ligne, $valeurs, $structure,$format,$separateur) {
	// Prendre les differents champs dans l'ordre
	foreach ($structure as $champ => $t) {
		if (!isset($valeurs[$champ])) {
			$ligne[$champ] = "";
		}
		else{
			$v = $valeurs[$champ];
			if ($t['type']=='multiple'){
				// pour un choix multiple on cree une colonne par reponse potentielle, plus une vide avant
				$ligne[$champ][] = "";
				foreach($t['choix'] as $choix=>$titre)
					if (isset($v[$choix]))
						$ligne[$champ][$choix] = $v[$choix];
					else
						$ligne[$champ][$choix] = "";
			}
			else
				$ligne[] = strval(join(', ', $v));
		}
	}
	return forms_formater_ligne($ligne,$format,$separateur);
}

function forms_formater_les_reponses($id_form, $format, $separateur, &$fichiers, &$filename, $head=true, $traduit=true){
	//
	// Telechargement du tableau de reponses au format CSV ou autre
	// le support d'un autre format ne necessite que l'implementation de la fonction
	// forms_formater_ligne_xxx avec xxx le nom du format
	//
	$nb_reponses = sql_countsel("spip_forms_donnees","id_form=".intval($id_form)." AND confirmation='valide' AND statut<>'poubelle'");

	if (!$id_form || !autoriser('administrer','form',$id_form))
		acces_interdit();

	if ($row = sql_fetsel("titre,descriptif,type_form","spip_forms","id_form=".intval($id_form))) {
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
		$type_form = $row['type_form'];
	}

	$filename = preg_replace(',[^-_\w]+,', '_', translitteration(textebrut(typo($titre))));

	$s = '';
	$structure = forms_structure($id_form);
	
	if ($head) {
		// Une premiere ligne avec les noms de champs
		$ligne1 = $ligne2 = array();
		$ligne1[] = $ligne2[] = 'id_donnee';
		$ligne1[] = 'date';
		$ligne2[] = _T("forms:date");
		$ligne1[] = 'url';
		$ligne2[] = _T("forms:page");
		foreach ($structure as $champ => $t) {
			$ligne1[] = $champ;
			$ligne2[] = textebrut(typo($t['titre']));
			if ($t['type']=='multiple'){
				// pour un choix multiple on cree une colonne par reponse potentielle
				$choix = $t['choix'];
				foreach($t['choix'] as $choix=> $v){
					$ligne1[] = $choix;
					$ligne2[] = textebrut(typo($v));
				}
			}
		}
		$s .= forms_formater_ligne($ligne1,$format,$separateur);
		if ($traduit)
			$s .= forms_formater_ligne($ligne2,$format,$separateur);
	}

	// Ensuite les reponses
	$fichiers = array();
	$id_donnee = 0;
	$rows = sql_allfetsel(
	  "r.id_donnee, r.date,r.url, c.champ, c.valeur ",
	  "spip_forms_donnees AS r LEFT JOIN spip_forms_donnees_champs AS c ON r.id_donnee=c.id_donnee",
	  "id_form=".intval($id_form)." AND confirmation='valide' AND statut<>'poubelle' AND c.id_donnee IS NOT NULL ",
	  "",
		"date, r.id_donnee");
	foreach($rows as $row) {
		if ($id_donnee != $row['id_donnee']) {
			if ($id_donnee)
				$s .= forms_formater_reponse($ligne,$valeurs,$structure,$format,$separateur);
			$id_donnee = $row['id_donnee'];
			$date = $row['date'];
			$ligne = array();
			$ligne[] = $id_donnee;
			$ligne[] = jour($date).'/'.mois($date).'/'.annee($date);
			$ligne[] = str_replace("&amp;","&",$row['url']);
			$valeurs = array();
		}
		$champ = $row['champ'];
		if ($structure[$champ]['type'] == 'fichier') {
			$fichiers[] = $row['valeur'];
			$valeurs[$champ][] = 'fichiers/'.basename($row['valeur']);
		}
		else {
			$v = $row['valeur'];
			if (isset($structure[$champ]['choix'][$v])){
				$vt = $v;
				if ($traduit) $vt = $structure[$champ]['choix'][$v];
				$valeurs[$champ][$v] = $vt;
			}
			else{
				if ($traduit AND isset($structure[$champ][$v])) $v = $structure[$champ][$v];
				$valeurs[$champ][] = $v;
			}
		}
	}

	// Ne pas oublier la derniere reponse
	if ($id_donnee)
		$s .= forms_formater_reponse($ligne,$valeurs,$structure,$format,$separateur);
	return $s;
}

?>