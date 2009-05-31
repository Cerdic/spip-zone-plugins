<?php
/**
 * Debut de balise SELECT dans un TR
 *
 * @param string $label : Titre du label pour le champ select
 * @param string $name : nom de la variable PHP/HTML
 * @param string $html : code HTML facultatif
 * @return string
 */
function formSelectTR1($label,$name,$html='') {
	$tr = "\t<tr>\n\t\t<td align='left'><label for='$name'>$label</label></td>\n\t\t<td align='left'><select name='$name' id='$name' $html>\n";
	return $tr;
}

/**
 * Fin de balise SELECT dans un TR
 * 
 * @return string
 */
function formSelectTR2() {
	return "</select></td>\n\t</tr>\n";
}

/**
 * Cree un champ OPTION dans un SELECT
 *
 * @param string $label : le titre du OPTION
 * @param string $value : la valeur transmise dans la variable (cf name du SELECT)
 * @param string $selected : contient la valeur a selectionner par defaut
 * @return string
 */
function formOptionsInSelect($label,$value,$selected="") {
	if ((string)trim($selected)==(string)trim($value) && trim($selected!="")) {
		//echo "<b>$value==$selected</b> $label<br>";
		$selected="selected";
	} else {
		if($label=='Faux') echo "$label - $value!=$selected<br>";
		$selected="";
	}
	return "\t<option value=\"$value\" $selected>$label</option>\n";
}

/** Ajoute du code javascript
 * 
 * @param string $script : <ul>
 * 	<li>Nom d'un mod&egrave;le de script :
 * 	<ul>
 * 		<li>isDate : permet de detecter si une date est correcte</li>
 * 		<li>changerStyle : permet de changer le style d'un champ de formulaire (backgroundColor+disable)</li>
 * 	</ul>
 * 	</li>
 * 	<li>OU directement code javascript a encapsuler</li>
 * </ul>
 * @return string : code javascript
 */
function putJavascript($script) {
	switch ($script)
	{
		case 'changerStyle' :
			$script= <<<FINSCRIPT
function changerStyle(champ,couleurFond,style) {
	switch (style)
	{
		case 'readonly': 
			champ.readonly=true;
			break;
		case 'disable': 
			champ.disabled=true;
			break;
		case 'enable':
			champ.disabled=false;
			champ.readonly=false;
			break;
	}
	champ.style.backgroundColor='couleurFond';
}
FINSCRIPT;
			break;
		case 'isDate' : 
			$annee_min=(int)(date('Y')-100);
			$annee_max=(int)(date('Y')-10);
			$script= <<<FINSCRIPT
function isDate(d) {
// http://www.javascriptfr.com/code.aspx?ID=15737

if (d == "") // si la variable est vide on retourne faux
return false;

e = new RegExp("^[0-9]{1,2}\/[0-9]{1,2}\/([0-9]{2}|[0-9]{4})$");

if (!e.test(d)) // On teste l'expression régulière pour valider la forme de la date
return false; // Si pas bon, retourne faux

// On sépare la date en 3 variables pour vérification, parseInt() converti du texte en entier
j = parseInt(d.split("/")[0], 10); // jour
m = parseInt(d.split("/")[1], 10); // mois
a = parseInt(d.split("/")[2], 10); // année

// Si l'année n'est composée que de 2 chiffres on complète automatiquement
if (a < 1000) {
if (a < 89) a+=2000; // Si a < 89 alors on ajoute 2000 sinon on ajoute 1900
else a+=1900;
}

// Définition du dernier jour de février
// Année bissextile si annnée divisible par 4 et que ce n'est pas un siècle, ou bien si divisible par 400
if (a%4 == 0 && a%100 !=0 || a%400 == 0) fev = 29;
else fev = 28;

// Nombre de jours pour chaque mois
nbJours = new Array(31,fev,31,30,31,30,31,31,30,31,30,31);

// Enfin, retourne vrai si le jour est bien entre 1 et le bon nombre de jours, idem pour les mois, sinon retourn faux
return ( m >= 1 && m <=12 && j >= 1 && j <= nbJours[m-1] && a >= $annee_min && a <= $annee_max);
}
FINSCRIPT;
			break;
	}
	return "<script type='text/javascript'><!--\n$script\n//-->\n</script>\n";
}

