<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Renvoie un tableau d infos sur les familles de polices font-face trouvees dans le repertoire polices.
 * le tableau indique pour chaque famille les extensions et le chemin complet vers le fichier correspondant.
 * pour rappel (d apres fontsquirrel.com) :
 * TTF  : ok sauf IE et iPhone
 * EOT  : IE
 * WOFF : standard emergent
 * SVG  : iPhone/iPad
 * 
 * @param array $familles	vide pour toutes les familles trouvees dans le repertoire,
 * 				sinon une liste des familles pour restreindre a celles-ci.
 * 				(famille = nom du fichier sans extension)
 * @return array 	famille => ( extension1 => chemin/vers/fichier, extension2 => chemin/vers/fichier, ... )
 */
function polpriv_familles_polices_fontface ($familles='') {
	// definir ici les extensions requises
	$extensions_requises = array('ttf','eot','woff');

	// collecte des fichiers de police
	$ext = implode('|', $extensions_requises);
	$fichiers = find_all_in_path('polices/', "\w+\.[$ext]");
	if (!$fichiers OR empty($fichiers)) return false;

	// sans parametre, on deduit les familles d apres les fichiers du repertoire
	if (!$familles) {
		foreach ($fichiers as $fichier=>$chemin)
			$familles[] = substr($fichier, 0, strrpos($fichier,'.'));
		$familles = array_unique($familles);
	}

	// etablir un tableau, en filtrant les familles ne disposant pas des fichiers requis
	if (!$familles OR empty($familles)) return false;
	foreach ($familles as $famille) {
		$fichiers_famille = preg_grep("/^$famille/", array_keys($fichiers));
		foreach ($fichiers_famille as $k=>$fichier)
			$extensions[] = pathinfo($fichier, PATHINFO_EXTENSION);
		if ($extensions = array_intersect($extensions, $extensions_requises)
		AND count($extensions) == count($extensions_requises)) { // ne garder que les extensions valides
			foreach ($extensions as $ext) {
				$familles_infos[$famille][$ext] = url_absolue(find_in_path($famille.".".$ext, 'polices/'));
			}
		}
		$extensions = array();
	}

	return $familles_infos;
}


/*
 * Genere les styles css de familles de police fontface
 * Formattage du style tire de fontsquirrel.com
 * @font-face {
    font-family: 'famille';
    src: url('famille.eot');
    src: url('famille.eot?#iefix') format('embedded-opentype'),
         url('famille.woff') format('woff'),
         url('famille.ttf') format('truetype'),
         url('famille.svg#famille') format('svg');
    }
 *
 * @param $familles array	famille => ( extension1 => chemin/vers/fichier, extension2 => chemin/vers/fichier, ... )
 * @return string 		style complet, entre balises <style>
 */
function polpriv_generer_style_polices_fontface ($familles) {
	if (!$familles OR !is_array($familles))
		return false;

	$style = "\n<style type='text/css'>";
	foreach ($familles as $famille=>$fichiers) {
		if (array_key_exists($ext='eot', $fichiers)) {
			$eot = "src: url('$fichiers[$ext]');\n";
			$eot_iefix = "src: url('$fichiers[$ext]?#iefix') format('embedded-opentype')";
		}
		if (array_key_exists($ext='woff', $fichiers))
			$woff = "url('$fichiers[$ext]') format('woff')";
		if (array_key_exists($ext='ttf', $fichiers))
			$ttf = "url('$fichiers[$ext]') format('truetype')";
		if (array_key_exists($ext='svg', $fichiers))
			$svg = "url('$fichiers[$ext]#$famille') format('svg')";
		$chaine = implode(",\n", array_filter(array($eot_iefix,$woff,$ttf,$svg))) . ";";
		$style .= "\n@font-face {\n"
			."font-family: '$famille';\n"
			. $eot
			. $chaine
			."\n}\n";
	}
	$style .= "</style>\n";

	return $style;
}

/*
 * Formater un nom de fichier en truc plus lisible
 * Goudy_ModernLightCondensed_Western-webfont -> Goudy Modern Light Condensed Western 
 *
 * @param $nom string		nom brut
 * @return string		nom formate
 */
function polpriv_formater_label_fontface($nom) {
	// si extension, la retirer
	if ($ext = pathinfo($nom, PATHINFO_EXTENSION))
		$nom = preg_replace("/.$ext$/", '', $nom);
	// si minuscule collee a une majuscule, inserer un espace
	$nom = preg_replace('/([a-z])([A-Z])/', '$1 $2', $nom);
	// retirer '-webfont' final
	$nom = preg_replace('/\W?[wW]ebfont$/', '', $nom);
	// remplacer separateurs par des espaces
	$nom = preg_replace('/[-_.]/', ' ', $nom);
	// mise en majuscules
	$nom = ucwords($nom);

	return $nom;
}

?>
