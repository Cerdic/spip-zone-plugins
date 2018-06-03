<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function boucle_HIERARCHIE_CHAPITRES_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table . ".id_chapitre";

	// Si la boucle mere est une boucle RUBRIQUES il faut ignorer la feuille
	// sauf en presence du critere {tout} (vu par phraser_html)
	// ou {id_article} qui positionne aussi le {tout}

	$boucle->hierarchie = 'if (!($id_chapitre = intval('
	. calculer_argument_precedent($boucle->id_boucle, 'id_chapitre', $boucles)
	. ")))\n\t\treturn '';\n\t"
	. '$hierarchie = '
	. (isset($boucle->modificateur['tout']) ? '",$id_chapitre"' : "''")
	. ";\n\t"
	. 'while ($id_chapitre = sql_getfetsel("id_parent","spip_chapitres","id_chapitre=" . $id_chapitre,"","","", "", $connect)) { 
		$hierarchie = ",$id_chapitre$hierarchie";
	}
	if (!$hierarchie) return "";
	$hierarchie = substr($hierarchie,1);';
	
	// On enlève l'ancien critère "id_chapitre" du where
	foreach ($boucle->where as $cle=>$where){
		if (($where[0] == "'='" or $where[0] == '"="') and ($where[1] == "'$id_table'" or $where[1] == '"'.$id_table.'"')){
			unset($boucle->where[$cle]);
		}
	}
	$boucle->where[]= array("'IN'", "'$id_table'", '"($hierarchie)"');

        $order = "FIELD($id_table, \$hierarchie)";
	if (!isset($boucle->default_order[0]) OR $boucle->default_order[0] != " DESC")
		$boucle->default_order[] = "\"$order\"";
	else
		$boucle->default_order[0] = "\"$order DESC\"";
	return calculer_boucle($id_boucle, $boucles); 
}

function calculer_hierarchie_chapitre($id_chapitre, $tout=true, $connect=''){
	$hierarchie = array();
	
	if ($id_chapitre > 0){
		if ($tout) array_push($hierarchie, $id_chapitre);
		while ($id_chapitre = sql_getfetsel('id_parent', 'spip_chapitres', 'id_chapitre='.$id_chapitre, '', '', '', '', $connect)){
			array_push($hierarchie, $id_chapitre);
		}
		if (!$hierarchie) return array();
	}
	
	return $hierarchie;
}

/*
 * Un critère {branche ?} ou {branche #ID_CHAPITRE} spécifique aux CHAPITRES
 * Si on demande 0 alors on enlève le critère car ça veut dire tout
 */
function critere_CHAPITRES_branche_dist($idb, &$boucles, $crit){
	$not = $crit->not;
	$boucle = &$boucles[$idb];
	if (isset($crit->param[0])){
		$arg = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	}
	else
		$arg = calculer_argument_precedent($idb, 'id_chapitre', $boucles);
	
	$cle = $boucle->id_table;
	
	$c = "($arg>0) ? sql_in('$cle.id_parent', calcul_branche_in_chapitres($arg".(isset($boucle->modificateur['tout'])?",true":"").")"
	     .($not ? ", 'NOT'" : '').") : '1=1'";
	$boucle->where[] = !$crit->cond ? $c :
		("($arg ? $c : ".($not ? "'0=1'" : "'1=1'").')');
}

function calcul_branche_in_chapitres($id, $id_inclus=false){
	static $b = array();

	// normaliser $id qui a pu arriver comme un array, comme un entier, ou comme une chaine NN,NN,NN
	if (!is_array($id)) $id = explode(',',$id);
	$id = join(',', array_map('intval', $id));
	$i = $id.strval($id_inclus);
	if (isset($b[$i]))
		return $b[$i];

	// Notre branche commence par la rubrique de depart
	$branche = $r = $id;
	
	// On ajoute une generation (les filles de la generation precedente)
	// jusqu'a epuisement
	while ($filles = sql_allfetsel(
		'id_chapitre',
		'spip_chapitres',
		sql_in('id_parent', $r)." AND ". sql_in('id_chapitre', $r, 'NOT')
	)) {
		$r = join(',', array_map('array_shift', $filles));
		$branche .= ',' . $r;
	}
	
	if ($id_inclus){
		$branche .= ','.$id;
	}

	# securite pour ne pas plomber la conso memoire sur les sites prolifiques
	if (strlen($branche)<10000)
		$b[$i] = $branche;
	return $branche;
}


/**
 * Remplacer les intertitres dans le texte d'un chapitre par une autre balise avec une classe sémantique
 *
 * Dans les chapitres, la hiérarchie des titres est régie par leur imbrication,
 * il ne faut donc pas de vrais intertitres dans le texte.
 *
 * Par exemple, <h3> devient <div class="hn"> en fonction de la profondeur du chapitre et du niveau de heading des chapitres à la racine.
 *
 * @example
 * [(#TEXTE|chapitres_remplacer_intertitres{#GET{profondeur},2})]
 *
 * @param string $texte
 *    Texte sans traitement typo
 * @param int|string $profondeur
 *    Profondeur du chapitre
 *    0 = racine
 * @param int|string $niveau_racine
 *    Numéro du heading le plus haut (ceux des chapitres à la racine)
 *    Ex. : <h1> = 1, <h2> = 2, etc.
 *    Par défaut 2
 */
function chapitres_remplacer_intertitres($texte, $profondeur, $niveau_racine = 2) {

	if (strlen(trim($texte))) {
		// DOMDocument plutôt qu'une regex car plus fiable (ignorer commentaires, styles inline etc.).
		libxml_use_internal_errors(true);
		$dom = new DOMDocument;
		$dom->loadHTML(
			mb_convert_encoding($texte, 'HTML-ENTITIES', 'UTF-8'),
			LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
		);

		// On repère les intertitres et on note les niveaux présents.
		$niveaux_intertitres = array();
		for ($i = 1; $i <= 6; $i++) {
			if ($dom->getElementsByTagName("h$i")->item(0)) {
				$niveaux_intertitres[] = $i;
			}
		}

		if (count($niveaux_intertitres)) {

			// Niveau du chapitre actuel, à partir duquel on descend
			$niveau_chapitre = $niveau_racine + $profondeur;

			foreach ($niveaux_intertitres as $n) {
				// Définir le nouveau niveau
				$delta = $n - min($niveaux_intertitres);
				$niveau = $niveau_chapitre + 1 + $delta;
				$niveau = min($niveau, 6); // Limiter à .h6
				// Remplacer les intertitres
				// Boucle en arrière, cf. 
				$intertitres = $dom->getElementsByTagName("h$n");
				for ($i = $intertitres->length - 1; $i >= 0; $i--) {
					$avant = $intertitres->item($i);
					$apres = $dom->createElement('div', $avant->nodeValue);
					$apres->setAttribute('class', "spip h$niveau");
					$avant->parentNode->replaceChild($apres, $avant);
				}
			}

			$texte = $dom->saveHTML();

		}
	}

	return $texte;
}