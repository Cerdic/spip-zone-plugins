<?php
// extrait d'un contenu tout ce qui ressemble de pres ou de loin a un acronyme
function acronymes_voir($letexte){
  $pattern = '<([a-zA-Zיאטחשûפגמך0-9\. \s"\'_\/\-\+=;,!:@~\(\)\?&#%\n\[\]]+)>';
  $textMatches = preg_split ('/' . $pattern . '/', $letexte);
  $textMatches = implode(" ",$textMatches);

	preg_match_all("{[^A-Za-z\.](([A-Z](\.)?)([A-Z0-9](\.)?){1,7})(?![A-Za-z0-9\.])}",$textMatches,$matches,PREG_PATTERN_ORDER);
	$out=implode(" ",$matches[1])." ";
	preg_match_all("{^(([A-Z](\.)?)([A-Z0-9](\.)?){1,7})(?![A-Za-z0-9\.])}",$textMatches,$matches,PREG_PATTERN_ORDER);
	$out.=implode(" ",$matches[1])." ";
	return $out;
}

// traite les raccourcis de la forme [SNCF|societe nationale...]
function acronymes_traiter_raccourcis($letexte){
	$pattern="{\[([^\|\]-]+)\|([^\|\]-]+)\]}";
  preg_match_all ($pattern, $letexte, $tagMatches, PREG_SET_ORDER);
  $textMatches = preg_split ($pattern, $letexte);

	$tag_attr=array();
  foreach ($tagMatches as $key => $value) {
		$tag_attr[]="<acronym title='".texte_backend($value[2])."'>".$value[1]."</acronym>";
  }
  for ($i = 0; $i < count ($textMatches); $i ++) {
   $textMatches [$i] = $textMatches [$i] . $tag_attr [$i];
  }

  return implode ("", $textMatches);

}

/*
 *   +----------------------------------+
 *    Nom du Filtre : acronymes_ajouter
 *   +----------------------------------+
 *   fonction originale publiee le 30/11/2004 par Thomas Houssin <Thomas point Houssin at gmail.com>
 *
 */

