<?php

function validateur_apinc_dist($action, $url= ""){
	global $erreur1,$erreur2,$erreur3;
	
	switch ($action){
		case 'infos':
			return "<a href='http://validateur-accessibilite.apinc.org/'>Validateur Accessibili&eacute; apinc</a>";
			break;
		case 'test':
			// validation accessibilite
			$_GET['urlAVerif']=$url;
			ob_start();
			test_apinc();
			ob_end_clean();
			$erreurs = $erreur1+$erreur2+$erreur3;
			if($erreurs==0){
				$ok = true;
				$texte = _T("w3cgh:page_valide");
			}
			else {
				$ok = false;
				if ($erreurs>1)
					$texte = _T("w3cgh:erreurs",array('erreurs'=>$erreurs));
				else
					$texte = _T("w3cgh:une_erreur");
				$texte .= " ($erreur1/$erreur2/$erreur3)";
			}
			return array($ok,$erreurs,$texte);
			break;
		case 'visu':
			$_GET['urlAVerif']=$url;
			ob_start();
			test_apinc();
			$texte = ob_get_contents();
			ob_end_clean();
			return $texte;
	}
	return false;
}

function getmicrotime(){
 list($usec, $sec) = explode(" ", microtime());
 return ((float)$usec + (float)$sec);
}

