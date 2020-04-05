<?php
include_spip('inc/bible_tableau');
function generer_url_passage_lire($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lire,$lang){
	list($chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$petit) = lire_petit_livre($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang);
	$url = "http://lire.la-bible.net/lecture/$livre/$chapitre_debut/$verset_debut/$lire";
	return $url;
}

function lire_petit_livre($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang){
	//petit livre ?
	$petit_livre=bible_tableau('petit_livre',$lang);
	if (in_array(strtolower($livre),$petit_livre)) {
		
		$verset_debut=$chapitre_debut;
		
		$verset_fin = $chapitre_fin;
		$chapitre_debut = 1;
		$chapitre_fin = 1;
		$petit		= true;
	
	} 
	return array($chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$petit);

}
function recuperer_passage_lire($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lire,$lang){
	$param_cache = array(	'version'=>4,
				'livre'=>$livre,
				'chapitre_debut'=>$chapitre_debut,
				'verset_debut'=>$verset_debut,
				'chapitre_fin'=>$chapitre_fin,
				'verset_fin'=>$verset_fin,
				'lire'=>$lire);
	
	//Vérifions qu'on a pas en cache
	if (_NO_CACHE == 0){
		include_spip('inc/bible_cache');
		$cache = bible_lire_cache($param_cache);
		if ($cache){
			return $cache;	
		}
	}
	list($chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$petit) = lire_petit_livre($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lang);
	
	$tableau_resultat = array();

	//recuperation du passage
	include_spip("inc/distant");
	include_spip("inc/charsets");
	include_spip("inc/querypath");
	$chapitre =	intval($chapitre_debut);
	$chapitre_fin = intval($chapitre_fin);
	while ($chapitre <= $chapitre_fin) {
		// Créer un sous tableau
		$tableau_resultat[$chapitre] = array();

		// recuperer les fichiers distants
		$url = generer_url_passage_lire($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$lire,$lang);
		$code = charset2unicode(importer_charset(recuperer_page($url,'utf-8')));

		//// elagage pour recuperer juste zone_verset
		$tableau = explode("<main", $code);
		$code = "<main".$tableau[1];
		$tableau = explode("</main>", $code);
		$code = $tableau[0]."</main>";

		$qp = htmlqp($code, '', array( 'ignore_parser_warnings'=>true,'omit_xml_declaration'=>true));
		$qp->remove(".chapitre, .titre2,  .titre3,  .titre4");//suppression des intertitres:
		$versets = $qp->find("li[rel=\"$lire\"]  div.zone_versets");
		$versets = $versets->xml(); // le code fournit par lire.la-bible.net est tellement irrégulier que je renonce à parser directement le xml avec query_path, on va refaire d'une manière salle
		$versets = explode('<span class="reference">', $versets);
		unset($versets[0]);
		foreach ($versets as $nb => $texte){
			$tableau = explode('</span></a>', $texte);
			$texte = $tableau[1];
			$texte = strip_tags($texte);
			$texte = preg_replace( "/\r|\n/", " ", $texte);
			$texte = trim($texte);
		// Insérer le texte dans le tableau, si on a demandé ce verset
			if ($chapitre_debut == $chapitre_fin) { // sur un seul chapitre 
				if (
						($nb >= $verset_debut and $nb <= $verset_fin) // verset de fin et verset de debut
						or 
						($verset_debut == $verset_fin and $verset_fin == '') // pas de précision de verset > chapitre complet
				) {
					$tableau_resultat[$chapitre][$nb] = $texte;
				}	
			}	elseif ($chapitre == $chapitre_debut) { // Si plusieurs chapitres, et qu'on parse en ce moment le chapitre de début
					if ($nb >= $verset_debut or $verset_debut == '') {
						$tableau_resultat[$chapitre][$nb] = $texte;
					}	
			} elseif ($chapitre == $chapitre_fin) { // Si plusieurs chapitres, et qu'on parse en ce moment le chapitre de fin {
					if ($nb <= $verset_fin or $verset_fin == '') {
						$tableau_resultat[$chapitre][$nb] = $texte;
					}
			} else { // si plusieurs chapitres et qu'on parse en ce moment un chapitre intermédiaire
					$tableau_resultat[$chapitre][$nb] = $texte;
			}
		}

		// passer au chapitre suivant
		$chapitre++;
	}
	
	if (_NO_CACHE == 0){
		bible_ecrire_cache($param_cache,$tableau_resultat);
	}
	return $tableau_resultat;
}