function acronymes_ajouter($chaine,$replacenb=-1)
{
	static $acro_patterns=array();
	static $acro_replacements=array();
	static $acro_step2=array();

	$id_rubrique_acronymes = 0;
	if (isset($GLOBALS['meta']['acronymes_id_syndic'])){
		$id_syndic_acronymes=$GLOBALS['meta']['acronymes_id_syndic'];
		if (isset($GLOBALS['meta']['acronymes_rubrique_locale_active'])&&($GLOBALS['meta']['acronymes_rubrique_locale_active']!='non')){
			$id_rubrique_acronymes=$GLOBALS['meta']['acronymes_rubrique_locale_active'];
		}
	}
	else
		$id_syndic_acronymes = 0;

	$id_syndic_acronymes=intval($id_syndic_acronymes);
	$id_rubrique_acronymes=intval($id_rubrique_acronymes);
	
	if((strlen($chaine) == 0)||
		  ( (!include_spip('base/forms_base_api') OR !count($liste=Forms_liste_tables('acronymes_sigles')))
		    && !$id_rubrique_acronymes && !$id_syndic_acronymes
		  )
		)
		return $chaine;
	$chaine=" ".acronymes_traiter_raccourcis($chaine);

  if ($replacenb!=0)
  {
  	if (!count($acro_patterns)){
			#definition des remplacements
			$set = array();

			#Recuperation des mots et des definitions dans une table des acronymes
			if (count($liste)){
				include_spip('forms_fonctions');
				$id_form = intval(reset($liste));
		  	$res = spip_query("SELECT id_donnee FROM spip_forms_donnees WHERE id_form="._q($id_form)." AND statut='publie'");
		  	while ($row = spip_fetch_array($res)){
		  		$accro=str_replace(".","",forms_calcule_les_valeurs('forms_donnees_champs', $row['id_donnee'], 'ligne_1', $id_form,' ', true));
		  		$desc = forms_calcule_les_valeurs('forms_donnees_champs', $row['id_donnee'], 'texte_1', $id_form,' ', true);
		  		$balise = forms_calcule_les_valeurs('forms_donnees_champs', $row['id_donnee'], 'select_1', $id_form,' ', true);
		  		$lang = forms_calcule_les_valeurs('forms_donnees_champs', $row['id_donnee'], 'select_2', $id_form,' ', true);
					$acro_patterns[] = $accro;
					$acro_replacements[] = $desc;
					$acro_balise[] = $balise;
					$set[$accro] = 1;
		  	}
			}
			#Recuperation des mots et des definitions dans une rubrique locale
			if ($id_rubrique_acronymes) {
		  	$res = spip_query("SELECT titre,descriptif FROM spip_articles WHERE id_rubrique=".spip_abstract_quote($id_rubrique_acronymes));
				while($row = spip_fetch_array($res)){
					$accro=str_replace(".","",$row['titre']);
					if (!isset($set[$accro])){
						$acro_patterns[] = $accro;
						$acro_replacements[] = $row['descriptif'];
						$acro_balise[] = 'acronym';
						$set[$accro] = 1;
					}
				}
			}
			#Recuperation des mots et des definitions dans un site syndique distant
			if ($id_syndic_acronymes) {
		  	$res = spip_query("SELECT titre,descriptif FROM spip_syndic_articles WHERE id_syndic=".spip_abstract_quote($id_syndic_acronymes));
				while($row = spip_fetch_array($res)){
					$accro=str_replace(".","",$row['titre']);
					if (!isset($set[$accro])){
						$acro_patterns[] = $accro;
						$acro_replacements[] = $row['descriptif'];
						$acro_balise[] = 'acronym';
						$set[$accro] = 1;
					}
				}
			}
			unset($set);
	  	
			foreach($acro_patterns as $key=>$accro)
			{
				$desc_temp = trim(attribut_html(supprimer_tags($acro_replacements[$key])));
				$desc_temp = supprimer_tags(str_replace("'","&#039;",$desc_temp));
				$pattern="{([^\w@\.]|^)($accro|";
				for ($i=0;$i<strlen($accro);$i++)
					$pattern.=$accro{$i}."\.";
				$pattern.="?)(?=([^\w]|\s|&nbsp;|$))}";
				$acro_patterns[$key] = $pattern;
				$balise = $acro_balise[$key];
				$acro_replacements[$key] = "\\1<$balise title='@A@C@R@O@$key@@'>\\2@</$balise>";
				$acro_step2["@A@C@R@O@$key@@"] = $desc_temp;
			}
			$acro_step2["@</acronym>"] = "</acronym>"; // nettoyer les balises fermantes
			$acro_step2["@</abbr>"] = "</abbr>"; // nettoyer les balises fermantes
			
			#tri necessaire
			ksort($acro_patterns);
			ksort($acro_replacements);

  	}
  	if (count($acro_patterns)){
			// reperage des balises acronym existantes
			$pattern="{<(?:acronym|abbr)[^>]*>([^<]*)</(?:acronym|abbr)>}";
			preg_match_all ($pattern, $chaine, $tagMatches, PREG_SET_ORDER);
			$textMatches = preg_split ($pattern, $chaine);
 			//$textMatches = preg_replace($patterns, $replacements, $textMatches ,$replacenb);

		  for ($i = 0; $i < count ($textMatches); $i ++){
		    $texte=$textMatches[$i];
			  $pattern = '<([a-z]+[^<>]*)>';
			  preg_match_all ('/' . $pattern . '/', $texte, $xtagMatches, PREG_SET_ORDER);
			  $xtextMatches = preg_split ('/' . $pattern . '/', $texte);
			  // on met des acronymes la ou besoin, mais avec un title factice (pour pas en remettre dans le title)
 				$xtextMatches = preg_replace($acro_patterns, $acro_replacements, $xtextMatches ,$replacenb);
 				// on met les bons title et on nettoie les balises acronym fermantes
 				$xtextMatches = str_replace(array_keys($acro_step2), array_values($acro_step2), $xtextMatches);
 				
			  for ($ii = 0; $ii < count ($xtextMatches); $ii ++)
	 				$xtextMatches [$ii] = $xtextMatches [$ii] . $xtagMatches [$ii] [0];

			  $textMatches[$i] = implode("", $xtextMatches) . $tagMatches [$i][0];
			}
			$chaine = implode ("", $textMatches);
  	}

  }

	return substr($chaine,1);
}
?>
