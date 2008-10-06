<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| BOUCLE et BALISES 
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

//
// La BOUCLE DW2_DOC
//
function boucle_DW2_DOC($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_dw2_doc";
	$mstatut = $id_table .'.statut';
	
	$boucle->from["documents"] =  "spip_documents";
	
	$boucle->select[] = 'documents.taille';
	$boucle->select[] = 'documents.titre';
	$boucle->select[] = 'documents.descriptif';
	#$boucle->select[] = 'documents.id_document';
	$boucle->where[] = array("'='", "'$id_table.id_document'", "'documents.id_document'");
	// uniquement doc "actifs"
	$boucle->where[]= array("'='", "'$mstatut'", "'\"actif\"'");
	
	/*h.28/01/07 correctif !! oups */
	
	return calculer_boucle($id_boucle, $boucles);
}



//
// BALISEs
//


// Balise de redirection
function generer_url_doc_out($_id_document) {
	# h.17/11/06 problème de formation de l'url dans backend -> erreur xml
	# $ret_bal = "?action=dw2_out&id=$_id_document"; #
	$ret_bal = generer_url_action('dw2_out', 'id='.$_id_document, true);
	return $ret_bal;
}
function balise_URL_DOC_OUT($p) {
	$p->code = "generer_url_doc_out(" . champ_sql('id_document',$p) . ")";
	$p->interdire_scripts = false;	
	return $p;
}



// balise Nom => Nom de la fiche DW2 
function balise_NOM_DOC($p) {
	$p->code = champ_sql('nom', $p);
	$p->interdire_scripts = false;
	return $p;
}


// balise fichier_doc => Nom du fichier
function generer_fichier_doc($url_fichier) {
$nom_fichier = substr(strrchr($url_fichier,'/'), 1);
return $nom_fichier;
}
function balise_FICHIER_DOC($p) {
	$p->code = "generer_fichier_doc(".champ_sql('url', $p).")";
	#$p->interdire_scripts = true;
	return $p;
}


// balise dateur (date dern. telech)
// devient obso voir ci-dessous
function balise_DATEUR ($p) {
	$_date = champ_sql('dateur', $p);
	$p->code = "vider_date($_date)";
	$p->interdire_scripts = false;
	return $p;
}

// balise dateur (date dern. telech)
// a terme rend obsolete la precedente !! juste pour le fun !
function balise_DATEUR_DOC ($p) {
	$_date = champ_sql('dateur', $p);
	$p->code = "vider_date($_date)";
	$p->interdire_scripts = false;
	return $p;
}


// Balise taille (spip_documents champ taille)
function balise_TAILLE_DOC($p) {
	$p->code = '$Pile[$SP][\'taille\']';
	$p->interdire_scripts = false;
	return $p;
}


// Balise titre (spip_documents champ titre)
function balise_TITRE_DOC($p) {
	$p->code = 'interdire_scripts(typo($Pile[$SP][\'titre\']))';
	#$p->interdire_scripts = true;
	return $p;
}


// Balise descriptif (spip_documents champ descriptif)
function balise_DESCRIPTIF_DOC($p) {
	$p->code = 'interdire_scripts(typo($Pile[$SP][\'descriptif\']))';
	#$p->interdire_scripts = true;
	return $p;
}


// Balise total_doc (champ total)
function balise_TOTAL_DOC($p) {
	$p->code = champ_sql('total',$p);
	$p->interdire_scripts = false;
	return $p;
}


// Balise du type de conteneur : Affiche : Article ou Rubrique
function balise_CONT_DOC($p) {
	$p->code = champ_sql('doctype', $p);
	$p->interdire_scripts = false;
	return $p;
}


// balise ID du conteneur
function balise_ID_CONT_DOC($p) {
	$p->code = champ_sql('id_doctype', $p);
	$p->interdire_scripts = false;
	return $p;
}


