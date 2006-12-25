<?php
/*
*	+----------------------------------+
*	Nom du Filtre : Filets de Separation                                               
*	Version : 1.2                                               
*	Date : vendredi 8 septembre 2006
*	Auteur : FredoMkb (fredomkbfr@yahoo.fr)                                      
*	+-------------------------------------+
*	Fonctions de ce filtre :
*		Ce filtre permet d'introduire des filets de separation dans le corps du texte.
*		Le but est d'offrir un moyen simple pour structurer et visualiser les niveaux 
*		d'imbrication des differents textes a l'interieur d'un article ou breve.
*		Ce filtre se veut un complement un peu plus souple que le filet de separation
*		par defaut produit par Spip lors de l'insertion des 4 tirets normaux "----".
*	+-------------------------------------+ 
*	Integration de ce filtre dans le squelette :
*		Dans une boucle article, par exemple, il faut placer ce filtre sur la balise "#TEXTE", 
*		comme suit : [(#TEXTE*|filets_sep|propre)]
*		Si vous avez d'autres filtres a utiliser, inserez de preference ce filtre en premier, 
*		par exemple : [(#TEXTE*|filets_sep|propre|reduire_image{400,0})]
*		Attention !, cette nouvelle version du filtre s'utilise en inserant un asterisque apres
*		la balise "#TEXTE". Il faut aussi ajouter, apres le filtre "filets_sep", le filtre "propre"
*		afin que les formatages typographiques de Spip puissent s'applquer au texte.
*		Il faut absolument respecter cette syntaxe pour que le filtre fonctionne correctement.
*	+-------------------------------------+ 
*	Utilisation de ce filtre dans le texte des articles et breves :
*		Les balises s'inscrivent toujours en utilisant 4 tirets bas (obtenus par "majuscule + tiret"),
*		separes, deux a deux, par un chiffre correspondant au type de filet a inserer dans le texte.
*		Cette version du filtre est distribuee avec 10 styles pre-formates, qu'on peut obtenir 
*		en inserant des balises "__0__" jusqu'a "__9__" dans les corps des articles.
*		Attention !, seules les balises inserees dans une ligne isolee seront traitees, de plus,
*		aucun autre caractere ne doit entourer chaque balise, sans quoi le filtre sera innoperant.
*		L'aspect de ces filets est parametrable grace aux styles correspondants (voir ci-apres).
*	+-------------------------------------+ 
*	Parametrage de l'aspect des filets :
*		Ce filtre remplace donc les differentes balises inserees par des paragraphes Html vides, 
*		mais ayant chacun un style specifique, par exemple "<p class="filet_sep_1"></p>".
*		Ces styles sont fournis avec ce filtre dans le fichier "filets_sep.css", mais il faudra 
*		les inserer dans la feuille de style qui gere l'apparence de votre squelette ou les appeler 
*		a partir des fichiers Html "article.html" et "breve.html" de votre squelette.
*		Il y a donc 10 styles pre-formates fournis avec cette version du filtre, qui sont separes 
*		en trois groupes : "Filets" qui produisent des filets simples, "Blocs" qui produisent 
*		des blocs avec des fonds et filets differents, enfin, "Images" qui produisent des blocs
*		affichant differentes images (placez les images au meme niveau que la feuille de style). 
*		Tous ces filtres sont evidemment personnalisables suivant vos gouts et besoins. 
*		Vous pouvez aussi ajouter autant de styles supplementaires que necessaire, 
*		mais en respectant la convention de nommage suivante pour les nouveaux styles :
*		"filet_sep_N", ou "N" est le numero que vous aurez decide d'attribuer au style.
*		Attention !, seuls les chiffres sont supportes pour identifier les styles. 
*	+-------------------------------------+ 
*	Pour toute remarque ou suggestion, reportez-vous au forum de l'article :
*	<http://fredomkb.free.fr/spip/spip.php?article14> 
*	+-------------------------------------+ 
*/

function filets_sep($texte) {
// Fonction pour generer des filets de separation selon les balises presentes dans le texte fourni.
// Il y a par defaut 10 filets possibles, de 0 a 9, mais on peut en ajouter d'autres au besoin.
	
	// On memorise le modele d'expression rationnelle a utiliser pour chercher les balises.
	$modele = '#[\n\r]__(\d+)__[\n\r]#iU';
	
	// On verifie si des balises filets existent dans le texte fourni.
	$test= preg_match($modele, $texte);

	if ($test) {
		// On isole les textes presents dans les balises "cadre" et "code".
		preg_match_all('#<cadre>(.*?)</cadre>#is', $texte, $listeCadre);
		preg_match_all('#<code>(.*?)</code>#is', $texte, $listeCode);
		$listeCadreTexte = $listeCadre[0];
		$listeCodeTexte = $listeCode[0];
		
		// On modifie le format des balises filets dans les balises "cadre" pour ne pas les traiter.
		foreach ($listeCadreTexte as $texteCadreOrig) {
			$texteCadreNew = preg_replace('#__(\d+)__#iU','__-$1-__',$texteCadreOrig);
			$texte = str_replace($texteCadreOrig,$texteCadreNew,$texte);
		};
	
		// On modifie le format des balises filets dans les balises "code" pour ne pas les traiter.
		foreach ($listeCodeTexte as $texteCodeOrig) {
			$texteCodeNew = preg_replace('#__(\d+)__#iU','__-$1-__',$texteCodeOrig);
			$texte = str_replace($texteCodeOrig,$texteCodeNew,$texte);
		};

		// On remplace les balises filets dans le texte par le code Html correspondant.
		$texte = preg_replace($modele,'<html><p class="filet_sep_$1"><!-- --></p></html>',$texte); 
		
		// On remet les balises filets presents dans les balises "cadre" et "code" a leur format initial.
		$texte = preg_replace('#__-(\d+)-__#iU','__$1__',$texte); 
	};

	// Traitement des anciens filets, pour assurer une compatibilite descendente.
//	$texte = str_replace('__l__','<html><p class="filet_sep_long"></p></html>',$texte);
//	$texte = str_replace('__m__','<html><p class="filet_sep_moyen"></p></html>',$texte);
//	$texte = str_replace('__c__','<html><p class="filet_sep_court"></p></html>',$texte);

	return $texte;
}
?>