/**
 * Cree un champ INPUT TEXT dans une ligne TR
 *
 * @param string $label : <td>libelle (premiere cellule)</td>
 * @param string $name  : nom de la variable PHP/HTML
 * @param string $value : valeur par defaut
 * @param string $html  : code html optionnel dans le <INPUT>
 * @param string $fin_ligne : <td>optionnels a ajouter</td>
 * @param boolean $password : mettre true pour champ password (false par defaut)
 * @return string
 */
function formInputTextTR($label,$name,$value='',$html='',$fin_ligne='',$password='') {
	if($password) $type='password'; else $type='text';
	//if(substr_count($html,'class')==0) $html.=" class='forml'";
	$tr = "\t<tr>\n\t\t<td align='left'><label for='$name'>$label</label></td>\n\t\t<td align='left'><input type='$type' name='$name' id='$name' value=\"$value\" $html/></td>\n\t\t$fin_ligne\n\t</tr>\n";
	return $tr;
}

/**
 * Cree un champ TEXTAREA dans un TR
 *
 * @param string $label : <td>libelle (premiere cellule)</td>
 * @param string $name  : nom de la variable PHP/HTML
 * @param string $value : valeur par defaut
 * @param string $html  : code html optionnel dans le <INPUT>
 * @return string
 */
function formTextAreaTR($label,$name,$value='',$html='',$html_td='') {
	$tr = "\t<tr>\n\t\t<td align='left'><label for='$name'>$label</label></td>\n\t\t<td align='left' $html_td><textarea name='$name' id='$name' $html>$value</textarea></td>\n\t</tr>\n";
	return $tr;
}


/**
 * Cree un champ SELECT contenant les annees de 2002 a annee en cours + 1
 *
 * @param string $annee : annee selectionnee par defaut
 * @return string
 */
function formSelectAnnee($annee) {
	$str="";
	for ($i = (int)date("Y")+1; $i >= 2002; $i--) {
		$str.= formOptionsInSelect($i,$i,$annee);
	}
	return $str;
}

/** 
 * Champ SELECT Vrai/Faux
 * boolean
 * @param boolean $isVrai : valeur par defaut
 * @return string 
 */
function formSelectVraiFaux($isVrai) {
	$str= formOptionsInSelect('Vrai',1,$isVrai);
	$str.= formOptionsInSelect('Faux',0,$isVrai);
	return $str;
}

/**
 * Affiche une vignette (icone de type de fichier)
 *
 * @param string $ext : extension du fichier
 * @param string $title : title (et alt) de l'icone
 * @return string
 */
function vignette($ext,$title='') {
	define('DIR_ODB_COMMUN',_DIR_PLUGINS."odb/odb_commun/");

	switch(trim($ext)) {
		case "csv":
			$ext="ods";
			break;
		case "":
			$ext="defaut";
	}
	$src=DIR_ODB_COMMUN."img_pack/vignettes/$ext.png";
	if(is_file($src))
	$str = "<img src='$src' alt='Fichier $ext' title='$title' align='absmiddle'/>";
	else
	$str = "<b>[$ext]</b>\n";
	return $str;
}

/**
 * Affiche une table html
 * 
 * @param string $titre : titre du tableau
 * @param array $tbody : tableau de lignes (&lt;tr&gt;...&lt;/tr&gt;)) sans les &lt;tr&gt;
 * @param strin $thead : tableau de lignes de &lt;th&gt; a mettre au debut ('' si aucun)
 * @param string $icone : nom de fichier de l'icone (cf ../dist/images/)
 * @return string : table HTML
 */ 
