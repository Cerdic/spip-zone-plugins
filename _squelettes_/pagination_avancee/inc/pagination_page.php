<?
function pagination_page($total, $nom, $pas, $liste){
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
		PAGINATION_MAX,
		$pagination['nombre_pages'],
		$pagination['page_courante']);

	$texte = '';

	if ($premiere > 2)
		$texte .= pagination_item('',
			'...',
			$pagination[
				($i != $pagination['page_courante']) ?
				'lien_pagination' : 'lien_item_courant'
			],
			$pagination['lien_base'], $debut, $ancre)
			. $separateur;

	if ($premiere == 2) $premiere = 1; # '...' inutile quand on peut mettre 0

	for ($i = $premiere; $i<=$derniere; $i++) {
		$num = strval(($i-1)*$pas);
		$texte .= pagination_item($num,
			$i,
			$pagination[
				($i != $pagination['page_courante']) ?
				'lien_pagination' : 'lien_item_courant'
			],
			$pagination['lien_base'], $debut, $ancre);
		if ($i<$derniere) $texte .= $separateur;
	}

	if ($derniere < $pagination['nombre_pages'])
		$texte .= $separateur.
		pagination_item(strval(($pagination['nombre_pages']-1)*$pas),
			'...',
			$pagination[
				($i != $pagination['page_courante']) ?
				'lien_pagination' : 'lien_item_courant'
			],
			$pagination['lien_base'], $debut, $ancre);

	return $bloc_ancre.$texte;

}




?>