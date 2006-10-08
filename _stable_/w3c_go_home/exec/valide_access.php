<?php

function exec_valide_access(){
	?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo find_in_path('validateur.css');?>" />
<title>Validateur d'accessibilit�</title>
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
<a href="http://validateur-accessibilite.apinc.org/valider_site.htm">Points � v�rifier manuellement</a>
&nbsp;|&nbsp;
<a href="http://validateur-accessibilite.apinc.org/wiki/">Fiches techniques</a>
&nbsp;|&nbsp;
<a href="http://validateur-accessibilite.apinc.org/mode_emploi_validateur.htm">Mode d'emploi</a>
&nbsp;|&nbsp;
<a href="http://validateur-accessibilite.apinc.org/licence/index.htm">Licence</a>
&nbsp;|&nbsp;
<a href="http://validateur-accessibilite.apinc.org/validateur.zip">T�l�chargement</a>
</p>
<h1>V�rification des r�gles d'accessibilit� version 1</h1>

<?php

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
include_once('valide_fonctions.php');
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

$urlValide = @file ($_GET[urlAVerif]);
if($formulaire=="necessaire"){

	echo "<form id=\"langdecla\" action=\"".generer_url_ecrire("valide_access")."\" method=\"post\" onsubmit=\"return validation();\">";
	echo "<h2><label for=\"urlAVerif\">URL de la page � v�rifier : </label></h2>";
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
R�cup�ration et pr�paration du fichier
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
	$htp=$html;
	$html=htmlentities($html);
	$html=stripslashes($html);


/*
Contr�les � effectuer sur le fichier complet
*/
/*
V�rification de la pr�sence d'une feuille de style externe
*/
	if(!preg_match("`type\s?=\s?('text/css'|&quot;text/css&quot;)`i",$html)){
	$css='yenapa';
	}
/*
V�rification de la pr�sence d'accesskey
*/
	if(!preg_match('`accesskey`i',$html)){
	$accesskey='yenapa';
	}
/*
Est-ce que la langue du document est d�clar�e
*/
	if(!preg_match("`[^href]lang\s?=\s?`i",$html)){
	$lang='yenapa';
	}
/*
Est-ce que les ent�tes <h1> � <h6> sont utilis�s
*/
	if(!preg_match("`&lt;h[1-6]`i",$html)){
	$h='yenapa';
	}
/*
Est-ce que la balise <h1> est utilis�e
*/
	if(preg_match('`&lt;h1`i',$html)){
	$h1='yena';
	}
/*
Est-ce que la balise <h2> est utilis�e
*/
	if(preg_match('`&lt;h2`i',$html)){
	$h2='yena';
	}
/*
Est-ce que la balise <h3> est utilis�e
*/
	if(preg_match('`&lt;h3`i',$html)){
	$h3='yena';
	}
/*
Est-ce que la balise <h4> est utilis�e
*/
	if(preg_match('`&lt;h4`i',$html)){
	$h4='yena';
	}
/*
Est-ce que la balise <h5> est utilis�e
*/
	if(preg_match('`&lt;h5`i',$html)){
	$h5='yena';
	}
/*
Est-ce que la balise <h6> est utilis�e
*/
	if(preg_match('`&lt;h6`i',$html)){
	$h6='yena';
	}
/*
Est-ce que la balise <h6> est utilis�e alors qu'il manque une balise pr�c�dente
*/
	if($h6=='yena' && ($h5!='yena' or $h4!='yena' or $h3!='yena' or $h2!='yena' or $h1!='yena')){
	$hdes='vrai';
	$mess="Vous avez un &lt;h6&gt; alors qu'il manque un ent�te plus petit (&lt;h1&gt; � &lt;/h5&gt;";
	}
/*
Est-ce que la balise <h5> est utilis�e alors qu'il manque une balise pr�c�dente
*/
	elseif($h5=='yena' && ($h4!='yena' or $h3!='yena' or $h2!='yena' or $h1!='yena')){
	$hdes='vrai';
	$mess="Vous avez un &lt;h5&gt; alors qu'il manque un ent�te plus petit (&lt;h1&gt; � &lt;/h4&gt;";
	}
/*
Est-ce que la balise <h4> est utilis�e alors qu'il manque une balise pr�c�dente
*/
	elseif($h4=='yena' && ($h3!='yena' or $h2!='yena' or $h1!='yena')){
	$hdes='vrai';
	$mess="Vous avez un &lt;h4&gt; alors qu'il manque un ent�te plus petit (&lt;h1&gt; � &lt;/h3&gt;";
	}
/*
Est-ce que la balise <h3> est utilis�e aors qu'il manque une balise pr�c�dente
*/
	elseif($h3=='yena' && ($h2!='yena' or $h1!='yena')){
	$hdes='vrai';
	$mess="Vous avez un &lt;h3&gt; alors qu'il manque un ent�te plus petit (&lt;h1&gt; � &lt;/h2&gt;";
	}
/*
Est-ce que la balise <h2> est utilis�e aors qu'il manque une balise pr�c�dente
*/
	elseif($h2=='yena' && $h1!='yena'){
	$hdes='vrai';
	$mess="Vous avez un &lt;h2&gt; alors qu'il n'y a pas de &lt;h1&gt;";
	}
/*
Si il n'y a pas d'erreur d�tect�e dans les ent�tes, on n'affiche pas de message d'alerte
*/
	else{
	$hdes='faux';
	}
/*
Est-ce que le doctype est d�clar�
*/
	if(!preg_match('`&lt;!DOCTYPE`i',$html)){
	$doctype='yenapa';
	}
/*
Est-ce qu'il y a un tableau alors que les <th> ne sont pr�sents
*/
	if(preg_match('`&lt;table`i',$html) && !preg_match('`&lt;th`i',$html)){
	$tableth='yenapa';
	}
/*
Est-ce que le jeu de caract�res est d�clar�
*/
	if(!preg_match('`charset=`i',$html)){
	$charset='yenapa';
	}
/*
Est-ce que le titre de la page est d�clar�
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
D�tection des balises noframe et pr�sence d'un lien vers le menu ou un plan du site
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
Est-ce que les <label> sont pr�sent l� o� il faut
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
D�terminer le nb de textarea pr�sent
*/
	preg_match_all('`&lt;textarea`i',$html,$output);
	$nbtextarea=count($output[0]); 
/*
Est-ce que des tabindex sont d�clar�s
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
Compter le nb de <table> pr�sent sur la page
*/
	$motif2="`&lt;table`i";
	preg_match_all($motif2,$html,$out2);
	$nbtable=count($out2[0]);
if($nbtable=='1'){
$messageTable="le tableau pr�sent";
}
else{
$messageTable="les $nbtable tableaux pr�sents";
}
/*
V�rifier le doublement des liens d'une image map
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
V�rifier que tous les liens ayant le m�me nom pointent au m�me endroit

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
Pr�paration du fichier pour l'analyse ligne par ligne
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
			

			
			
/*R�gles de priorit� 1*/
		if($_GET['niveau']=='niveau1' or $_GET['niveau']=='niveau2' or $_GET['niveau']=='niveau3' or $_GET['niveau']==''){
/*R�gles de priorit� 1*/

/*
On v�rifie que la ligne contienne une balise frame ou frameset 
et qu'elle ne contient pas d'attribut title ou seulement un 
attribut title vide.
*/
			if(preg_match('`&lt;frame`i',$html[$i]) && (!eregi('title',$html[$i]) or preg_match('`title\s?=\s?&quot;&quot;`',$html[$i]) or preg_match("`title\s?=\s?''`",$html[$i]))){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/12_1_frame_nom\" tabindex=\"$tab\" title=\"lien vers la page sur les frames\">Priorit� 1 - Vous devez nommer vos frames et votre frameset.</a></div><p>";
			$erreur1++;
			$tab++;
			}
			else{}

/*
On v�rifie que la ligne contient une balise </frameset> et on affiche un message 
d'erreur si il n'y a pas de balises <noframes></noframes>
*/
			if($framesetSansNoframes=="yena" && preg_match('`&lt;/frameset`i',$html[$i])){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/12_2_frameset_sans_noframes\" tabindex=\"$tab\" title=\"lien vers la page sur les frames\">Priorit� 1 - Vous devez mettre en place les balises &lt;noframes&gt; et rendre votre site accessible � ceux qui ne peuvent pas afficher les frames.</a></div><p>";
			$erreur1++;
			$tab++;
			}
			else{}

/*
On v�rifie que la ligne contient une balise </embed> et on affiche un message 
d'erreur si il n'y a pas de balises <noembed></noembed>
*/
			if($embedSansnoembed=="yena" && preg_match('`&lt;/embed`i',$html[$i])){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/06_3_embed_sans_noembed\" tabindex=\"$tab\" title=\"lien vers la page sur le multimedia\">Priorit� 1 - Vous devez mettre en place les balises &lt;noembed&gt; et offrir une alternative pour ceux qui utilisent un navigateur ne supportant pas l'inclusion d'objets multimedia.</a></div><p>";
			$erreur1++;
			$tab++;
			}
			else{}

/*
On v�rifie que la ligne contient une balise </noframe> et on affiche un message 
d'erreur si il n'y a pas de lien d'indiqu� entre <noframes> et </noframes>
*/
			if($noframesSansLien=="yena" && preg_match('`&lt;/noframes`i',$html[$i])){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/12_2_noframes_sans_lien\" tabindex=\"$tab\" title=\"lien vers la page sur les frames\">Priorit� 1 - utilisez les balises &lt;noframes&gt; pour mettre en place un lien vers une page contenant les liens vers toutes les pages de votre site.</a></div><p>";
			$erreur1++;
			$tab++;
			}
			else{}



/*
On v�rifie que la ligne contienne une balise appelant une zone d'image map 
et qu'il n'y ai pas d'attribut alt ou que celui-ci soit vide.
*/
			if(preg_match('`shape\s?=`i',$html[$i]) && preg_match('`area`i',$html[$i]) && (!preg_match('`alt`i',$html[$i]) or preg_match("`alt\s?=\s?(''|&quot;&quot;)`i",$html[$i]))){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/01_1_map_alt\" tabindex=\"$tab\" title=\"lien vers la page sur les images map\">Priorit� 1 - Vous devez mettre un attribut alt avec toutes les zones de votre image r�active.</a></div><p>";
			$erreur1++;
			$tab++;
			}
			else{}
/*
On v�rifie que la ligne contienne une balise img 
et qu'il n'y ai pas d'attribut alt.
*/
			if(preg_match('`&lt;img`i',$html[$i]) && !preg_match('`alt`i',$html[$i])){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/01_1_image_alt\" tabindex=\"$tab\" title=\"lien vers la page sur les images\">Priorit� 1 - Vous devez mettre un attribut alt avec toutes vos images.</a></div><p>";
			$erreur1++;
			$tab++;
			}
			else{}
/*
On v�rifie que la ligne contienne une balise <table> 
et si il n'y a pas de <th> d�clar�, on affiche 1 fois 
le message d'alerte.
*/
			if($tableth=='yenapa' && $table=="3" && preg_match('`&lt;table(.*?)&gt;`i',$html[$i])){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Difficile ! </span><a href=\"wiki/wiki/05_1_tableau_th\" tabindex=\"$tab\" title=\"lien vers la page sur les tableaux\">Priorit� 1 - Identifiez les ent�tes &lt;th&gt; des tableaux de donn�es. V�rifiez que vous n'utilisez pas $messageTable sur votre page � des fins de pr�sentation.</a></div><p>";
			$erreur1++;
			$table++;
			$tab++;
			}
			else{}
/*
On v�rifie que la ligne contienne un lien 
et si celui-ci m�ne nulle part, on affiche une alerte.
*/
			if(preg_match("`&lt;a(.*?)href\s?=\s?(&quot;#&quot;|'#')(.*?)&gt;`i",$html[$i])){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/06_3_lien_javascript\" tabindex=\"$tab\" title=\"lien vers la page sur les liens\">Priorit� 1 - Ce type de lien ne fonctionne pas si le javascrit est d�sactiv�.</a></div><p>";
			$erreur1++;
			$tab++;
			$lien_alerte="invalide";
			}
			else{}
/*
On v�rifie si une image bmp est utilis�e.
*/
			if(preg_match('`&lt;img(.*)src=&quot;(.*).bmp&quot;(.*)&gt;`i',$html[$i])){
			$resultat.="</p><div class=\"rouge\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/11_1_image_bmp\" tabindex=\"$tab\" title=\"lien vers la page sur les images\">Priorit� 1 - Le format bmp n'est pas destin� au web, utilisez png, jpg ou gif.</a></div><p>";
			$avertissement1++;
			$tab++;
			}
			else{}
			
		if($_GET['avert']!='pasAvertissement'){
		
/*
On v�rifie que la ligne contienne une balise appelant un script
*/
			if(preg_match('`&lt;(object|script|embed|applet)`i',$html[$i])){
			$resultat.="</p><div class=\"orange\"><span class=\"facile\">Variable </span><a href=\"wiki/wiki/06_5_script_texte\" tabindex=\"$tab\" title=\"lien vers la page sur les script\">Priorit� 1 - Assurez-vous que la page est consultable sans ce script et/ou que le contenu soit doubl� d'une version accessible sans l'utilisation de langage de script.</a></div><p>";
			$avertissement1++;
			$tab++;
			}
			else{}
/*
On v�rifie que la ligne contienne une balise appelant 
une image map c�t� serveur.
*/
			if(preg_match('`ismap`i',$html[$i])){
			$resultat.="</p><div class=\"orange\"><a href=\"wiki/wiki/09_1_map_serveur\" tabindex=\"$tab\" title=\"lien vers la page sur les images map\">Priorit� 1 - Vous devriez utiliser de pr�f�rence une image map c�t� client.</a></div><p>";
			$resultat.="</p><div class=\"orange\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/01_2_map_lien\" tabindex=\"$tab\" title=\"lien vers la page sur les images map\">Priorit� 1 - Assurez-vous que l'ensemble des liens soient repris sous forme de texte.</a></div><p>";
			$avertissement1=$avertissement1+2;
			$tab++;
			}
			else{}
/*
On v�rifie que la ligne contienne un lien 
et que celui-ci fasse appel � du javascript.
*/
			if($lien_alerte!="invalide" && preg_match('`onclick`i',$html[$i]) && preg_match('`&lt;a`i',$html[$i]) && preg_match('`#`i',$html[$i])){
			$resultat.="</p><div class=\"orange\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/06_3_lien_javascript\" tabindex=\"$tab\" title=\"lien vers la page sur les liens\">Priorit� 1 - Assurez-vous que ce lien fonctionne sans le javascript.</a></div><p>";
			$avertissement1++;
			$tab++;
			}
			else{}
/*
On v�rifie que la ligne contienne une balise <form> 
et si on a d�tect� pr�c�demment l'usage de javascript 
sur un formulaire de la page, on affiche une alerte.
*/
			if(preg_match('`&lt;form marqueur=&quot;invalide&quot;`i',$html[$i])){
			$resultat.="</p><div class=\"orange\"><span class=\"facile\">Variable </span><a href=\"wiki/wiki/06_3_formulaire_javascript\" tabindex=\"$tab\" title=\"lien vers la page sur les formulaire\">Priorit� 1 - Assurez-vous que ce formulaire fonctionne si le javascrit est d�sactiv�.</a></div><p>";
			$avertissement1++;
			$tab++;
			}
			else{}
			
/*
On v�rifie que la ligne contienne une balise <select> 
et si on a d�tect� pr�c�demment l'usage de javascript 
sur un formulaire de la page, on affiche une alerte.
*/
			if(preg_match('`&lt;select marqueur=&quot;invalide&quot;`i',$html[$i])){
			$resultat.="</p><div class=\"orange\"><span class=\"facile\">Variable </span><a href=\"wiki/wiki/06_3_formulaire_javascript\" tabindex=\"$tab\" title=\"lien vers la page sur les formulaire\">Priorit� 1 - Assurez-vous que ce formulaire fonctionne si le javascrit est d�sactiv�.</a></div><p>";
			$avertissement1++;
			$tab++;
			}
			else{}
			
/*
On v�rifie que la ligne contienne une balise <input> 
et si on a d�tect� pr�c�demment l'usage de javascript 
sur un formulaire de la page, on affiche une alerte.
*/
			if(preg_match('`&lt;input marqueur=&quot;invalide&quot;`i',$html[$i])){
			$resultat.="</p><div class=\"orange\"><span class=\"facile\">Variable </span><a href=\"wiki/wiki/06_3_formulaire_javascript\" tabindex=\"$tab\" title=\"lien vers la page sur les formulaire\">Priorit� 1 - Assurez-vous que ce formulaire fonctionne si le javascrit est d�sactiv�.</a></div><p>";
			$avertissement1++;
			$tab++;
			}
			else{}
			
			
/*
On v�rifie que la ligne contienne une balise <img> 
et un attribut alt vide.
*/
			if(preg_match("`&lt;img(.*)alt\s?=\s?(''|&quot;&quot;)`i",$html[$i])){
			$resultat.="</p><div class=\"orange\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/01_1_image_alt_vide\" tabindex=\"$tab\" title=\"lien vers la page sur les images\">Priorit� 1 - Vous avez mis un attribut alt vide, est-ce volontaire ?.</a></div><p>";
			$avertissement1++;
			$tab++;
			}
			else{}
}
		}
		else{
		}
		
		
		
		
		
/*R�gles de priorit� 2*/
		if($_GET['niveau']=='niveau2' or $_GET['niveau']=='niveau3' or $_GET['niveau']==''){
/*
Si il n'y a pas d'appel � une feuille de style externe, 
on affiche une alerte apres </head>.
*/
			if($css=='yenapa' && preg_match('`&lt;/head`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/03_3_css\" tabindex=\"$tab\" title=\"lien vers la page sur les css\">Priorit� 2 - Vous devriez utiliser une feuille de style externe.</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}
/*
On v�rifie que la ligne contienne une balise <frame> ou <frameset> 
et si il n'y a pas de longdesc, on affiche une alerte.
*/
			if(preg_match('`&lt;frame`i',$html[$i]) && !preg_match('`longdesc`',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Tr�s facile ! </span><a href=\"glossaire./12_1_frame_nom\" tabindex=\"$tab\" title=\"lien vers la page sur les descriptions de frame\">Priorit� 2 - D�crivez correctement vos frames et compl�tez la partie &lt;noframes&gt;... &lt;/noframes&gt;.</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}
/*
Si il n'y a pas de doctype d�clar�, on affiche une alerte 
apr�s le </head>.
*/
			if($doctype=='yenapa' && preg_match('`&lt;/head`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/03_2_doctype\" tabindex=\"$tab\" title=\"lien vers la page sur les doctypes\">Priorit� 2 - Vous devez d�clarer un doctype et valider votre document.</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}
/*
Si il n'y a pas de d�finition de caract�res d�clar�e, on affiche une alerte 
apr�s le </head>.
*/
			if($charset=='yenapa' && preg_match('`&lt;/head`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/03_2_charset\" tabindex=\"$tab\" title=\"lien vers la page sur les doctypes\">Priorit� 2 - Vous devez d�clarer l'encodage de votre page.</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}

/*
Si il n'y a pas de <link rel="stylesheet" type="text/css" media="screen" href="../validateur.css" />
<title></title> d�clar�, on affiche une alerte 
apr�s le </head>.
*/
			if($title=='yenapa' && preg_match('`&lt;/head`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/03_5_title\" tabindex=\"$tab\" title=\"lien vers la page sur le meta-donn�es\">Priorit� 2 - Vous devriez indiquer le titre de votre document.</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}
/*
On v�rifie que la ligne contienne une balise forcant 
le rafraich�ssement de la page.
*/
			if(preg_match("`http-equiv\s?=\s?(&quot;refresh&quot;|'refresh')`i",$html[$i]) && !preg_match('`url`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Variable </span><a href=\"wiki/wiki/07_4_refresh\" tabindex=\"$tab\" title=\"lien vers la page sur les rafraichissements\">Priorit� 2 - Ne forcez pas le rafra�chissement d'une page.</a></div><p>";
			$erreur2=$erreur2+2;
			$tab++;
			//$refresh='yena';
			}
			else{}
/*
On v�rifie que la ligne contienne une balise forcant 
la redirection de la page.
*/
			if(preg_match("`http-equiv\s?=\s?(&quot;refresh&quot;|'refresh')`i",$html[$i]) && preg_match('`url`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Variable </span><a href=\"wiki/wiki/07_5_redirection\" tabindex=\"$tab\" title=\"lien vers la page sur les redirections\">Priorit� 2 - Pr�f�rez les redirections \"c�t� serveur\".</a></div><p>";
			$erreur2=$erreur2+2;
			$tab++;
			}			
			else{}
/*
On v�rifie que la ligne contienne une balise d�pr�ci�e (font) : 
*/
			if(preg_match("`&lt;font\s?=`i",$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/11_2_deprecie\" tabindex=\"$tab\" title=\"lien vers la page sur les technologies d�pr�ci�es\">Priorit� 2 - Remplacez la balise font d�pr�ci�e par son �quivalent en css.</a></div><p>";
			$erreur2++;
			$tab++;
			}			
			else{}
/*
On v�rifie que la ligne contienne une balise d�pr�ci�e (bgcolor) : 
*/
			if(preg_match("`&lt;(.*?)bgcolor\s?=(.*?)&gt;`i",$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/11_2_deprecie\" tabindex=\"$tab\" title=\"lien vers la page sur les technologies d�pr�ci�es\">Priorit� 2 - Remplacez l'attribut bgcolor d�pr�ci� par son �quivalent en css.</a></div><p>";
			$erreur2++;
			$tab++;
			}			
			else{}
/*
On v�rifie que la ligne contienne une balise d�pr�ci�e (border) : 
*/
			if(preg_match("`&lt;(.*?)border\s?=(.*?)&gt;`i",$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/11_2_deprecie\" tabindex=\"$tab\" title=\"lien vers la page sur les technologies d�pr�ci�es\">Priorit� 2 - Remplacez l'attribut border d�pr�ci� par son �quivalent en css.</a></div><p>";
			$erreur2++;
			$tab++;
			}			
			else{}
/*
On v�rifie que la ligne contienne une balise d�pr�ci�e (center) : 
*/
			if(preg_match("`&lt;(.*?)[^:]/s?center(.*?)&gt;`i",$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/11_2_deprecie\" tabindex=\"$tab\" title=\"lien vers la page sur les technologies d�pr�ci�es\">Priorit� 2 - Remplacez la balise center d�pr�ci�e par son �quivalent en css.</a></div><p>";
			$erreur2++;
			$tab++;
			}			
			else{}
/*
Si aucune balise <h1> � <h6> n'est d�clar�e, 
on affiche une alerte juste apr�s <body>.
*/
			if($h=='yenapa' && preg_match('`&lt;body`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">variable ! </span><a href=\"wiki/wiki/03_5_h\" tabindex=\"$tab\" title=\"lien vers la page sur la s�mantique\">Priorit� 2 - Structurez votre document � l'aide des balises &lt;h1&gt; � &lt;h6&gt;.</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}

/*
On v�rifie si il n'y a pas un lien pas assez explicite.
*/
			if(preg_match('`&lt;/a marqueur=&quot;invalide&quot;&gt;`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/13_1_lien_clair\" tabindex=\"$tab\" title=\"lien vers la page sur les liens explicites\">Priorit� 2 - Vos liens doivent �tre plus explicites</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}
/*
On v�rifie si les lignes devant recevoir un <label> sont conformes 
en �vitant les alertes sur la ligne de submit.
*/
			if($label=='yenapa' && preg_match('`&lt;(input|select|textarea)`i',$html[$i]) && !preg_match('`submit`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/10_2_label\" tabindex=\"$tab\" title=\"lien vers la page sur les labels\">Priorit� 2 - Vous devriez mettre en place un &lt;label&gt;...&lt;/label&gt;</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}
/*
On v�rifie que la balise label contienne bien l'attribut for="".
*/
			if(preg_match('`&lt;label`i',$html[$i]) && !preg_match('`for`i',$html[$i])){
			$resultat.="</p><div class=\"rouge2\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/12_4_labelfor\" tabindex=\"$tab\" title=\"lien vers la page sur les labels et les for\">Priorit� 2 - D�finissez la cible de votre &lt;label&gt; � l'aide de for=\"id_cible\"</a></div><p>";
			$erreur2++;
			$tab++;
			}
			else{}
			
			
			
		if($_GET['avert']!='pasAvertissement'){
/*
On v�rifie que les tailles sont bien d�finies en relatif.
*/
			if(preg_match('`&lt;(table|tr|td|div|span)(.*?)px(.*?)&gt;`i',$html[$i])){
			$resultat.="</p><div class=\"orange2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/03_4_taille_relative\" tabindex=\"$tab\" title=\"lien vers la page sur les css\">Priorit� 2 - Utilisez plut�t des tailles relatives</a></div><p>";
			$avertissement2++;
			$tab++;
			}
			else{}
/*
On v�rifie si la ligne contient un appel 
pour ouvrir une nouvelle fen�tre.
*/
			if(preg_match("`(window.open|window.resize|target\s?=\s?&quot;_blank&quot;|target\s?=\s?'_blank')`i",$html[$i])){
			$resultat.="</p><div class=\"orange2\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/10_1_new_win\" tabindex=\"$tab\" title=\"lien vers la page sur les nouvelles fen�tre\">Priorit� 2 - N'ouvrez pas de nouvelle fen�tre et ne modifiez pas celle en cours sans pr�venir clairement le visiteur.</a></div><p>";
			$avertissement2++;
			$tab++;
			}
			else{}
/*
On v�rifie si la ligne contient un gestionnaire d'�v�nement 
ne fonctionnant qu'avec une souris.
*/
			if(preg_match("`(ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout)`i",$html[$i])){
			$resultat.="</p><div class=\"orange2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/06_4_gestionnaire\" tabindex=\"$tab\" title=\"lien vers la page sur les gestionnaire d'evenements\">Priorit� 2 - Utilisez de pr�f�rence des gestionnaires d'�v�nements qui fonctionnent avec autre chose qu'une souris.</a></div><p>";
			$avertissement2++;
			$tab++;
			}
			else{}
/*
On v�rifie si la ligne contient une balise de citation 
<q>, <cite> ou <blocquote> et on demande la confirmation 
du bon usage de celle-ci.
*/
			if($citation=='2' && preg_match("`(&lt;q&gt;|&lt;cite&gt;|&lt;blockquote&gt;)`i",$html[$i])){
			$resultat.="</p><div class=\"orange2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/03_7_citation\" tabindex=\"$tab\" title=\"lien vers la page sur les gestionnaire d'evenements\">Priorit� 2 - V�rifiez que vous utilisez bien les balises &lt;q&gt; &lt;cite&gt; &lt;blockquote&gt;  pour introduire une citation.</a></div><p>";
			$avertissement2++;
			$tab++;
			$citation++;
			}
			else{}
/*
On v�rifie si la ligne contient une balise ou une extension 
d�montrant l'usage de parties mobiles sur la page et on affiche 
une alerte.
*/
			if(preg_match("`(\.swf|\.mng|&lt;blink|&lt;marquee)`i",$html[$i])){
			$resultat.="</p><div class=\"orange2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/07_2_image_clignote\" tabindex=\"$tab\" title=\"lien vers la page sur les pages qui clignotent\">Priorit� 2 - l'usage d'images anim�es, de clignotement ou de d�filement est d�conseill�.</a></div><p>";
			$avertissement2++;
			$tab++;
			}
			else{}
			
/*
On v�rifie si la ligne contient une d�finition de style 
qui pourrait �tre mis dans une feuille externe.
*/
			if($stycss==0 && preg_match("`style\s?=\s?('|&quot;)`i",$html[$i])){
			$resultat.="</p><div class=\"orange2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/03_3_css_locale\" tabindex=\"$tab\" title=\"lien vers la page sur les css\">Priorit� 2 - V�rifiez si il n'est pas plus simple de regrouper vos d�clarations de style dans une feuille de style externe. En d�clarant vos styles CSS au sein de chaque page, vous perdez un des avantages des CSS, � savoir la facilit� de maintenance.</a></div><p>";
			$avertissement2++;
			$tab++;
			$stycss++;
			}
			else{}
/*
On v�rifie si la ligne contient une d�finition de style 
qui pourrait �tre mis dans une feuille externe.
*/
			if($css=='yenapa' && preg_match("`class\s?=`i",$html[$i])){
			$resultat.="</p><div class=\"orange2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/03_3_css_locale\" tabindex=\"$tab\" title=\"lien vers la page sur les css\">Priorit� 2 - En d�clarant vos styles CSS au sein de chaque page, vous perdez un des avantages des CSS, � savoir la facilit� de maintenance.</a></div><p>";
			$avertissement2++;
			$tab++;
			}
			else{}
/*
Si on a d�tect� une erreur dans l'imbrication des ent�te <h1 � 6>,
on affiche une alerte juste apr�s <body>
*/
			if($hdes=='vrai' && preg_match('`&lt;body`i',$html[$i])){
			$resultat.="</p><div class=\"orange2\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/03_5_hdes\" tabindex=\"$tab\" title=\"lien vers la page sur la s�mantique\">Priorit� 2 - $mess</a></div><p>";
			$avertissement2++;
			$tab++;
			}
			else{}
		}
		}
		else{
		}
		
		
		
/*R�gles de priorit� 3*/
		if($_GET['niveau']=='niveau3' or $_GET['niveau']==''){
		/*R�gles de priorit� 3*/
		
/*
Si le document ne contient pas d'accesskey, on met 
une alerte juste apr�s <body>
*/
	if($accesskey=='yenapa' && preg_match('`&lt;body`i',$html[$i])){
	$resultat.="</p><div class=\"rouge3\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/09_5_accesskey\" tabindex=\"$tab\" title=\"lien vers la page sur les css\">Priorit� 3 - Vous devriez mettre en place des raccourcis clavier (accesskey).</a></div><p>";
	$erreur3++;
	$tab++;
	}
	else{}

/*
On v�rifie si la langue du document a bien �t� 
d�clar�e sinon on met une alerte.
*/
	if($lang=='yenapa' && preg_match('`&lt;/head`i',$html[$i])){
	$resultat.="</p><div class=\"rouge3\"><span class=\"facile\">Tr�s facile ! </span><a href=\"wiki/wiki/04_3_lang\" tabindex=\"$tab\" title=\"lien vers la page sur la d�claration de la langue\">Priorit� 3 - Vous devriez indiquer la langue principale du document.</a></div><p>";
	$erreur3++;
	$tab++;
	}			
	else{}

/*
On v�rifie si la ligne contient une image r�active 
et on affiche une alerte pour les liens textes en doublure.
*/
			if(preg_match('`hrefpasbon`i',$html[$i])){
			$resultat.="</p><div class=\"rouge3\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/01_5_map_double\" tabindex=\"$tab\" title=\"lien vers la page sur les images map\">Priorit� 3 - V�rifiez que vous avez bien repris sous une forme classique l'ensemble des liens de votre image r�active.</a></div><p>";
			$erreur3++;
			$tab++;
			}
			else{}



		if($_GET['avert']!='pasAvertissement'){

/*
Si la page ne contient aucun tabindex, on affiche une alerte 
juste apr�s <body>.
*/
			if($tabindex=='yenapa' && preg_match('`&lt;body`i',$html[$i])){
			$resultat.="</p><div class=\"orange3\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/09_4_tabindex\" tabindex=\"$tab\" title=\"lien vers la page sur les tabindex\">Priorit� 3 - V�rifiez si l'ordre de navigation � l'aide de la touche tab est logique, en cas de r�ponse n�gative, mettez en place des tabindex.</a></div><p>";
			$avertissement3++;
			$tab++;
			}
			else{}
/*
On v�rifie si au moins un tabindex est d�clar� et si oui 
si toutes les balises le requ�rant en ont bien un de d�clar�.
*/
			if($tabindex=='yena' && $tabindexPasPartout != "houi" && preg_match('`(&lt;a |&lt;input|&lt;area|&lt;button|&lt;object|&lt;select|&lt;textarea)`i',$html[$i]) && !preg_match('`tabindex`i',$html[$i]) && !preg_match('`(hidden|acronym|link|&lt;/|address)`i',$html[$i])){
			$resultat.="</p><div class=\"orange3\"><span class=\"facile\">Variable ! </span><a href=\"wiki/wiki/09_4_tabindex\" tabindex=\"$tab\" title=\"lien vers la page sur les tabindex\">Priorit� 3 - Vous avez mis en place certains tabindex mais pas de partout o� il en faudrait, est-ce d�lib�r� ?</a></div><p>";
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
	echo "<strong>Bravo ! </strong>il y n'y a pas d'erreurs de d�tect�es sur votre page, prenez tout de 
	m�me le temps de v�rifier les �ventuels avertissements et de valider les points non v�rifiables 
	automatiquement.";
	echo "</p>";	
	}

	echo "<p>";
	echo "<strong>Priorit� 1 :</strong> ";
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
	echo "<strong>Priorit� 2 :</strong> ";
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
	echo "<strong>Priorit� 3 :</strong> ";
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
?>
<p>
En plus des points signal�s directement dans le code, veuillez prendre garde � respecter les 
<a href="valider_site.htm">points non v�rifiables automatiquement</a>.
</p>
<h2>Choisir le niveau de validation</h2>
<form action="index.php" id="niveau">
<p>
<label for="niveau1">Niveau 1 : </label><input type="radio" name="niveau" id="niveau1" value="niveau1" /><br /> 
<label for="niveau2">Niveau 2 : </label><input type="radio" name="niveau" id="niveau2" value="niveau2" /><br />
<label for="niveau3">Niveau 3 : </label><input type="radio" name="niveau" id="niveau3" value="niveau3" /><br />
<label for="avert">Que les erreurs : </label><input type="checkbox" name="avert" id="avert" value="pasAvertissement" /><br />
<input type="hidden" name="urlAVerif" id="urlAVerif" value="<?php echo $_GET['urlAVerif']?>" />
<input type="submit" name="revalider" value="Revalider votre document" />
</p>
</form>

<?php
/*
On affiche le code avec les messages d'erreurs.
*/
	echo "<h2>Voici le code source de la page :</h2>";
	$resultat=eregi_replace("marqueur=&quot;invalide&quot;","",$resultat);
	$resultat=eregi_replace("hrefpasbon","href",$resultat);
	echo "<p class=\"gauche\">$resultat</p>";


/*
On prend l'heure de fin de traitement et on affiche 
la dur�e de pr�paration de la page.
*/
$time_end = getmicrotime();
$time = $time_end - $time_start;
$time=number_format($time,3,","," ");
//echo "<p>Page g�n�r�e en $time secondes</p>";
	}
?>
</body>
</html>
<?php
}
?>
