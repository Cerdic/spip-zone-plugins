<?php


function definitions_mots($texte) {


			
					
					$result = spip_query("SELECT * from spip_mots WHERE texte!=\"\" OR descriptif!=\"\"");
					
					while( $row = spip_fetch_array($result))
					{
					
						$mot = html_entity_decode($row['titre']);
						$definition = substr($row['descriptif']." ".$row['texte'], 0, 150);
						$url_mot = $row['url_propre'];
						$id_mot = $row['id_mot'];
						if (strpos($texte, strtolower($mot)) != FALSE)
						{
							//mot en minuscule
							$patterns[0] = " ".strtolower($mot);
							$replacements[0] = " <a class=\"mot\" style=\"display: inline;\" href=\"spip.php?page=mot&amp;id_mot=$id_mot\" onclick=\"javascript:afficher_definition('def-$id_mot'); return false;\" >".strtolower($mot)."</a><img class=\"mot_afficher\" src=\""._DIR_PLUGINS."definitions_mots/definition.png\" alt=\"[?]\" />";
							$texte = str_replace($patterns, $replacements, $texte);
							$texte = "<div class=\"defdiv\" style=\"display: none;\" id=\"def-$id_mot\"><a href=\"javascript:fermer_definition('def-$id_mot')\" title=\"fermer\"><img class=\"bouton-fermer\" src=\""._DIR_PLUGINS."definitions_mots/fermer.gif\" alt=\"fermer\" /></a>D&eacute;finition du mot <b>".strtolower($mot)."</b> :<br/>$definition<br/><a href=\"spip.php?page=mot&amp;id_mot=$id_mot\" title=\"En savoir plus\">En savoir plus...</a></div>".$texte;
								
						}
						if (strpos($texte, ucfirst(strtolower($mot))) != FALSE)
						{
							//première lettre majuscule
							$patterns[0] = " ".ucfirst(strtolower($mot));
							$replacements[0] = " <a class=\"mot\" style=\"display: inline;\" href=\"spip.php?page=mot&amp;id_mot=$id_mot\" onclick=\"javascript:afficher_definition('def-$id_mot'); return false;\" >".ucfirst(strtolower($mot))."</a><img class=\"mot_afficher\" src=\""._DIR_PLUGINS."definitions_mots/definition.png\" alt=\"[?]\" />";
							$texte = str_replace($patterns, $replacements, $texte);
							$texte = "<div class=\"defdiv\" style=\"display: none;\" id=\"def-$id_mot\"><a href=\"javascript:fermer_definition('def-$id_mot')\" title=\"fermer\"><img class=\"bouton-fermer\" src=\""._DIR_PLUGINS."definitions_mots/fermer.gif\" alt=\"fermer\" /></a>D&eacute;finition du mot <b>".ucfirst(strtolower($mot))."</b> :<br/>$definition<br/><a href=\"spip.php?page=mot&amp;id_mot=$id_mot\" title=\"En savoir plus\">En savoir plus...</a></div>".$texte;
						
						}
						if (strpos($texte, strtoupper($mot)) != FALSE)
						{
							//mot en majuscule
							$patterns[0] = " ".strtoupper($mot);
							$replacements[0] = " <a class=\"mot\" style=\"display: inline;\" href=\"spip.php?page=mot&amp;id_mot=$id_mot\" onclick=\"javascript:afficher_definition('def-$id_mot'); return false;\" >".strtoupper($mot)."</a><img class=\"mot_afficher\" src=\""._DIR_PLUGINS."definitions_mots/definition.png\" alt=\"[?]\" />";
							$texte = str_replace($patterns, $replacements, $texte);
							$texte = "<div class=\"defdiv\" style=\"display: none;\" id=\"def-$id_mot\"><a href=\"javascript:fermer_definition('def-$id_mot')\" title=\"fermer\"><img class=\"bouton-fermer\" src=\""._DIR_PLUGINS."definitions_mots/fermer.gif\" alt=\"fermer\" /></a>D&eacute;finition du mot <b>".strtoupper($mot)."</b> :<br/>$definition<br/><a href=\"spip.php?page=mot&amp;id_mot=$id_mot\" title=\"En savoir plus\">En savoir plus...</a></div>".$texte;
						}
						
					}

 	
	return $texte;
	
}


?>