function odb_html_table($titre,$tbody,$thead='',$icone='vignette-24.png') {
	$isMSIE=eregi('msie',$_SERVER['HTTP_USER_AGENT'])>0;

	$wrapper=$isMSIE ? 'wrapper.php?file=':'';

	$ret="<div class='liste'>\n"
   . "   <div style='position: relative;'>\n"
   . "      <div style='position: absolute; top: -12px; left: 3px;'><img src='../dist/images/$wrapper$icone' alt=''  /></div>\n"
	. "      <div style='background-color: white; color: black; padding: 3px; padding-left: 30px; border-bottom: 1px solid #444444;' class='verdana2'>\n"
   . "         <b>$titre</b>\n"
	. "      </div>\n"
   . "   </div>\n"
   . "   <table id='".getRewriteString(supprimeAccents(strip_tags(substr(html_entity_decode(odb_propre($titre)),0,20))))."' width='100%' cellpadding='2' cellspacing='0' border='0' class='spip'>\n"
   ;
   if($thead!=='') {
   	$ret.="<thead>\n";
   	if(is_array($thead))
   		foreach($thead as $ligne)
   			$ret.="\t<tr $js>\n\t\t$ligne\n\t</tr>\n";
   	else $ret.="\t<tr $js>\n\t\t$thead\n\t</tr>\n";
   	$ret.="</thead>\n";
	}
	$ret.="<tbody>\n";

	//$js=$isMSIE ? "onmouseover=\"changeclass(this,'tr_liste_over');\" onmouseout=\"changeclass(this,'tr_liste');\"" : '';
	$js="onmouseover=\"changeclass(this,'tr_liste_over');\" onmouseout=\"changeclass(this,'tr_liste');\"";
	if(is_array($tbody))
		foreach($tbody as $ligne)
			$ret.="\t<tr class='tr_liste' $js>\n\t\t$ligne\n\t</tr>\n";
	$ret.="</tbody>\n";
	$ret .= "   </table>\n</div>\n";

	return $ret;
}

/**
 * affiche une boite de message
 *
 * @param string $texte : texte a afficher
 * @param string $type  : type de boite
 * @return string
 */
function boite_important($texte,$type='') {
	$style="border: 1px solid red;background-color:#eee;padding:5px;";
	switch($type) {
		//TODO implementer differents types de boite
		case 'attention':
		case 'warning':
		default:
			$panno="<b style='color:red;text-decoration:underline;'>/!\</b>";
			break;
	}
	return "<p style='$style'>$panno $texte</p>\n";
}

/**
 * Supprime les accents et enleve les espaces inutiles
 * 
 * @param string str : chaine a traiter 
 * @param string $stripette : true (par defaut) s'il faut remplacer "-" et "/" par " - " et " / "
 * @return string
 */
function supprimeAccents($str,$stripette=true) {
	if($stripette) {
		$str=str_replace("-"," - ",$str);
		$str=str_replace("/"," / ",$str);
	}
	//$str=ereg_replace('^[:blank:]+$',' ',$str);
	while(substr_count($str,'  ')>0)
	$str=str_replace('  ',' ',$str);
	$str=utf8_decode($str);
	$str = strtr($str,"\xC0\xC1\xC2\xC3\xC4\xC5\xC6","AAAAAAA");
	$str = strtr($str,"\xC7","C");
	$str = strtr($str,"\xC8\xC9\xCA\xCB","EEEE");
	$str = strtr($str,"\xCC\xCD\xCE\xCF","IIII");
	$str = strtr($str,"\xD1","N");
	$str = strtr($str,"\xD2\xD3\xD4\xD5\xD6\xD8","OOOOOO");
	$str = strtr($str,"\xDD","Y");
	$str = strtr($str,"\xDF","S");
	$str = strtr($str,"\xE0\xE1\xE2\xE3\xE4\xE5\xE6","aaaaaaa");
	$str = strtr($str,"\xE7","c");
	$str = strtr($str,"\xE8\xE9\xEA\xEB","eeee");
	$str = strtr($str,"\xEC\xED\xEE\xEF","iiii");
	$str = strtr($str,"\xF1","n");
	$str = strtr($str,"\xF2\xF3\xF4\xF5\xF6\xF8","oooooo");
	$str = strtr($str,"\xF9\xFA\xFB\xFC","uuuu");
	$str = strtr($str,"\xFD\xFF","yy");
	return trim($str);
}

