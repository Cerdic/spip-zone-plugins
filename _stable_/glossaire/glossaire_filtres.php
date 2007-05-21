<?php
/*
 * Glossaire
 * Gestion des listes de definitions techniques
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

function glossaire_ajouter($chaine,$replacenb=-1){
	static $patterns=array();
	static $replace_1=array();
	static $replace_2=array();
	static $ids=array();

	if((strlen($chaine) == 0)||
	    ( (!include_spip('base/forms_base_api') OR !count($liste=Forms_liste_tables('glossaire'))) )
	  )
		return $chaine;
	$chaine=" ".$chaine;

  if ($replacenb!=0)
  {
  	if (!count($patterns)){
			#definition des remplacements
			$set = array();

			#Recuperation des mots et des definitions dans une table des glossaire
			if (count($liste)){
				include_spip('base/abstract_sql');
				$in_form = calcul_mysql_in('id_form',implode(',',$liste));
		  	$res = spip_query("SELECT id_donnee FROM spip_forms_donnees WHERE $in_form ORDER BY id_form,rang");
		  	while ($row = spip_fetch_array($res)){
		  		list($nom,$definition,$survol,$lang) = Forms_les_valeurs($row['id_form'],$row['id_donnee'],array('ligne_1','texte_1','texte_2','select_1'));
		  		if (!isset($set[$nom])){
						$ids[] = $id = $row['id_donnee'];
						$patterns[$id] = $nom;
						if (!strlen($survol))
							$survol = couper($definition,60);
						$replace_1[$id] = $survol;
						$set[$nom] = 1;
		  		}
		  	}
			}
			foreach($ids as $id) {
				$desc_temp = trim(attribut_html($replace_1[$id]));
				//$nom = trim(attribut_html($patterns[$id]));
				$patterns[$id]="/([^\w@\.])(".preg_quote($patterns[$id]).")(?=[\s.-])/";
				$url = generer_url_public('glossaire',"id_donnee=$id");
				$replace_1[$id] = "\\1<a href='$url' title='@D@E@F@$id@@' class='spip_glossaire'>\\2@</a@>";
				$replace_2["@D@E@F@$id@@"] = $desc_temp;
			}
			$replace_2["@</a@>"] = "</a>"; // nettoyer les balises fermantes
			
			#tri necessaire
			//ksort($acro_patterns);
			//ksort($acro_replacements);
  	}

  	if (count($patterns)){
			$pattern = '<([a-z]+[^<>]*)>';
			preg_match_all ('/' . $pattern . '/', $chaine, $xtagMatches, PREG_SET_ORDER);
			$xtextMatches = preg_split ('/' . $pattern . '/', $chaine);
			// on met des glossaire la ou besoin, mais avec un title factice (pour pas en remettre dans le title)
			$xtextMatches = preg_replace($patterns, $replace_1, $xtextMatches ,$replacenb);
			// on met les bons title et on nettoie les balises acronym fermantes
			$xtextMatches = str_replace(array_keys($replace_2), array_values($replace_2), $xtextMatches);
				
			for ($ii = 0; $ii < count ($xtextMatches); $ii ++)
				$xtextMatches [$ii] = $xtextMatches [$ii] . $xtagMatches [$ii] [0];

			$chaine = implode("", $xtextMatches) . $tagMatches [$i][0];
  	}

  }

	return substr($chaine,1);
}
?>