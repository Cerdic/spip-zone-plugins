<?php
# Config
	# L'identifiant (id_rubrique) de la rubrique glossaire
	$id_rubrique = ; /* indiquez ici le numéro de rubrique de votre glossaire */
	
/*
 *   +----------------------------------+
 *    Nom du Filtre : Glossaire interne                                               
 *   +----------------------------------+
 *    Date : jeudi 11 septembre 2003
 *    Auteur :  François Schreuer <francois (sur) schreuer (point) org>
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *    Gestion des liens vers un glossaire interne à un site
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.spip-contrib.net/article241.html
*/

# Remplace seulement la première occurence. Mêmes arguments que str_replace
# Cette fonction est inspirée d'une fonction trouvée à l'adresse http://www.phpapps.org/index.php?action=sources&go=voir_source&id=174 (qui toutefois contenait un bug lorsque la chaîne recherchée ne se trouvait pas dans le texte
function first_replace($c,$r,$t)
{
	if(strstr($t,$c))
	{
		$d = str_replace(strstr($t,$c),"",$t);
		$f = strstr($t,$c);
		$f = substr($f,strlen($c));
		return $d . $r . $f;
	}
	else
		return $t;
}

# Crée des liens vers le glossaire
function lier_glossaire($texte)
{
	

	/* parcourt la base de donne pour faire un tableau $r des titres */
	$r = spip_query("SELECT id_article,titre,descriptif FROM spip_articles WHERE statut='publie' AND id_rubrique='$id_rubrique'");
	
	/*cree le motif que l'on va retirer : tout ce qui est entre <a et </a> */
	$search= '@<a[^>]*?>.*?</a>@msi';
	
	/* je sauvegarde mes chaines avant de les enlever */
	preg_match_all ($search, $texte, $tagMatches);
	
	/* je crée une chaine que je vais mettre aux endroits ou il y a des liens */
	$replace = "#MaChaine#";
	
   /* je sors les uris et le texte qui y est inclus et je les remplace par $replace */
	$texte = preg_replace($search, $replace, $texte);
	
	while($o = spip_fetch_array($r))
	{
			$descriptif= $o[descriptif];
			$texte = first_replace(" $o[titre] "," <a href=\"?article=".$o[id_article]."\"  alt=\"definition du mot\" class=\"glossaire\">$o[titre]</a> ",$texte);
		
	}
		/* je remplace les $replace une à une par le contenu du tableau $tagMatches[] */
	for($i=0;$i<sizeof($tagMatches[0]);$i++)
			/* je compte combien il y a d'occurences dans $tagMatches et je cree une boucle de ce nombre d'occurences */
			{
			$texte= first_replace ($replace,$tagMatches[0][$i], $texte); 
			}
	return $texte;
}
?>