<?
function pagination_precedent_suivant($total, $nom, $pas, $liste){
static $ancres = array();
	
	$separateur= " | ";
	
	$debut = 'debut'.$nom;
	$ancre='pagination'.$nom;
	

	$lien_precedent = '<a href="@url@" rev="prev" class="pagination_precedent lien_pagination" title="'._T('public:page_precedente').'">@item@</a>';
	$lien_suivant	= '<a href="@url@" rel="next" class="pagination_suivant lien_pagination" title="'._T('public:page_suivante').'">@item@</a>';

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
	
	// calcul des liens
	$url = parametre_url(self(),'fragment','');
	if ($num == 0){
		$texte=pagination_item($suivant,
			_T('public:page_suivante'),
			$lien_suivant,$url,$debut,$ancre);
			}
	
	
	else if (($num >= $total - 1 - $pas) and ($num >=  $pas)){	
			echo "truc";				
			$texte=pagination_item($precedant,
			_T('public:page_precedente'),
			$lien_precedent,$url,$debut,$ancre);
			}
	
	else {
			
			$texte=pagination_item($precedant,
			_T('public:page_precedente'),
			$lien_precedent,$url,$debut,$ancre);
			
			$texte = $texte.$separateur.pagination_item($suivant,
			_T('public:page_suivante'),
			$lien_suivant,$url,$debut,$ancre);
			
			}
		
	

	return $bloc_ancre.$texte;

}




?>