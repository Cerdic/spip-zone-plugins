<?php
// extrait d'un contenu tout ce qui ressemble de pres ou de loin a un acronyme
function acronymes_voir($letexte){
  $pattern = '<([a-zA-Zéàèçùûôâîê0-9\. \s"\'_\/\-\+=;,!:@~\(\)\?&#%\n\[\]]+)>';
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
 *    Nom du Filtre : ajouter_acronymes
 *   +----------------------------------+
 *   Publié le 30/11/2004
 *   Par Thomas Houssin <Thomas point Houssin at gmail.com>
 *   Modifié le 2/08/2005 par Cedric MORIN
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
	
	if((strlen($chaine) == 0)||(!$id_rubrique_acronymes && !$id_syndic_acronymes))
		return $chaine;
	$chaine=" ".$chaine;

  if ($replacenb!=0)
  {
  	if (!count($acro_patterns)){
			#définition des remplacements
			$set = array();

			#Récupération des mots et des définitions dans une rubrique locale
	  	$res = spip_query("SELECT titre,descriptif FROM spip_articles WHERE id_rubrique=".spip_abstract_quote($id_rubrique_acronymes));
			while($row = spip_fetch_array($res)){
				$accro=str_replace(".","",$row['titre']);
				if (!isset($set[$accro])){
					$acro_patterns[] = $accro;
					$acro_replacements[] = $row['descriptif'];
					$set[$accro] = 1;
				}
			}
			
			#Récupération des mots et des définitions dans un site syndique distant
	  	$res = spip_query("SELECT titre,descriptif FROM spip_syndic_articles WHERE id_syndic=".spip_abstract_quote($id_syndic_acronymes));
			while($row = spip_fetch_array($res)){
				$accro=str_replace(".","",$row['titre']);
				if (!isset($set[$accro])){
					$acro_patterns[] = $accro;
					$acro_replacements[] = $row['descriptif'];
					$set[$accro] = 1;
				}
			}
			unset($set);
	  	
			foreach($acro_patterns as $key=>$accro)
			{
				$desc_temp = trim(attribut_html(supprimer_tags($acro_replacements[$key])));
				$desc_temp = str_replace("'","&#039;",$desc_temp);
				$pattern="{([^\w@\.])(";
				for ($i=0;$i<strlen($accro);$i++)
					$pattern.=$accro{$i}."[\.]?";
				$pattern.=")(?![\w@])}";
				$acro_patterns[$key] = $pattern;
				$acro_replacements[$key] = "\\1<acronym title='@A@C@R@O@$key@@'>\\2@</acronym>";
				$acro_step2["@A@C@R@O@$key@@"] = $desc_temp;
			}
			$acro_step2["@</acronym>"] = "</acronym>"; // nettoyer les balises fermantes
			
			#tri nécessaire
			ksort($acro_patterns);
			ksort($acro_replacements);
  	}

  	if (count($acro_patterns)){
			// reperage des balises acronym existantes
			$pattern="{<acronym[^>]*>([^<]*)</acronym>}";
			preg_match_all ($pattern, $chaine, $tagMatches, PREG_SET_ORDER);
			$textMatches = preg_split ($pattern, $chaine);
 			//$textMatches = preg_replace($patterns, $replacements, $textMatches ,$replacenb);

		  for ($i = 0; $i < count ($textMatches); $i ++){
		    $texte=$textMatches[$i];
			  $pattern = '<([a-zA-Zéàèçùûôâîê0-9\. \s"\'_\/\-\+=;,!:@~\(\)\?&#%\n\[\]]+)>';
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