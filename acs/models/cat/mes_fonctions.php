<?php
/*
             ACS
         (Plugin Spip)
         Squelette Cat
    http://acs.geomaticien.org

Copyright Daniel FAIVRE, 2007-2009
Copyleft: licence GPL - Cf. LICENCES.txt in acs plugin dir
*/

/*
*	+----------------------------------+
*	Nom du Filtre : "Sommaire Tableau"
*	Version : 1.2
*	Date : dimanche 10 septembre 2006
*	Auteur : FredoMkb (fredomkbfr@yahoo.fr)
*	+-------------------------------------+
*	Fonctions de ce filtre :
*		Ce filtre permet de generer un sommaire de navigation dans les articles.
*		Le but est d'offrir un moyen simple et automatique pour fabriquer des
*		sommaires de navigation a l'interieur de vos articles, aussi bien sous forme
*		de tableau (option par defaut) que sous forme de liste.
*		En plus du sommaire de navigation, le filtre ajoute donc les ancres et liens
*		necessaires a une navigation aisee au sein de l'article.
*		Une option permet la numerotation automatique du sommaire et des intertitres,
*		une autre permet le masquage ponctuel des intertitres.
*	+-------------------------------------+
*	Integration de ce filtre dans le squelette :
*		Dans une boucle article, il faut placer ce filtre sur la balise "#TEXTE".
*		Par exemple : [(#TEXTE*|somm_table|propre)]
*		Important : prendre soin a mettre un asterisque "*" apres la balise "#TEXTE"
*		et de terminer l'integration par le filtre "propre", pour retrouver le formatage
*		typographique par defaut de Spip.
*	+-------------------------------------+
*	Utilisation de ce filtre dans le texte des articles et breves :
*		Il suffit de rediger votre article avec des intertitres puis d'inscrire
* 		un intertitre "Sommaire" pour que le filtre fabrique le sommaire
*		de navigation sous forme de tableau, ajoutez un trait d'union a la fin,
*		"Sommaire-", pour generer une liste a la place du tableau.
*		Ajoutez un caractere diese "#" pour generer une numerotaion automatique.
*		Entourez les intertitres des crochets, "[" et "]", pour les masquer.
*	+-------------------------------------+
*	Personnalisation de l'intertitre declencheur :
*		Pour une utilisation dans un site multilingue, ou pour changer le terme
*		declencheur de la fabrication du sommaire de navigation, ajoutez un argument
*		au filtre lors de son integration dans les fichiers Html, comme suit :
*		[(#TEXTE*|somm_table{'Declencheur'}|propre)]
*		ou 'Declencheur' represente le terme que vous choisirez de personnaliser.
*	+-------------------------------------+
*	Pour toute remarque ou suggestion, reportez-vous au forum de l'article.
*	<http://fredomkb.free.fr/spip/spip.php?article15>
*	+-------------------------------------+
*/

