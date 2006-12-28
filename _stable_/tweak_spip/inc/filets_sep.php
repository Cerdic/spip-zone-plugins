<?php
/*
*	+----------------------------------+
*	Nom du Tweak : Filets de Separation
*	Idee originale : FredoMkb
*	Serieuse refonte et ameliorations pour Tweak SPIP : Patrice Vanneufville
*	Date : decembre 2006
*	+-------------------------------------+
*	Fonctions de ce tweak :
*		Ce tweak permet d'introduire des filets de separation dans le corps du texte.
*		Le but est d'offrir un moyen simple pour structurer et visualiser les niveaux 
*		d'imbrication des differents textes a l'interieur d'un article ou breve.
*		Ce tweak est un complement plus riche et plus souple que le filet de separation
*		par defaut produit par Spip lors de l'insertion des 4 tirets normaux "----".
*	+-------------------------------------+ 
*	Utilisation de ce tweak dans les textes de votre site SPIP :
*	1) Version predefinie
*		Les balises s'inscrivent toujours en utilisant 4 tirets bas (4 tirets en souligne),
*		separes, deux a deux, par un chiffre correspondant au type de filet a inserer dans le texte.
*		Cette version du filtre est distribuee avec 7 styles pre-formates, qu'on peut obtenir 
*		en inserant des balises "__0__" jusqu'a "__6__" dans les corps des articles.
*	2) Version image
*		Les balises s'inscrivent toujours en utilisant 4 tirets bas (4 tirets en souligne),
*		separes, deux a deux, par un nom de fichier que l'on peut trouver dans le dossier :
*		img/filets/
*		Cette version du tweak permet donc d'adapter n'importe quelle image en inserant des 
*		balises du genre "__exemple.png__" dans les corps des articles.
*		Seules les images au format png, gif ou jpg sont reconnues.
*		L'aspect de ces filets en image est parametrable grace au style .filet_sep_image que l'on
*		peut trouver dans le fichier "inc/filets_sep.css"
*		La hauteur du filet correspond toujours à la hauteur reelle de l'image.
*	Attention : les balises (predefinies ou images) doivent etre inserees dans une ligne isolee 
*		pour etre traitees.
*		Entre <code> et </code> ou <cadre> et </cadre>, il n'y a pas de recherche de balises de filets.
*	+-------------------------------------+ 
*	Parametrage de l'aspect des filets  predefinis:
*		Ce tweak remplace donc les differentes balises inserees par des paragraphes Html vides, 
*		mais ayant chacun un style specifique, par exemple "<p class="filet_sep_1"></p>".
*		Ces styles sont fournis avec ce tweak dans le fichier "inc/filets_sep.css".
*		Il y a donc 7 styles pre-formates  qui sont separes en deux groupes : 
*		"Filets" qui produisent des filets simples et "Blocs" qui produisent 
*		des blocs avec des fonds et filets differents. 
*		Tous ces filets sont evidemment personnalisables suivant vos gouts et besoins. 
*		Vous pouvez aussi ajouter autant de styles supplementaires que necessaire, 
*		mais en respectant la convention de nommage suivante pour les nouveaux styles :
*		"filet_sep_N", ou "N" est le numero que vous aurez decide d'attribuer au style.
*		Attention !, seuls les chiffres sont supportes pour identifier les styles. 
*	+-------------------------------------+ 
*	Pour toute remarque ou suggestion, reportez-vous au forum de l'article :
*	<http://www.spip-contrib.net/> 
*	+-------------------------------------+ 
*/

// Fonction pour generer des filets de separation selon les balises presentes dans le texte fourni.
// Cette fonction n'est pas appelee dans les balises html : cadre|code
function filets_sep_rempl($texte) {
	
	// On memorise les modeles d'expression rationnelle a utiliser pour chercher les balises.
	$base_nombre = '\d+';
	$base_fichier = '[\w+\.-]+\.(jpg|png|gif)';
	$base_total = $base_nombre.'|'.$base_fichier;
	$modele_nombre = "#[\n\r]\s*__({$base_nombre})__\s*[\n\r]#iU";
	$modele_fichier = "#[\n\r]\s*__({$base_fichier})__\s*[\n\r]#iU";
	$modele_total = "#[\n\r]\s*__({$base_total})__\s*[\n\r]#iU";
	
	// On verifie si des balises filets existent dans le texte fourni.
	$test= preg_match($modele_total, $texte);

	if ($test) {
		// On remplace les balises filets numeriques dans le texte par le code Html correspondant.
		$texte = preg_replace($modele_nombre,'<html><p class="filet_sep_$1"><!-- --></p></html>',$texte); 

		// On remplace les balises filets numeriques dans le texte par le code Html correspondant.
		$t=preg_split($modele_fichier, $texte, -1, PREG_SPLIT_DELIM_CAPTURE);
		$texte = $t[0];
		for ($i=1; $i<count($t); $i+=3) {
			$f=find_in_path('img/filets/'.$t[$i]);
			if (file_exists($f)) list(,$haut) = @getimagesize($f);
			if ($haut) $haut='height:'.$haut.'px; ';
			$texte .= '<html><p class="filet_sep_image" style="'.$haut.'background-image: url('.$f.');"><!-- --></p></html>'.$t[$i+2];
		}
	};

	return $texte;
}

function filets_sep($texte) {
	return tweak_exclure_balises('cadre|code', 'filets_sep_rempl', $texte);
}
?>