function test_apinc(){
	global $erreur1,$erreur2,$erreur3;
	$css = find_in_path('validateur.css');
echo <<<html1

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15" />
<link rel="stylesheet" type="text/css" media="screen" href="$css" />
<title>Validateur d'accessibilité</title>
<script type="text/javascript">
<!--
function validation() {
    if (document.getElementById('urlAVerif').value.length < 1) {
	    window.alert("Vous devez indiquer une adresse valide.");
		return false;
}
    if (document.getElementById('urlAVerif').value == "http://") {
	    window.alert("Vous devez indiquer une adresse valide.");
		return false;
	}
	return true;
}
-->
</script>
</head>
<body>
<p>
Validateur
&nbsp;|&nbsp;
<a href="http://validateur-accessibilite.apinc.org/valider_site.htm">Points à vérifier manuellement</a>
&nbsp;|&nbsp;
<a href="http://validateur-accessibilite.apinc.org/wiki/">Fiches techniques</a>
&nbsp;|&nbsp;
<a href="http://validateur-accessibilite.apinc.org/mode_emploi_validateur.htm">Mode d'emploi</a>
&nbsp;|&nbsp;
<a href="http://validateur-accessibilite.apinc.org/licence/index.htm">Licence</a>
&nbsp;|&nbsp;
<a href="http://validateur-accessibilite.apinc.org/validateur.zip">Téléchargement</a>
</p>
<h1>Vérification des règles d'accessibilité version 1</h1>
html1;

//$formulaire=0;
$logo='';
$h6='';
$h5='';
$h4='';
$h2='';
$toto='';
$toto1='';
$toto2='';
$toto42='';
$framesetSansNoframes='';
$embedSansnoembed='';
$tableth='';
$lien_alerte='';
$css='';
$doctype='';
$charset='';
$title='';
$h='';
$label='';
$css='';
$accesskey='';
$lang='';
$tabindexPasPartout='';

$tab="1";
$time_start = getmicrotime();

/*	
Formulaire de saisie 
*/
if($_POST['urlAVerif'])
	$_GET['urlAVerif']=$_POST['urlAVerif'];

if($_GET['urlAVerif']=='' && !isSet($_POST['verifLocale']) && !isSet($_GET['logo'])){
$formulaire="necessaire";
}
//endif;
if($_GET['urlAVerif']=='http://' && !isSet($_POST['verifLocale']) && !isSet($_GET['logo'])){
$formulaire="necessaire";
}
//endif;
if($_GET['urlAVerif']!='' && $_GET['urlAVerif']!='http://'){
	$urlValide = @file ($_GET['urlAVerif']);
		if($urlValide==FALSE){
			$formulaire="necessaire";
		}
}
//endif;
$urlAverif = $_GET['urlAVerif'];
$urlValide = @file ($_GET[urlAVerif]);
if($formulaire=="necessaire"){

	echo "<form id=\"langdecla\" action=\"".generer_url_ecrire("valide_access")."\" method=\"post\" onsubmit=\"return validation();\">";
	echo "<h2><label for=\"urlAVerif\">URL de la page à vérifier : </label></h2>";
	echo "<p>";
	echo "<input type=\"text\" id=\"urlAVerif\" name=\"urlAVerif\" size=\"80\" value=\"http://\" />";
	echo "</p>";
	echo "<p>";
	echo "<input type=\"submit\" name=\"Submit\" value=\"Soumettre\" />";
	echo "</p>";
	echo "</form>";
}
else{
/*
Récupération et préparation du fichier
*/

if($_GET['urlAVerif']!=''){
$adresse_base=$_GET['urlAVerif'];
}
//endif;
if($_GET['logo']=="valide"){
$adresse_base=$_SERVER['HTTP_REFERER'];
}
//endif;


if(isSet($_POST['verifLocale'])){
$html=$_POST['verifLocale'];
}
else{
$html=implode ('', file ($adresse_base));
}
// enlever les commentaires html, ca allege l'analyse surtout si du javascript embarque
$html = preg_replace(",<!--.*-->,Ums","",$html);
	$htp=$html;
	$html=htmlentities($html);
	$html=stripslashes($html);


/*
Contrôles à effectuer sur le fichier complet
*/
/*
Vérification de la présence d'une feuille de style externe
*/
	if(!preg_match("`type\s?=\s?('text/css'|&quot;text/css&quot;)`i",$html)){
	$css='yenapa';
	}
/*
Vérification de la présence d'accesskey
*/
	if(!preg_match('`accesskey`i',$html)){
	$accesskey='yenapa';
	}
/*
Est-ce que la langue du document est déclarée
*/
	if(!preg_match("`[^href]lang\s?=\s?`i",$html)){
	$lang='yenapa';
	}
/*
Est-ce que les entêtes <h1> à <h6> sont utilisés
*/
	if(!preg_match("`&lt;h[1-6]`i",$html)){
	$h='yenapa';
	}
/*
Est-ce que la balise <h1> est utilisée
*/
	if(preg_match('`&lt;h1`i',$html)){
	$h1='yena';
	}
/*
Est-ce que la balise <h2> est utilisée
*/
	if(preg_match('`&lt;h2`i',$html)){
	$h2='yena';
	}
/*
Est-ce que la balise <h3> est utilisée
*/
	if(preg_match('`&lt;h3`i',$html)){
	$h3='yena';
	}
/*
Est-ce que la balise <h4> est utilisée
*/
	if(preg_match('`&lt;h4`i',$html)){
	$h4='yena';
	}
/*
Est-ce que la balise <h5> est utilisée
*/
	if(preg_match('`&lt;h5`i',$html)){
	$h5='yena';
	}
/*
Est-ce que la balise <h6> est utilisée
*/
	if(preg_match('`&lt;h6`i',$html)){
	$h6='yena';
	}
/*
Est-ce que la balise <h6> est utilisée alors qu'il manque une balise précédente
*/
	if($h6=='yena' && ($h5!='yena' or $h4!='yena' or $h3!='yena' or $h2!='yena' or $h1!='yena')){
	$hdes='vrai';
	$mess="Vous avez un &lt;h6&gt; alors qu'il manque un entête plus petit (&lt;h1&gt; à &lt;/h5&gt;";
	}
/*
Est-ce que la balise <h5> est utilisée alors qu'il manque une balise précédente
*/
	elseif($h5=='yena' && ($h4!='yena' or $h3!='yena' or $h2!='yena' or $h1!='yena')){
	$hdes='vrai';
	$mess="Vous avez un &lt;h5&gt; alors qu'il manque un entête plus petit (&lt;h1&gt; à &lt;/h4&gt;";
	}
/*
Est-ce que la balise <h4> est utilisée alors qu'il manque une balise précédente
*/
	elseif($h4=='yena' && ($h3!='yena' or $h2!='yena' or $h1!='yena')){
	$hdes='vrai';
	$mess="Vous avez un &lt;h4&gt; alors qu'il manque un entête plus petit (&lt;h1&gt; à &lt;/h3&gt;";
	}
/*
Est-ce que la balise <h3> est utilisée aors qu'il manque une balise précédente
*/
	elseif($h3=='yena' && ($h2!='yena' or $h1!='yena')){
	$hdes='vrai';
	$mess="Vous avez un &lt;h3&gt; alors qu'il manque un entête plus petit (&lt;h1&gt; à &lt;/h2&gt;";
	}
/*
Est-ce que la balise <h2> est utilisée aors qu'il manque une balise précédente
*/
	elseif($h2=='yena' && $h1!='yena'){
	$hdes='vrai';
	$mess="Vous avez un &lt;h2&gt; alors qu'il n'y a pas de &lt;h1&gt;";
	}
/*
Si il n'y a pas d'erreur détectée dans les entêtes, on n'affiche pas de message d'alerte
*/
	else{
	$hdes='faux';
	}
/*
Est-ce que le doctype est déclaré
*/
	if(!preg_match('`&lt;!DOCTYPE`i',$html)){
	$doctype='yenapa';
	}
/*
Est-ce qu'il y a un tableau alors que les <th> ne sont présents
*/
	if(preg_match('`&lt;table`i',$html) && !preg_match('`&lt;th`i',$html)){
	$tableth='yenapa';
	}
/*
Est-ce que le jeu de caractères est déclaré
*/
	if(!preg_match('`charset=`i',$html)){
	$charset='yenapa';
	}
/*
Est-ce que le titre de la page est déclaré
*/
	if(!preg_match('`&lt;title&gt;(.*)?&lt;/title&gt;`is',$html)){
	$title='yenapa';
	}
/*
Est-ce qu'au moins un formulaire de la page utilise le javascript
*/
$cha='`&lt;form(.*?)onsubmit(.*?)&gt;`i';
preg_match_all($cha,$html,$sortie);
$nb=count($sortie[0]);
for($i=0;$i<$nb;$i++){
$toto=eregi_replace("&lt;form","&lt;form marqueur=&quot;invalide&quot;",$sortie[0][$i]);
//echo $toto."<br><br><br>";
} 
$html=preg_replace($cha,$toto,$html);

/*
Détection des balises noframe et présence d'un lien vers le menu ou un plan du site
*/
//$detect='`&lt;frameset(.*?)&gt;&lt;/frameset&gt;&lt;noframes(.*?)&gt;&lt;a(.*?)&gt;(.*?)&lt;/a&gt;`is';
$detect='`&lt;noframes(.*?)&gt;(.*?)&lt;a(.*?)&gt;(.*?)&lt;/noframes(.*?)&gt;`is';
preg_match_all($detect,$html,$nofram);
$nomb=count($nofram[0]);
if($nomb==0){
$noframesSansLien="yena";
}
/*
detection de frameset sans noframes.
*/
if(preg_match('`&lt;frameset(.*)&gt;`is',$html) && !preg_match('`&lt;noframes(.*?)&gt;`is',$html)){
$framesetSansNoframes="yena";
}
/*
detection de <embed> sans <noembed>.
*/
if(preg_match('`&lt;embed(.*)&gt;`is',$html) && !preg_match('`&lt;noembed(.*?)&gt;`is',$html)){
$embedSansnoembed="yena";
}


$chai='`&lt;select(.*?)onchange(.*?)&gt;`i';
preg_match_all($chai,$html,$sortie1);
$nb1=count($sortie1[0]);
for($i=0;$i<$nb1;$i++){
$toto1=eregi_replace("&lt;select","&lt;select marqueur=&quot;invalide&quot;",$sortie1[0][$i]);
//echo $toto1."<br><br><br>";
} 
$html=preg_replace($chai,$toto1,$html);

$chain='`&lt;input(.*?)type=&quot;submit&quot;(.*?)onclick(.*?)&gt;`i';
preg_match_all($chain,$html,$sortie2);
$nb2=count($sortie2[0]);
for($i=0;$i<$nb2;$i++){
$toto2=eregi_replace("&lt;input","&lt;input marqueur=&quot;invalide&quot;",$sortie2[0][$i]);
//echo $toto2."<br><br><br>";
} 
$html=preg_replace($chain,$toto2,$html);


$lienPasClair='`&lt;a(.*?)&gt;(cliquez ici|click|ici|lire|lire la suite)&lt;/a&gt;`is';
preg_match_all($lienPasClair,$html,$sortie42);
$nb42=count($sortie42[0]);
for($i=0;$i<$nb42;$i++){
$toto42=eregi_replace("&lt;/a&gt;","&lt;/a marqueur=&quot;invalide&quot;&gt;",$sortie2[0][$i]);
//echo $toto2."<br><br><br>";
} 
$html=preg_replace($lienPasClair,$toto42,$html);


/*
Est-ce que les <label> sont présent là où il faut
*/
	if(preg_match('`&lt;[^/](input|select|textarea)`i',$html) && !preg_match('`&lt;label`i',$html)){
	$label='yenapa';
	}
/*
Est-ce qu'il y a un ou plusieurs <textarea> vides
*/
	if(preg_match('`&lt;textarea(.*)?&gt;&lt;/textarea&gt;`is',$html) or preg_match('`&lt;textarea(.*)?&gt;(\r|\n|\r\n|\n\r)&lt;/textarea&gt;`is',$html)){
	$textareaVide='yena';
	}
	
/*
	$chaine='`&lt;textarea(.*)&gt;&lt;/textarea&gt;`i';
preg_match_all($chaine,$html,$sortie4);
$nb4=count($sortie4[0]);
for($i=0;$i<$nb4;$i++){
$toto4=eregi_replace("&lt;textarea","&lt;textarea marqueur=&quot;invalide&quot;",$sortie4[0][$i]);
echo $toto4."<br><br><br>";
} 

$html=preg_replace($chaine,$toto4,$html);
*/
	
/*
Déterminer le nb de textarea présent
*/
	preg_match_all('`&lt;textarea`i',$html,$output);
	$nbtextarea=count($output[0]); 
/*
Est-ce que des tabindex sont déclarés
*/
	$motif="`(tabindex=)`";
	preg_match_all($motif,$html,$out);
	$nbtab=count($out[0]); 
	if($nbtab==0){
	$tabindex='yenapa';
	}
	else{
	$tabindex='yena';
	}






/*
Compter le nb de <table> présent sur la page
*/
	$motif2="`&lt;table`i";
	preg_match_all($motif2,$html,$out2);
	$nbtable=count($out2[0]);
if($nbtable=='1'){
$messageTable="le tableau présent";
}
else{
$messageTable="les $nbtable tableaux présents";
}
/*
Vérifier le doublement des liens d'une image map
*/
	$motif989="`&lt;map(.*?)&gt;(.*?)&lt;a(.*?)href=&quot;(.*?)&quot;(.*?)&gt;(.*?)&lt;/map&gt;`is";
	preg_match_all($motif989,$html,$outkl);
	$mapliens=count($outkl[0]);
		$lienParMap="`href=&quot;(.*?)&quot;`";
		for($i=0;$i<$mapliens;$i++){
			preg_match_all($lienParMap,$outkl[0][$i],$out1442);
			$nbliens=count($out1442[0]);
			for($ii=0;$ii<$nbliens;$ii++){
				$motif5858='`'.$out1442[0][$ii].'`';
				preg_match_all($motif5858,$html,$sortie114);
				$nb_sortie=count($sortie114[0]);
					if($nb_sortie<2){
					$bebebe=eregi_replace('href','hrefpasbon',$out1442[0][$ii]);
					$html=preg_replace($motif5858,$bebebe,$html);
					}
					else{
					}
			}
		}
/*
Vérifier que tous les liens ayant le même nom pointent au même endroit

$motif="`&lt;a (.*?)href=&quot;(.*?)&quot(.*?)&gt;(.*?)&lt;/a&gt;`i";
preg_match_all($motif,$html,$outputlien);
$nbLiens=count($outputlien[0]);
	for($i=0;$i<$nbLiens;$i++){
	echo $outputlien[4][$i]."<br />";
		$motif2='`&lt;a (.*?)href=&quot;(.*?)&quot(.*?)&gt;'.$outputlien[4][$i].'&lt;/a&gt;`is';
		preg_match_all($motif2,$html,$outputlien2);
		echo $outputlien2[0][$i]."<br />";
		
	}
*/

/*
Préparation du fichier pour l'analyse ligne par ligne
*/
	$html=str_replace('&gt;',"&gt;<br />",$html);
	if(!eregi('^(&lt;/)',$html)){
	$html=str_replace('&lt;/',"<br />&lt;/",$html);
	}
	
	$html=preg_replace('(\r|\n|\r\n|\n\r)',"",$html);
	//$html=str_replace('',"",$html);
	$html=explode('<br />',$html);
	$nb=count($html);
	$resultat="";
	global $erreur1;
	global $erreur2;
	global $erreur3;
	$avertissement1=0;
	$erreur1=0;
	$avertissement2=0;
	$erreur2=0;
	$avertissement3=0;
	$erreur3=0;
	$stycss=0;
 	$citation='2';
	$table="3";
	echo "<h2>Url de la page : $adresse_base</h2>";
	echo "<p class=\"gauche\">";
	
	
	
	
	
	for($i=0;$i<$nb;$i++){
	if(!preg_match('`(.*)?(&lt;/)`',$html[$i])){
	$html[$i]=str_replace('&lt;/',"<br />&lt;/",$html[$i]);
	}
	else{
	}
			if($html[$i]!=""){
			$resultat.="$html[$i]<br />\r\n";
			}
			

			
			
/*Règles de priorité 1*/
		if($_GET['niveau']=='niveau1' or $_GET['niveau']=='niveau2' or $_GET['niveau']=='niveau3' or $_GET['niveau']==''){
/*Règles de priorité 1*/

/*
On vérifie que la ligne contienne une balise frame ou frameset 
et qu'elle ne contient pas d'attribut title ou seulement un 
attribut title vide.
*/
			if(preg_match('`&lt;frame`i',$html[$i]) && (!eregi('title',$html[$i]) or preg_match('`title\s?=\s?&quot;&quot;`',$html[$i]) or preg_match("`title\s?=\s?''`",$html[$i]))){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/12_1_frame_nom\" tabindex=\"$tab\" title=\"lien vers la page sur les frames\">Priorité 1 - Vous devez nommer vos frames et votre frameset.</a></div><p>";
			$erreur1++;
			$tab++;
			}
			else{}

/*
On vérifie que la ligne contient une balise </frameset> et on affiche un message 
d'erreur si il n'y a pas de balises <noframes></noframes>
*/
			if($framesetSansNoframes=="yena" && preg_match('`&lt;/frameset`i',$html[$i])){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/12_2_frameset_sans_noframes\" tabindex=\"$tab\" title=\"lien vers la page sur les frames\">Priorité 1 - Vous devez mettre en place les balises &lt;noframes&gt; et rendre votre site accessible à ceux qui ne peuvent pas afficher les frames.</a></div><p>";
			$erreur1++;
			$tab++;
			}
			else{}

/*
On vérifie que la ligne contient une balise </embed> et on affiche un message 
d'erreur si il n'y a pas de balises <noembed></noembed>
*/
			if($embedSansnoembed=="yena" && preg_match('`&lt;/embed`i',$html[$i])){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/06_3_embed_sans_noembed\" tabindex=\"$tab\" title=\"lien vers la page sur le multimedia\">Priorité 1 - Vous devez mettre en place les balises &lt;noembed&gt; et offrir une alternative pour ceux qui utilisent un navigateur ne supportant pas l'inclusion d'objets multimedia.</a></div><p>";
			$erreur1++;
			$tab++;
			}
			else{}

/*
On vérifie que la ligne contient une balise </noframe> et on affiche un message 
d'erreur si il n'y a pas de lien d'indiqué entre <noframes> et </noframes>
*/
			if($noframesSansLien=="yena" && preg_match('`&lt;/noframes`i',$html[$i])){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/12_2_noframes_sans_lien\" tabindex=\"$tab\" title=\"lien vers la page sur les frames\">Priorité 1 - utilisez les balises &lt;noframes&gt; pour mettre en place un lien vers une page contenant les liens vers toutes les pages de votre site.</a></div><p>";
			$erreur1++;
			$tab++;
			}
			else{}



/*
On vérifie que la ligne contienne une balise appelant une zone d'image map 
et qu'il n'y ai pas d'attribut alt ou que celui-ci soit vide.
*/
			if(preg_match('`shape\s?=`i',$html[$i]) && preg_match('`area`i',$html[$i]) && (!preg_match('`alt`i',$html[$i]) or preg_match("`alt\s?=\s?(''|&quot;&quot;)`i",$html[$i]))){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/01_1_map_alt\" tabindex=\"$tab\" title=\"lien vers la page sur les images map\">Priorité 1 - Vous devez mettre un attribut alt avec toutes les zones de votre image réactive.</a></div><p>";
			$erreur1++;
			$tab++;
			}
			else{}
/*
On vérifie que la ligne contienne une balise img 
et qu'il n'y ai pas d'attribut alt.
*/
			if(preg_match('`&lt;img`i',$html[$i]) && !preg_match('`alt`i',$html[$i])){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/01_1_image_alt\" tabindex=\"$tab\" title=\"lien vers la page sur les images\">Priorité 1 - Vous devez mettre un attribut alt avec toutes vos images.</a></div><p>";
			$erreur1++;
			$tab++;
			}
			else{}
/*
On vérifie que la ligne contienne une balise <table> 
et si il n'y a pas de <th> déclaré, on affiche 1 fois 
le message d'alerte.
*/
			if($tableth=='yenapa' && $table=="3" && preg_match('`&lt;table(.*?)&gt;`i',$html[$i])){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Difficile ! </span><a href=\"wiki/wiki/05_1_tableau_th\" tabindex=\"$tab\" title=\"lien vers la page sur les tableaux\">Priorité 1 - Identifiez les entêtes &lt;th&gt; des tableaux de données. Vérifiez que vous n'utilisez pas $messageTable sur votre page à des fins de présentation.</a></div><p>";
			$erreur1++;
			$table++;
			$tab++;
			}
			else{}
/*
On vérifie que la ligne contienne un lien 
et si celui-ci mêne nulle part, on affiche une alerte.
*/
			if(preg_match("`&lt;a(.*?)href\s?=\s?(&quot;#&quot;|'#')(.*?)&gt;`i",$html[$i])
				// ne faire une alerte que le lien comporte un on(click|press|..)
			 AND preg_match(",on(click|press)\s*=,i",$html[3])){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/06_3_lien_javascript\" tabindex=\"$tab\" title=\"lien vers la page sur les liens\">Priorité 1 - Ce type de lien ne fonctionne pas si le javascrit est désactivé.</a></div><p>";
			$erreur1++;
			$tab++;
			$lien_alerte="invalide";
			}
			else{}
/*
On vérifie si une image bmp est utilisée.
*/
			if(preg_match('`&lt;img(.*)src=&quot;(.*).bmp&quot;(.*)&gt;`i',$html[$i])){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/11_1_image_bmp\" tabindex=\"$tab\" title=\"lien vers la page sur les images\">Priorité 1 - Le format bmp n'est pas destiné au web, utilisez png, jpg ou gif.</a></div><p>";
			$avertissement1++;
			$tab++;
			}
			else{}
			
		if($_GET['avert']!='pasAvertissement'){
		
/*
On vérifie que la ligne contienne une balise appelant un script
*/
			if(preg_match('`&lt;(object|script|embed|applet)`i',$html[$i])){
			$resultat.="</p><div class=\"orange\"><span class=\"facile\">Variable </span><a href=\"wiki/wiki/06_5_script_texte\" tabindex=\"$tab\" title=\"lien vers la page sur les script\">Priorité 1 - Assurez-vous que la page est consultable sans ce script et/ou que le contenu soit doublé d'une version accessible sans l'utilisation de langage de script.</a></div><p>";
			$avertissement1++;
			$tab++;
			}
			else{}
/*
On vérifie que la ligne contienne une balise appelant 
une image map côté serveur.
*/
			if(preg_match('`ismap`i',$html[$i])){
			$resultat.="</p><div class=\"orange\"><a href=\"wiki/wiki/09_1_map_serveur\" tabindex=\"$tab\" title=\"lien vers la page sur les images map\">Priorité 1 - Vous devriez utiliser de préférence une image map côté client.</a></div><p>";
			$resultat.="</p><div class=\"orange\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/01_2_map_lien\" tabindex=\"$tab\" title=\"lien vers la page sur les images map\">Priorité 1 - Assurez-vous que l'ensemble des liens soient repris sous forme de texte.</a></div><p>";
			$avertissement1=$avertissement1+2;
			$tab++;
			}
			else{}
/*
On vérifie que la ligne contienne un lien 
et que celui-ci fasse appel à du javascript.
*/
			if($lien_alerte!="invalide" && preg_match('`onclick`i',$html[$i]) && preg_match('`&lt;a`i',$html[$i]) && preg_match('`#`i',$html[$i])){
			$resultat.="</p><div class=\"orange\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/06_3_lien_javascript\" tabindex=\"$tab\" title=\"lien vers la page sur les liens\">Priorité 1 - Assurez-vous que ce lien fonctionne sans le javascript.</a></div><p>";
			$avertissement1++;
			$tab++;
			}
			else{}
/*
On vérifie que la ligne contienne une balise <form> 
et si on a détecté précédemment l'usage de javascript 
sur un formulaire de la page, on affiche une alerte.
*/
			if(preg_match('`&lt;form marqueur=&quot;invalide&quot;`i',$html[$i])){
			$resultat.="</p><div class=\"orange\"><span class=\"facile\">Variable </span><a href=\"wiki/wiki/06_3_formulaire_javascript\" tabindex=\"$tab\" title=\"lien vers la page sur les formulaire\">Priorité 1 - Assurez-vous que ce formulaire fonctionne si le javascrit est désactivé.</a></div><p>";
			$avertissement1++;
			$tab++;
			}
			else{}
			
/*
On vérifie que la ligne contienne une balise <select> 
et si on a détecté précédemment l'usage de javascript 
sur un formulaire de la page, on affiche une alerte.
*/
			if(preg_match('`&lt;select marqueur=&quot;invalide&quot;`i',$html[$i])){
			$resultat.="</p><div class=\"orange\"><span class=\"facile\">Variable </span><a href=\"wiki/wiki/06_3_formulaire_javascript\" tabindex=\"$tab\" title=\"lien vers la page sur les formulaire\">Priorité 1 - Assurez-vous que ce formulaire fonctionne si le javascrit est désactivé.</a></div><p>";
			$avertissement1++;
			$tab++;
			}
			else{}
			
/*
On vérifie que la ligne contienne une balise <input> 
et si on a détecté précédemment l'usage de javascript 
sur un formulaire de la page, on affiche une alerte.
*/
			if(preg_match('`&lt;input marqueur=&quot;invalide&quot;`i',$html[$i])){
			$resultat.="</p><div class=\"orange\"><span class=\"facile\">Variable </span><a href=\"wiki/wiki/06_3_formulaire_javascript\" tabindex=\"$tab\" title=\"lien vers la page sur les formulaire\">Priorité 1 - Assurez-vous que ce formulaire fonctionne si le javascrit est désactivé.</a></div><p>";
			$avertissement1++;
			$tab++;
			}
			else{}
			
			
/*
On vérifie que la ligne contienne une balise <img> 
et un attribut alt vide.
*/
			if(preg_match("`&lt;img(.*)alt\s?=\s?(''|&quot;&quot;)`i",$html[$i])){
			$resultat.="</p><div class=\"orange\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/01_1_image_alt_vide\" tabindex=\"$tab\" title=\"lien vers la page sur les images\">Priorité 1 - Vous avez mis un attribut alt vide, est-ce volontaire ?.</a></div><p>";
			$avertissement1++;
			$tab++;
			}
			else{}
}
		}
		else{
		}
		
		
		
		
		
/*Règles de priorité 2*/
		if($_GET['niveau']=='niveau2' or $_GET['niveau']=='niveau3' or $_GET['niveau']==''){
/*
Si il n'y a pas d'appel à une feuille de style externe, 
on affiche une alerte apres </head>.
*/
			if($css=='yenapa' && preg_match('`&lt;/head`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/03_3_css\" tabindex=\"$tab\" title=\"lien vers la page sur les css\">Priorité 2 - Vous devriez utiliser une feuille de style externe.</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}
/*
On vérifie que la ligne contienne une balise <frame> ou <frameset> 
et si il n'y a pas de longdesc, on affiche une alerte.
*/
			if(preg_match('`&lt;frame`i',$html[$i]) && !preg_match('`longdesc`',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Très facile ! </span><a href=\"glossaire./12_1_frame_nom\" tabindex=\"$tab\" title=\"lien vers la page sur les descriptions de frame\">Priorité 2 - Décrivez correctement vos frames et complétez la partie &lt;noframes&gt;... &lt;/noframes&gt;.</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}
/*
Si il n'y a pas de doctype déclaré, on affiche une alerte 
après le </head>.
*/
			if($doctype=='yenapa' && preg_match('`&lt;/head`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/03_2_doctype\" tabindex=\"$tab\" title=\"lien vers la page sur les doctypes\">Priorité 2 - Vous devez déclarer un doctype et valider votre document.</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}
/*
Si il n'y a pas de définition de caractères déclarée, on affiche une alerte 
après le </head>.
*/
			if($charset=='yenapa' && preg_match('`&lt;/head`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/03_2_charset\" tabindex=\"$tab\" title=\"lien vers la page sur les doctypes\">Priorité 2 - Vous devez déclarer l'encodage de votre page.</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}

/*
Si il n'y a pas de <link rel="stylesheet" type="text/css" media="screen" href="../validateur.css" />
<title></title> déclaré, on affiche une alerte 
après le </head>.
*/
			if($title=='yenapa' && preg_match('`&lt;/head`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/03_5_title\" tabindex=\"$tab\" title=\"lien vers la page sur le meta-données\">Priorité 2 - Vous devriez indiquer le titre de votre document.</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}
/*
On vérifie que la ligne contienne une balise forcant 
le rafraichîssement de la page.
*/
			if(preg_match("`http-equiv\s?=\s?(&quot;refresh&quot;|'refresh')`i",$html[$i]) && !preg_match('`url`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Variable </span><a href=\"wiki/wiki/07_4_refresh\" tabindex=\"$tab\" title=\"lien vers la page sur les rafraichissements\">Priorité 2 - Ne forcez pas le rafraîchissement d'une page.</a></div><p>";
			$erreur2=$erreur2+2;
			$tab++;
			//$refresh='yena';
			}
			else{}
/*
On vérifie que la ligne contienne une balise forcant 
la redirection de la page.
*/
			if(preg_match("`http-equiv\s?=\s?(&quot;refresh&quot;|'refresh')`i",$html[$i]) && preg_match('`url`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Variable </span><a href=\"wiki/wiki/07_5_redirection\" tabindex=\"$tab\" title=\"lien vers la page sur les redirections\">Priorité 2 - Préférez les redirections \"côté serveur\".</a></div><p>";
			$erreur2=$erreur2+2;
			$tab++;
			}			
			else{}
/*
On vérifie que la ligne contienne une balise dépréciée (font) : 
*/
			if(preg_match("`&lt;font\s?=`i",$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/11_2_deprecie\" tabindex=\"$tab\" title=\"lien vers la page sur les technologies dépréciées\">Priorité 2 - Remplacez la balise font dépréciée par son équivalent en css.</a></div><p>";
			$erreur2++;
			$tab++;
			}			
			else{}
/*
On vérifie que la ligne contienne une balise dépréciée (bgcolor) : 
*/
			if(preg_match("`&lt;(.*?)bgcolor\s?=(.*?)&gt;`i",$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/11_2_deprecie\" tabindex=\"$tab\" title=\"lien vers la page sur les technologies dépréciées\">Priorité 2 - Remplacez l'attribut bgcolor déprécié par son équivalent en css.</a></div><p>";
			$erreur2++;
			$tab++;
			}			
			else{}
/*
On vérifie que la ligne contienne une balise dépréciée (border) : 
*/
			if(preg_match("`&lt;(.*?)border\s?=(.*?)&gt;`i",$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/11_2_deprecie\" tabindex=\"$tab\" title=\"lien vers la page sur les technologies dépréciées\">Priorité 2 - Remplacez l'attribut border déprécié par son équivalent en css.</a></div><p>";
			$erreur2++;
			$tab++;
			}			
			else{}
/*
On vérifie que la ligne contienne une balise dépréciée (center) : 
*/
			if(preg_match("`&lt;(.*?)[^:]/s?center(.*?)&gt;`i",$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/11_2_deprecie\" tabindex=\"$tab\" title=\"lien vers la page sur les technologies dépréciées\">Priorité 2 - Remplacez la balise center dépréciée par son équivalent en css.</a></div><p>";
			$erreur2++;
			$tab++;
			}			
			else{}
/*
Si aucune balise <h1> à <h6> n'est déclarée, 
on affiche une alerte juste après <body>.
*/
			if($h=='yenapa' && preg_match('`&lt;body`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">variable ! </span><a href=\"wiki/wiki/03_5_h\" tabindex=\"$tab\" title=\"lien vers la page sur la sémantique\">Priorité 2 - Structurez votre document à l'aide des balises &lt;h1&gt; à &lt;h6&gt;.</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}

/*
On vérifie si il n'y a pas un lien pas assez explicite.
*/
			if(preg_match('`&lt;/a marqueur=&quot;invalide&quot;&gt;`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/13_1_lien_clair\" tabindex=\"$tab\" title=\"lien vers la page sur les liens explicites\">Priorité 2 - Vos liens doivent être plus explicites</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}
/*
On vérifie si les lignes devant recevoir un <label> sont conformes 
en évitant les alertes sur la ligne de submit.
*/
			if($label=='yenapa' && preg_match('`&lt;(input|select|textarea)`i',$html[$i]) && !preg_match('`submit`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/10_2_label\" tabindex=\"$tab\" title=\"lien vers la page sur les labels\">Priorité 2 - Vous devriez mettre en place un &lt;label&gt;...&lt;/label&gt;</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}
/*
On vérifie que la balise label contienne bien l'attribut for="".
*/
			if(preg_match('`&lt;label`i',$html[$i]) && !preg_match('`for`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/12_4_labelfor\" tabindex=\"$tab\" title=\"lien vers la page sur les labels et les for\">Priorité 2 - Définissez la cible de votre &lt;label&gt; à l'aide de for=\"id_cible\"</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}
			
			
			
		if($_GET['avert']!='pasAvertissement'){
/*
On vérifie que les tailles sont bien définies en relatif.
*/
			if(preg_match('`&lt;(table|tr|td|div|span)(.*?)px(.*?)&gt;`i',$html[$i])){
			$resultat.="</p><div class=\"orange2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/03_4_taille_relative\" tabindex=\"$tab\" title=\"lien vers la page sur les css\">Priorité 2 - Utilisez plutôt des tailles relatives</a></div><p>";
			$avertissement2++;
			$tab++;
			}
			else{}
/*
On vérifie si la ligne contient un appel 
pour ouvrir une nouvelle fenêtre.
*/
			if(preg_match("`(window.open|window.resize|target\s?=\s?&quot;_blank&quot;|target\s?=\s?'_blank')`i",$html[$i])){
			$resultat.="</p><div class=\"orange2\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/10_1_new_win\" tabindex=\"$tab\" title=\"lien vers la page sur les nouvelles fenêtre\">Priorité 2 - N'ouvrez pas de nouvelle fenêtre et ne modifiez pas celle en cours sans prévenir clairement le visiteur.</a></div><p>";
			$avertissement2++;
			$tab++;
			}
			else{}
/*
On vérifie si la ligne contient un gestionnaire d'événement 
ne fonctionnant qu'avec une souris.
*/
			if(preg_match("`(ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout)`i",$html[$i])){
			$resultat.="</p><div class=\"orange2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/06_4_gestionnaire\" tabindex=\"$tab\" title=\"lien vers la page sur les gestionnaire d'evenements\">Priorité 2 - Utilisez de préférence des gestionnaires d'événements qui fonctionnent avec autre chose qu'une souris.</a></div><p>";
			$avertissement2++;
			$tab++;
			}
			else{}
/*
On vérifie si la ligne contient une balise de citation 
<q>, <cite> ou <blocquote> et on demande la confirmation 
du bon usage de celle-ci.
*/
			if($citation=='2' && preg_match("`(&lt;q&gt;|&lt;cite&gt;|&lt;blockquote&gt;)`i",$html[$i])){
			$resultat.="</p><div class=\"orange2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/03_7_citation\" tabindex=\"$tab\" title=\"lien vers la page sur les gestionnaire d'evenements\">Priorité 2 - Vérifiez que vous utilisez bien les balises &lt;q&gt; &lt;cite&gt; &lt;blockquote&gt;  pour introduire une citation.</a></div><p>";
			$avertissement2++;
			$tab++;
			$citation++;
			}
			else{}
/*
On vérifie si la ligne contient une balise ou une extension 
démontrant l'usage de parties mobiles sur la page et on affiche 
une alerte.
*/
			if(preg_match("`(\.swf|\.mng|&lt;blink|&lt;marquee)`i",$html[$i])){
			$resultat.="</p><div class=\"orange2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/07_2_image_clignote\" tabindex=\"$tab\" title=\"lien vers la page sur les pages qui clignotent\">Priorité 2 - l'usage d'images animées, de clignotement ou de défilement est déconseillé.</a></div><p>";
			$avertissement2++;
			$tab++;
			}
			else{}
			
/*
On vérifie si la ligne contient une définition de style 
qui pourrait être mis dans une feuille externe.
*/
			if($stycss==0 && preg_match("`style\s?=\s?('|&quot;)`i",$html[$i])){
			$resultat.="</p><div class=\"orange2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/03_3_css_locale\" tabindex=\"$tab\" title=\"lien vers la page sur les css\">Priorité 2 - Vérifiez si il n'est pas plus simple de regrouper vos déclarations de style dans une feuille de style externe. En déclarant vos styles CSS au sein de chaque page, vous perdez un des avantages des CSS, à savoir la facilité de maintenance.</a></div><p>";
			$avertissement2++;
			$tab++;
			$stycss++;
			}
			else{}
/*
On vérifie si la ligne contient une définition de style 
qui pourrait être mis dans une feuille externe.
*/
			if($css=='yenapa' && preg_match("`class\s?=`i",$html[$i])){
			$resultat.="</p><div class=\"orange2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/03_3_css_locale\" tabindex=\"$tab\" title=\"lien vers la page sur les css\">Priorité 2 - En déclarant vos styles CSS au sein de chaque page, vous perdez un des avantages des CSS, à savoir la facilité de maintenance.</a></div><p>";
			$avertissement2++;
			$tab++;
			}
			else{}
/*
Si on a détecté une erreur dans l'imbrication des entête <h1 à 6>,
on affiche une alerte juste après <body>
*/
			if($hdes=='vrai' && preg_match('`&lt;body`i',$html[$i])){
			$resultat.="</p><div class=\"orange2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/03_5_hdes\" tabindex=\"$tab\" title=\"lien vers la page sur la sémantique\">Priorité 2 - $mess</a></div><p>";
			$avertissement2++;
			$tab++;
			}
			else{}
		}
		}
		else{
		}
		
		
		
/*Règles de priorité 3*/
		if($_GET['niveau']=='niveau3' or $_GET['niveau']==''){
		/*Règles de priorité 3*/
		
/*
Si le document ne contient pas d'accesskey, on met 
une alerte juste après <body>
*/
	if($accesskey=='yenapa' && preg_match('`&lt;body`i',$html[$i])){
	$resultat.="</p><div class=\"rouge3\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/09_5_accesskey\" tabindex=\"$tab\" title=\"lien vers la page sur les css\">Priorité 3 - Vous devriez mettre en place des raccourcis clavier (accesskey).</a></div><p>";
	$erreur3++;
	$tab++;
	}
	else{}

/*
On vérifie si la langue du document a bien été 
déclarée sinon on met une alerte.
*/
	if($lang=='yenapa' && preg_match('`&lt;/head`i',$html[$i])){
	$resultat.="</p><div class=\"rouge3\"><span class=\"facile\">Très facile ! </span><a href=\"wiki/wiki/04_3_lang\" tabindex=\"$tab\" title=\"lien vers la page sur la déclaration de la langue\">Priorité 3 - Vous devriez indiquer la langue principale du document.</a></div><p>";
	$erreur3++;
	$tab++;
	}			
	else{}

/*
On vérifie si la ligne contient une image réactive 
et on affiche une alerte pour les liens textes en doublure.
*/
			if(preg_match('`hrefpasbon`i',$html[$i])){
			$resultat.="</p><div class=\"rouge3\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/01_5_map_double\" tabindex=\"$tab\" title=\"lien vers la page sur les images map\">Priorité 3 - Vérifiez que vous avez bien repris sous une forme classique l'ensemble des liens de votre image réactive.</a></div><p>";
			$erreur3++;
			$tab++;
			}
			else{}



		if($_GET['avert']!='pasAvertissement'){

/*
Si la page ne contient aucun tabindex, on affiche une alerte 
juste après <body>.
*/
			if($tabindex=='yenapa' && preg_match('`&lt;body`i',$html[$i])){
			$resultat.="</p><div class=\"orange3\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/09_4_tabindex\" tabindex=\"$tab\" title=\"lien vers la page sur les tabindex\">Priorité 3 - Vérifiez si l'ordre de navigation à l'aide de la touche tab est logique, en cas de réponse négative, mettez en place des tabindex.</a></div><p>";
			$avertissement3++;
			$tab++;
			}
			else{}
/*
On vérifie si au moins un tabindex est déclaré et si oui 
si toutes les balises le requérant en ont bien un de déclaré.
*/
			if($tabindex=='yena' && $tabindexPasPartout != "houi" && preg_match('`(&lt;a |&lt;input|&lt;area|&lt;button|&lt;object|&lt;select|&lt;textarea)`i',$html[$i]) && !preg_match('`tabindex`i',$html[$i]) && !preg_match('`(hidden|acronym|link|&lt;/|address)`i',$html[$i])){
			$resultat.="</p><div class=\"orange3\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/09_4_tabindex\" tabindex=\"$tab\" title=\"lien vers la page sur les tabindex\">Priorité 3 - Vous avez mis en place certains tabindex mais pas de partout où il en faudrait, est-ce délibéré ?</a></div><p>";
			$avertissement3++;
			$tabindexPasPartout = "houi";
			$tab++;
			}
			else{}

		}
		}
	}
	echo "</p>";

/*
On affiche le nb d'erreurs et d'avertissements 
*/

	if($erreur1=='0' && $erreur2=='0' && $erreur3=='0'){
	echo "<p class=\"felicitation\">";	
	echo "<strong>Bravo ! </strong>il y n'y a pas d'erreurs de détectées sur votre page, prenez tout de 
	même le temps de vérifier les éventuels avertissements et de valider les points non vérifiables 
	automatiquement.";
	echo "</p>";	
	}

	echo "<p>";
	echo "<strong>Priorité 1 :</strong> ";
	if($erreur1=='0'){
	echo "<span class=\"nberreurszero\">Il y a $erreur1 erreur </span>";
	}
	elseif($erreur1=='1'){
	echo "<span class=\"nberreurs\">Il y a $erreur1 erreur</span> ";
	}
	else{
	echo "<span class=\"nberreurs\">Il y a $erreur1 erreurs</span> ";
	}
	if($avertissement1=='0'){
	echo "<span class=\"nbavertissement\">et $avertissement1 avertissement</span><br />";
	}
	elseif($avertissement1=='1'){
	echo "<span class=\"nbavertissement\">et $avertissement1 avertissement</span><br />";
	}
	else{
	echo "<span class=\"nbavertissement\">et $avertissement1 avertissements</span><br />";
	}
	echo "<strong>Priorité 2 :</strong> ";
	if($erreur2=='0'){
	echo "<span class=\"nberreurszero\">Il y a $erreur2 erreur </span>";
	}
	elseif($erreur2=='1'){
	echo "<span class=\"nberreurs\">Il y a $erreur2 erreur</span> ";
	}
	else{
	echo "<span class=\"nberreurs\">Il y a $erreur2 erreurs</span> ";
	}
	if($avertissement2=='0'){
	echo "<span class=\"nbavertissement\">et $avertissement2 avertissement</span><br />";
	}
	elseif($avertissement2=='1'){
	echo "<span class=\"nbavertissement\">et $avertissement2 avertissement</span><br />";
	}
	else{
	echo "<span class=\"nbavertissement\">et $avertissement2 avertissements</span><br />";
	}
	echo "<strong>Priorité 3 :</strong> ";
	if($erreur3=='0'){
	echo "<span class=\"nberreurszero\">Il y a $erreur3 erreur </span>";
	}
	elseif($erreur3=='1'){
	echo "<span class=\"nberreurs\">Il y a $erreur3 erreur</span> ";
	}
	else{
	echo "<span class=\"nberreurs\">Il y a $erreur3 erreurs</span> ";
	}
	if($avertissement3=='0'){
	echo "<span class=\"nbavertissement\">et $avertissement3 avertissement</span><br />";
	}
	elseif($avertissement3=='1'){
	echo "<span class=\"nbavertissement\">et $avertissement3 avertissement</span><br />";
	}
	else{
	echo "<span class=\"nbavertissement\">et $avertissement3 avertissements</span><br />";
	}

	echo "</p>";
	echo <<<html2
<p>
En plus des points signalés directement dans le code, veuillez prendre garde à respecter les 
<a href="valider_site.htm">points non vérifiables automatiquement</a>.
</p>
<h2>Choisir le niveau de validation</h2>
<form action="index.php" id="niveau">
<p>
<label for="niveau1">Niveau 1 : </label><input type="radio" name="niveau" id="niveau1" value="niveau1" /><br /> 
<label for="niveau2">Niveau 2 : </label><input type="radio" name="niveau" id="niveau2" value="niveau2" /><br />
<label for="niveau3">Niveau 3 : </label><input type="radio" name="niveau" id="niveau3" value="niveau3" /><br />
<label for="avert">Que les erreurs : </label><input type="checkbox" name="avert" id="avert" value="pasAvertissement" /><br />
<input type="hidden" name="urlAVerif" id="urlAVerif" value="$urlAverif" />
<input type="submit" name="revalider" value="Revalider votre document" />
</p>
</form>
html2;
/*
On affiche le code avec les messages d'erreurs.
*/
	echo "<h2>Voici le code source de la page :</h2>";
	$resultat=eregi_replace("marqueur=&quot;invalide&quot;","",$resultat);
	$resultat=eregi_replace("hrefpasbon","href",$resultat);
	echo "<p class=\"gauche\">$resultat</p>";


/*
On prend l'heure de fin de traitement et on affiche 
la durée de préparation de la page.
*/
$time_end = getmicrotime();
$time = $time_end - $time_start;
$time=number_format($time,3,","," ");
//echo "<p>Page générée en $time secondes</p>";
	}
	echo <<<html3
</body>
</html>
html3;
}
?>