function somm_table($texteOrig, $titreSommaire = '') {
// Fonction pour creer un sommaire sous forme d'une liste ou d'un tableau Spip.
// La fonction verifie l'existence d'un intertitre "Sommaire" par defaut,
// ou le titre de sommaire fourni par l'tuilisateur (pour des articles multilingues).
// Si un intertitre sommaire existe, alors on analyse le texte fourni pour isoler
// tous les intertitres afin de pouvoir fabriquer le sommaire, avec des liens
// internes vers tous les intertitres et de liens de retour vers le sommaire.
// Le sommaire ainsi cree sera place juste en dessous du titre "Sommaire".

	// Si l'utilisateur n'a pas fourni le titre sommaire a utiliser,
	// alors on utilise le titre par defaut en francais.
	if (empty($titreSommaire)) {
		$titreSommMin = 'sommaire';
	} else {
		$titreSommMin = strtolower($titreSommaire);
	};

	// Test de l'existence d'un intertitre 'Sommaire' pour generer un tableau
	// ou 'Sommaire-' (avec un trait d'union a la fin) pour generer une liste.
	$test = preg_match('#\{\{\{\[?\#?'.$titreSommMin.'-?\]?}\}\}#i', $texteOrig);

	// Si un des intertitres sommaire existe, alors on genere le sommaire.
	if ($test) {

		// On isole les textes presents dans les balises "cadre" et "code".
		preg_match_all('#<cadre>(.*?)</cadre>#is', $texteOrig, $listeCadre);
		preg_match_all('#<code>(.*?)</code>#is', $texteOrig, $listeCode);
		// On place les resultast, avec les balises, dans des variables.
		$listeCadreTexte = $listeCadre[0];
		$listeCodeTexte = $listeCode[0];

		// On modifie le format des balises intertitre dans les balises "cadre" pour ne pas les traiter.
		foreach ($listeCadreTexte as $texteCadreOrig) {
			$texteCadreNew = preg_replace('#(\{\{)(\{.*?\})(\}\})#i','$1-$2-$3',$texteCadreOrig);
			$texteOrig = str_replace($texteCadreOrig,$texteCadreNew,$texteOrig);
		};

		// On modifie le format des balises intertitre dans les balises "code" pour ne pas les traiter.
		foreach ($listeCodeTexte as $texteCodeOrig) {
			$texteCodeNew = preg_replace('#(\{\{)(\{.*?\})(\}\})#i','$1-$2-$3',$texteCodeOrig);
			$texteOrig = str_replace($texteCodeOrig,$texteCodeNew,$texteOrig);
		};

		// Recuperation des tous les intertitres presents dans le texte nettoye.
		preg_match_all('#\{\{\{(.*?)\}\}\}#i', $texteOrig, $listeOrig);

		// On place le resultat a utiliser dans une variable.
		$listeTitresOrig = $listeOrig[1];

		// On verifie qu'il y reste des intertitres a traiter.
		if (count($listeTitresOrig) > 0) {

			// On verifie si le sommaire demande est sous forme de liste ou tableau.
			$testType = preg_match('#\{\{\{\[?\#?'.$titreSommMin.'-\]?\}\}\}#i', $texteOrig);

			// On verifie si la numerotation automatique est demandee.
			$testNro = preg_match('#\{\{\{\[?\#'.$titreSommMin.'-?\]?\}\}\}#i', $texteOrig);

			// On initialise les autres variables.
			$newSomm = '';
			$esp = '&nbsp; &nbsp;';
			$nb = 1;

			// Boucle sur chaque element de la liste des intertitres originaux.
			foreach ($listeTitresOrig as $titreOrig) {
				$masquer = preg_match('#^\[(.*?)\]$#i', $titreOrig); // On test s'il faut masquer.
				$titreClean = rtrim(trim($titreOrig, '[#'),'-]'); // On supprime les eventuels indesirables.
				$titreClean = ucfirst($titreClean); // On met la premiere lettre en majuscule.
				$titreMin = strtolower($titreClean); // On converti en minuscules.

				if ($titreMin == $titreSommMin) {
					// Si le titre considere est 'sommaire', alors on fabrique le debut du sommaire.
					$titreSommOrig = '{{{'.$titreOrig.'}}}';
					// On insere l'ancre et l'intertitre, ou l'ancre seulement s'il faut masquer l'intertitre.
					if ($masquer) {
						$titreSommNew = '[somm<-]'."\n";
					} else {
						$titreSommNew = '[somm<-]'."\n".'{{{'.$titreClean.'}}}'."\n\n";
					}
				} else {
					// On insere la numerotation automatique si elle est demandee.
					if ($testNro) { $titreClean = $nb.'. '.$titreClean; };

					// On fabrique la liste ou le tableau et on place les ancres et liens des intertitres.
					if ($testType) {
						// On fabrique le sommaire sous forme de liste.
						$newSomm = $newSomm.'- [{{<html>'.$titreClean.'</html>}}->#inter'.$nb.']'."\n";
					} else {
						// On fabrique le sommaire sous forme de tableau.
						$newSomm = $newSomm.'|['.$esp.'{{<html>'.$titreClean.'</html>}}'.$esp.'->#inter'.$nb.']|'."\n";
					};
					// On insere l'ancre et l'intertitre, ou l'ancre seulement s'il faut masquer l'intertitre.
					if ($masquer) {
						$titreNew = '[inter'.$nb.'<-]'."\n";
					} else {
						$titreNew = '[inter'.$nb.'<-]'."\n".'{{{[<html>'.$titreClean.'</html>->#somm]}}}';
					}
					// On remplace les intertitres par d'autres avec une ancre et un lien vers le sommaire.
					$texteOrig = str_replace('{{{'.$titreOrig.'}}}', $titreNew, $texteOrig);
					$nb++;
				};
			};
			// On remplace l'intertitre "Sommaire" original par le nouveau sommaire.
			$texteOrig = str_replace($titreSommOrig, $titreSommNew.$newSomm, $texteOrig);
		};
		// On remet les balises intertitres dans les balises "cadre" et "code" a leur format initial.
		$texteOrig = preg_replace('#\{\{-\{(.*?)\}-\}\}#i','{{{$1}}}',$texteOrig);

		// On efface tous les eventuels intertitres vides.
		$texteOrig = str_replace('{{{}}}', '', $texteOrig);
	};
	// Retour du texte avec le sommaire ou le texte original a defaut.
	return $texteOrig;
}

?>