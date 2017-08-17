<?php

function multidomaines_pre_liens($texte) {
	// uniquement dans le public
	if (test_espace_prive()) return $texte;
	$regs = $match = array();
	// pour chaque lien
	if (preg_match_all(_RACCOURCI_LIEN, $texte, $regs, PREG_SET_ORDER)) {
		foreach ($regs as $reg) {
			//on épluche chaque chaine de la forme raccourciXXX 
			if($objet=typer_raccourci($reg[4]) ) {
				$objet_num=$objet[2]; //n° de l'objet
				$objet_type=substr($objet[0],0,3); //donne le type de l'objet sur 3 lettre (art, rub ...)
				$objet_chaine=$objet[1].$objet[2]; // de la forme racXXX
			} else {
				$objet_num=$objet_type=$objet_chaine=NULL;
			}
			if ($objet_type=='rub') {
				$id_secteur = sql_getfetsel("id_secteur", "spip_rubriques", "id_rubrique=" . intval($objet_num));
				$url_multidom = lire_config('multidomaines/editer_url_' .$id_secteur);
				if(!$url_multidom) $url_multidom = lire_config('multidomaines/editer_url');
				$url_spip = generer_url_entite_absolue($objet_num, 'rubrique');
				//on va comparer les domaines dans l'url donné par spip et celui théorique
				$domaine_spip=explode('/', $url_spip);
				$domaine_multidom=explode('/', $url_multidom);
				//si différents on fait le remplacement du genre rubrique158 par son url corrigé d'après config multidomain
				if ($domaine_spip[2]!=$domaine_multidom[2] and !is_null($domaine_multidom[2])) {
					//si texte lien vide
					if (!$reg[1]) {
						$objet_titre=generer_info_entite($objet_num, 'rubrique', 'titre');
						$remplacerca=$reg[0];
						$parca='['.$objet_titre.'->'.str_ireplace($domaine_spip[2], $domaine_multidom[2], $url_spip).']';
						// Dans un mes_options mettre 1 ou plus pour avoir des url_secteur au lieu des url_rubriques_absolu
						if (_SECTEUR_URL!='0') { if($id_secteur==$objet_num) $parca='['.$objet_titre.'->'.$url_multidom.']'; }
					} else {
						$remplacerca='>'.$objet_chaine.']';
						$parca='>'.str_ireplace($domaine_spip[2], $domaine_multidom[2], $url_spip).']';
						// Dans un mes_options mettre 1 ou plus pour avoir des url_secteur au lieu des url_rubriques_absolu
						if (_SECTEUR_URL!='0') { if($id_secteur==$objet_num) $parca='>'.$url_multidom.']'; }
					}
					$texte=str_replace($remplacerca, $parca, $texte);
				}
			} elseif ($objet_type=='art') {
				$id_secteur = sql_getfetsel("id_secteur", "spip_articles", "id_article=" . intval($objet_num));
				$url_multidom = lire_config('multidomaines/editer_url_' .$id_secteur);
				if(!$url_multidom) $url_multidom = lire_config('multidomaines/editer_url');
				$url_spip = generer_url_entite_absolue($objet_num, 'article');
				$domaine_spip=explode('/', $url_spip);
				$domaine_multidom=explode('/', $url_multidom);
				if ($domaine_spip[2]!=$domaine_multidom[2] and !is_null($domaine_multidom[2])) {
					if (!$reg[1]) {
						$objet_titre=generer_info_entite($objet_num, 'article', 'titre');
						$remplacerca=$reg[0];
						$parca='['.$objet_titre.'->'.str_ireplace($domaine_spip[2], $domaine_multidom[2], $url_spip).']';
					} else {
						$remplacerca='>'.$objet_chaine.']';
						$parca='>'.str_ireplace($domaine_spip[2], $domaine_multidom[2], $url_spip).']';
					}
					$texte=str_replace($remplacerca, $parca, $texte);
				}
			}
		}
	}
	return $texte;
}
?>
