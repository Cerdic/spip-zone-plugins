<?
function pagination_precedent_suivant($total, $nom, $pas, $liste){
static $ancres = array();
	
	$separateur="&nbsp;| ";
	
	

	

	

	$debut = 'debut'.$nom;
	$ancre='pagination'.$nom;
	$pagination = array(
		'lien_base' => self(),
		'total' => $total,
		'position' => intval(_request($debut)),
		'pas' => $pas,
		'nombre_pages' => floor(($total-1)/$pas)+1,
		'page_courante' => floor(intval(_request($debut))/$pas)+1,
		'lien_pagination' => '<a href="@url@">@item@</a>',
		'lien_item_courant' => '<span class="on">@item@</span>'
	);

	

	// n'afficher l'ancre qu'une fois
	if (!isset($ancres[$ancre]))
		$bloc_ancre = $ancres[$ancre] = "<a name='$ancre' id='$ancre'></a>";

	// Pas de pagination
	if ($pagination['nombre_pages']<=1)
		return '';

	// liste = false : on ne veut que l'ancre
	if (!$liste)
		return $bloc_ancre;

	// liste  = true : on retourne tout (ancre + bloc de navigation)

	list ($premiere, $derniere) = calcul_bornes_pagination(
		$pagination['nombre_pages'],
		$pagination['nombre_pages'],
		$pagination['page_courante']);

	$texte = '';
	
	$num=_request($debut);
	
	if ($pagination['page_courante']==$premiere){
		$texte=pagination_item($num+$pas,
			'<:pagination_suivant:>',
			$pagination['lien_pagination'],self(),$debut,$ancre);
			}
	
	
	else if ($pagination['page_courante']==$derniere){
			$texte=pagination_item($num-$pas,
			'<:pagination_precedent:>',
			$pagination['lien_pagination'],self(),$debut,$ancre);
			}
	
	else {
			$texte=pagination_item($num-$pas,
			'<:pagination_precedent:>',
			$pagination['lien_pagination'],self(),$debut,$ancre);
			
			$texte = $texte.pagination_item($num+$pas,
			'<:pagination_suivant:>',
			$pagination['lien_pagination'],self(),$debut,$ancre);
			
			}
		
	

	return $bloc_ancre.$texte;

}




?>