// balise TITRE du cont. : titre article...
function generer_tt_cont_doc($cont_type,$cont_id) {
	$query=spip_query("SELECT titre FROM spip_".$cont_type."s WHERE id_".$cont_type." = $cont_id");
	$row=spip_fetch_array($query);
	$titre=supprimer_numero($row['titre']);
	return $titre;
}
function balise_TITRE_CONT_DOC($p) {
	$cont_type = champ_sql('doctype', $p);
	$cont_id = champ_sql('id_doctype', $p);
	$p->code = "generer_tt_cont_doc($cont_type,$cont_id)";
	$p->interdire_scripts = false;
	return $p;
}


// balise URL du conteneur
function generer_url_cont_doc($_cont_type,$_cont_id) {
	if ($_cont_type=='article')
		return generer_url_article($_cont_id) ;
	if ($_cont_type=='rubrique')
		return generer_url_rubrique($_cont_id) ;
}
function balise_URL_CONT_DOC($p) {
	$_cont_type = champ_sql('doctype', $p);
	$_cont_id = champ_sql('id_doctype', $p);
	$p->code = "generer_url_cont_doc($_cont_type,$_cont_id)" ;
	#$p->interdire_scripts = true;
	return $p;
}


// balise total des téléchargements du site
function generer_total_compteur_actif() {
	$query=spip_query("SELECT SUM(total) AS tac FROM spip_dw2_doc WHERE statut='actif'");
	$row=spip_fetch_array($query);
	return $row['tac'];
}
function balise_TOTAL_DOC_SITE($p) {
	$p->code = "generer_total_compteur_actif()";
	$p->interdire_scripts = false;
	return $p;
}


// balise LOGO (copie/modif function spip 1.8.2 .. h10/7 a reviser 1.9 !
function balise_LOGO_DOC($p) {
$_id_objet = champ_sql('id_document', $p);
// analyser les faux filtres, 
	// supprimer ceux qui ont le tort d'etre vrais
	$flag_fichier = 0;
	$filtres = '';
	if (is_array($p->fonctions)) {
		foreach($p->fonctions as $couple) {
			// eliminer les faux filtres
			if (!$flag_stop) {
				array_shift($p->param);
				$nom = $couple[0];
				if (ereg('^(left|right|center|top|bottom)$', $nom))
					$align = $nom;
				else if ($nom == 'lien') {
					$flag_lien_auto = 'oui';
					$flag_stop = true;
				}
				else if ($nom == 'fichier') {
					$flag_fichier = 1;
					$flag_stop = true;
				}
				// double || signifie "on passe aux filtres"
				else if ($nom == '') {
					if (!$params = $couple[1])
						$flag_stop = true;
				}
				else if ($nom) {
					$lien = $nom;
					$flag_stop = true;
				} else {
					
				}
			}
			// apres un URL ou || ou |fichier ce sont
			// des filtres (sauf left...lien...fichier)
		}
	}

	//
	// Preparer le code du lien
	//
	// 1. filtre |lien
	if ($flag_lien_auto AND !$lien)
		$code_lien = '($lien = generer_url_doc_out('.$_id_objet.')) ? $lien : ""';
	// 2. lien indique en clair (avec des balises : imprimer#ID_ARTICLE.html)
	else if ($lien) {
		$code_lien = "'".texte_script(trim($lien))."'";
		while (ereg("^([^#]*)#([A-Za-z_]+)(.*)$", $code_lien, $match)) {
			$c = new Champ();
			$c->nom_champ = $match[2];
			$c->id_boucle = $p->id_boucle;
			$c->boucles = &$p->boucles;
			$c->descr = $p->descr;
			$c = calculer_champ($c);
			$code_lien = str_replace('#'.$match[2], "'.".$c.".'", $code_lien);
		}
		// supprimer les '' disgracieux
		$code_lien = ereg_replace("^''\.|\.''$", "", $code_lien);
	}

	if ($flag_fichier)
		$code_lien = "'',''" ; 
	else {
		if (!$code_lien)
			$code_lien = "''";
		$code_lien .= ", '". addslashes($align) . "'";
	}
	
		$p->code = "calcule_logo_document($_id_objet, '" .
			$p->descr['documents'] .
			'\', $doublons, '. intval($flag_fichier).", $code_lien, '".
			// #LOGO_DOCUMENT{x,y} donne la taille maxi
			texte_script($params)
			."')";

$p->interdire_scripts = false;
return $p;
}



?>