/**
 * rend une chaine compatible url-rewriting 
 *
 * @see http://www.php.net/manual/en/function.strtr.php#51862
 * @param string $sString : chaine a traiter
 * @return string
 */
function getRewriteString($sString) {
	$string    = htmlentities(strtolower($sString));
	$string    = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml);/", "$1", $string);
	$string    = preg_replace("/([^a-z0-9]+)/", "-", html_entity_decode($string));
	$string    = trim($string, "-");
	//echo "$sString&rarr;$string<br>";
	return $string;
}

/**
 * affiche un taux sous forme d'image
 * 
 * @param string $taux : taux<1 a representer
 * @param string $largeur : multiplicateur de largeur
 * @return string
 */
function afficheTaux($taux,$largeur=1) {
	$taux=round(100*$taux,1);
	return "<IMG SRC='"._DIR_PLUGIN_ODB_REPARTITION."img_pack/carre.gif' title='$taux %' alt='$taux %' height='5'  width='".round($largeur*$taux,0)."'>";
}

/**
 * affiche un texte  la verticale (image, si gd install)
 * 
 * @param string $texte : chaine a traiter
 * @param int $taille : taille du texte (1..5)
 * @param int $largeur : largeur de l'image
 * @param int $hauteur : hauteur de l'image
 * @param string $type : img pour creer le code d'une image ou src pour une image background
 */
function texte90($texte,$taille,$largeur,$hauteur,$type='img') {
	$texte=str_replace("'","&quot;",$texte);
	$src="../plugins/odb/odb_commun/inc-image.php?text=$texte&x=$largeur&y=$hauteur&taille=$taille";
	if($type=='img')
	return "<img alt='$texte' src='$src'/>";
	else return $src;
}

/**
 * table des matieres 
 * Le tableau des index determine le texte a afficher et le nom des ancres html (utilise la fonction getRewriteString)
 * 
 * @param array $tIndex : tableau des index
 * @param int $tailleLettrines : taille lettrines en pixels (0 pour pas de lettrine, alors 1e lettre en gras)
 * @param string $html : attributs html de la table
 * @return string : tableau html [lettrine][entree=>titre]
 */
function odb_table_matieres($tIndex,$tailleLettrines=24,$html='',$isTrier=true) {
	if($isTrier) asort($tIndex);
	$sIndex='';
	if($tailleLettrines>0) {
		foreach($tIndex as $entree) {
			$lettre=$entree[0];
			$lettrine[$lettre][]=substr($entree,1);
		}
		$sIndex="<table $html>\n";
		foreach($lettrine as $lettre=>$t1) {
			$sIndex.="<tr><td valign='top' style='font-size:".$tailleLettrines."px;font-weight:bolder;'>$lettre</td><td valign='middle'><small></small>";
			foreach($t1 as $entree) $sIndex.="<A HREF='#".getRewriteString($lettre.$entree)."'>$entree</a><br/>\n";
			$sIndex.="</small></td></tr>\n";
		}
		$sIndex.="</table>\n";
	} else {
		foreach($tIndex as $entree) $sIndex.="<A HREF='#".getRewriteString($entree)."'><b>".$entree[0]."</b>".substr($entree,1)."</a><br/>\n";
	}
	return $sIndex;
}

/**
 * Nettoie une chaine de caracteres
 *
 * @param string $str
 * @return string
 */
function odb_propre($str) {
	//$str=preg_replace('/\s\s+/',' ',$str);
	$str=str_replace(array("\r","\n","\t",'  '),' ',trim($str));
	$str=str_replace(' - ','-',$str);
	return trim($str);
}

?>
