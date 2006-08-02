<?
function pagination_precedent_suivant($total, $nom, $pas, $liste){
static $ancres = array();
	
	
	
	$debut = 'debut'.$nom;
	$ancre='pagination'.$nom;
	

	$lien_precedent = '<a href="@url@" rev="prev" class="pagination_precedent">@item@</a>';
	$lien_suivant	= '<a href="@url@" rel="next" class="pagination_suivant">@item@</a>';

	// n'afficher l'ancre qu'une fois
	if (!isset($ancres[$ancre]))
		$bloc_ancre = $ancres[$ancre] = "<a name='$ancre' id='$ancre'></a>";

	// Pas de pagination
	if ($total<=1)
		return '';

	// liste = false : on ne veut que l'ancre
	if (!$liste)
		return $bloc_ancre;

	// liste  = true : on retourne tout (ancre + bloc de navigation)

	

	$texte = '';
		
	$num=_request($debut);
	// calcul des pages
	
	$num + $pas  > $total - 1 ? $suivant = $total - 1 : $suivant = $num + $pas; // la page suivante
	$num - $pas < 0 ? $precedant = 0 : $precedant = $num - $pas; // la page précédante
	echo $precedant;
	echo $total;
	// calcul des liens
	
	if ($num == 0){
		$texte=pagination_item($suivant,
			'<:pagination_suivant:>',
			$lien_suivant,self(),$debut,$ancre);
			}
	
	
	else if (($num >= $total - 1 - $pas) and ($num >  $pas)){					
			$texte=pagination_item($precedant,
			'<:pagination_precedent:>',
			$lien_precedent,self(),$debut,$ancre);
			}
	
	else {
			
			$texte=pagination_item($precedant,
			'<:pagination_precedent:>',
			$lien_precedent,self(),$debut,$ancre);
			
			$texte = $texte.pagination_item($suivant,
			'<:pagination_suivant:>',
			$lien_suivant,self(),$debut,$ancre);
			
			}
		
	

	return $bloc_ancre.$texte;

}




?>