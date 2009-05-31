<?php

// Cette fonction extrait les titres de l'article pour constituer un sommaire
$sommaire_complet = "";
function sommaire($texte){
if (preg_match('/\{\{\{/', $texte) || preg_match('/\{2\{/', $texte)){
	$sommaire_complet.="<div id='sommaire_article'>";
	$sommaire_complet.= "
<h4 class='center'>
	<a name='haut' class='sommaire_article'>
		Sommaire
	</a>
</h4>";
	$sommaire_complet.= "
<ul>";
	$texte=preg_match_all('/\{[\{2]\{(.*?)\}[\}2]\}/', $texte, $texte_sommaire);
	$j=1;
	// Pas de sommaire s'il n'y a pas plus de eux titres
	if(count($texte_sommaire[0])<3){	return; }
	for($i=0; $i<count($texte_sommaire[0]); $i++){
		$sommaire_complet.= preg_replace(
			'/\{\{\{(.*?)\}\}\}/',
	'<li>
		<strong>
			<a href="#sommaire_'.$j.'">
				$1
			</a>
		</strong>',
			preg_replace(
				'/(\{2\{)(.*?)(\}2\})/',
	'<ul>
		<li>
			<a href="#sommaire_'.$j.'">
				$2
			</a>
		</li>
	</ul>',
				$texte_sommaire[0][$i])
		);
	$j++;
		$sommaire_complet.="</li>";
		}
	$sommaire_complet.= "
</ul>
</div>";
	return $sommaire_complet;
	}
}

// Cette fonction ajoute des ancres aux titres de l'article et les nomme.
function ancrer($texte){
$texte=preg_replace(
	'/(\{\{\{)(.*?)(\}\}\})/',
	'$1<a href="#haut" class="haut" title="Retour au sommaire"><img alt="^" class="spip_logos" border="none" src="plugins/eva/images/2uparrow.png" /></a><a class="sommaire_article" name="sommaire_@num_ordre@">$2</a>$3',
	preg_replace(
		'/(\{2\{)(.*?)(\}2\})/',
		'$1<a href="#haut" class="haut" title="Retour au sommaire"><img alt="^" class="spip_logos" border="none" src="plugins/eva/images/2uparrow.png" /></a><a class="sommaire_article" name="sommaire_@num_ordre@">$2</a>$3',
	$texte)
	);

// La fin du code est inspiree de
// <http://www.spip-contrib.net/Sommaire-de-l-article>

	$array = explode("@num_ordre@" , $texte);
	$res =count($array);
	$i = 1;
	$texte=$array[0];
	while($i<$res){
		$texte=$texte.$i.$array[$i];
		$i++;
	}

return $texte;
}

?>
