<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Utilisation de Spip jQuery Cycle "sjcycle"</title>
</head>
<?php 
$text ='';
if (isset($_GET['art']) && is_numeric($_GET['art'])) {
	$text =', ici le num&eacute;ro "'.$_GET['art'].'"';
}
?>
<body style="padding:0px;margin:0px;background:#fff;font-size:0.9em;font-family: Arial, Helvetica, Geneva, SunSans-Regular, sans-serif;">
<div style="padding:0px;margin:10px auto;width:570px;border:5px solid #ddd;background:#eee;">
<h3 style="padding:10px 2px;margin:0px;background:#fff;font-size:0.9em;text-align:center;border-bottom:5px solid #ddd;">1. D&eacute;cryptage de la syntaxe et param&egrave;tres du raccourci typographique "sjcycle"</h3>
<div style="padding:0px;font-size:0.8em;margin:10px;">
	<p style="background:#eee;color:#666;padding:10px;font-weight:700">Le raccourci typographique "sjcycle" permet d'ins&eacute;rer facilement un ou plusieurs diaporamas au sein d'un article : il vous suffit de le recopier &agrave; l’int&eacute;rieur de la case « Texte » de l'article, l&agrave; vous d&eacute;sirez situer le diaporama.
	<br /><br />
	Ce raccourci comporte plusieurs param&egrave;tres s&eacute;par&eacute;s par le caract&egrave;re "|".</p>
					<ol style="font-size:11px">
						<li style="padding:10px 5px;margin:10px 0px;background:white;">
							<h4 style="margin:0px;color:#666">&lt;sjcycle<span style="color:red">N</span>&gt;</h4>
							<strong>Premier param&egrave;tre, "<span style="color:#666">N</span>",  <span style="color:red">obligatoire</span> :</strong>
							<br />Correspond au num&eacute;ro de l'article en cours<?php echo $text; ?>.
							<br />Il permet de cibler les images li&eacute;es &agrave; un unique article.
							<p style="background:#eee;color:#666;padding:10px;font-weight:700">
								<img src="./images/star.gif" align="absmiddle" alt="Astuce">Astuce : Vous pouvez ainsi ins&eacute;rer un diaporama avec les images d'un article dans un autre article.
							</p>
						</li>
						<li style="padding:10px 5px;margin:10px 0px;background:white;">
							<h4 style="margin:0px;color:#666">&lt;sjcycleN<span style="color:red">|ALIGN</span>&gt;</h4>
							<strong>Second param&egrave;tre, "<span style="color:#666">|ALIGN</span>", optionnel :</strong>
							<br />Permet de sp&eacute;cifier l'alignement du diaporama au sein du texte.
							<br />S'il n'est pas pr&eacute;cis&eacute;, l'alignement  est "center".
							<br /><strong>Les options de l'alignement :</strong>
							<ul style="font-size:11px">
								<li>"<em>left</em>" : &agrave; gauche du texte;</li>
								<li>"<em>center</em>" : centrer dans le texte;</li>
								<li>"<em>right</em>" : &agrave; droite du texte.</li>
							</ul>
						</li>
						<li style="padding:10px 5px;margin:10px 0px;background:white;">
							<h4  style="margin:0px;color:#666">&lt;sjcycleN|ALIGN|<span style="color:red">docs=n1,n2</span>&gt;</h4>
							<strong>Troisi&egrave;me param&egrave;tre, "<span style="color:#666">|docs=n1,n2,[...],nx</span>", optionnel :</strong>
							<br />Permet de sp&eacute;cifier la liste d'images &agrave; afficher dans le diaporama.
							<br />Ainsi, on peut afficher plusieurs diaporamas dans au sein d'un même texte, isoler des images.
							<br />S'il n'est pas pr&eacute;cis&eacute;, l'ensemble des images sont s&eacute;lectionn&eacute;es par d&eacute;faut.
							<br />
							<br /><strong>Les arguments de s&eacute;lection "<em>n1,n2,[...],nx</em>" du param&egrave;tre "<em>|docs=</em>" :</strong><br />
							Ils se composent d'une suite de num&eacute;ros d'images s&eacute;par&eacute;es par des virgules sans espace.
							<br />Sous chaque image est indiqu&eacute; un raccourci typographique du type "<em>&lt;imgXX|left&gt;</em>", "<em>&lt;docXX|left&gt;</em>" ou "<em>&lt;docXX&gt;</em>",
							 où "<em>XX</em>" d&eacute;signe le num&eacute;ro de l'image correspondante.<br />
							Ainsi le param&egrave;tre "<em>|docs=1,3</em>", s&eacute;lectionne les images num&eacute;ro 1 et 3 de l'article courant.
							<p style="background:#eee;color:#666;padding:10px;font-weight:700">
								<img src="./images/star.gif" align="absmiddle" alt="Astuce">Astuce : Vous pouvez utiliser le raccourci en ne passant qu'un seul num&eacute;ro d'image au param&egrave;tre "<em>|docs=</em>" afin de b&eacute;n&eacute;ficier de l'effet "FancyBox" : un clic sur l'image redimensionnée du diaporama permet d'afficher l'image originale en superposition.
							</p>
						</li>
						<li style="padding:10px 5px;margin:10px 0px;background:white;">
							<h4  style="margin:0px;color:#666">&lt;sjcycleN|ALIGN|<span style="color:red">opt=p1:v1,p2:v2</span>&gt;</h4>
							<strong>Quatrième param&egrave;tre, "<span style="color:#666">|opt=p1:v1,p2:v2,[...],px:vx</span>", optionnel :</strong>
						  <br />
							Permet d'utiliser les options du script jcycle.
							La liste complète est définie sur le site du plugin jquery jcycle <a href="http://malsup.com/jquery/cycle/options.html" target="_blank">par ici</a><br />
							Ces valeurs écrasent 
							celles définies dans la page de configuration du plugin.<br />
							Les options possibles correspondent à celles de la version utilisée dans ce plugin spip et non à celles proposées sur la page citée ci-dessus. Actuellement, c'est la version 2.88 (08-JUN-2010) qui est utilisée
							<br />
							<strong>Exemple :</strong><br />
						   &lt;sjcycle16|center|opt=autostop:1,autostopCount:5&gt; : diaporama sur toutes les images de l'article 16 mais qui s'arrêtera à la cinquième image<br />
						   &lt;sjcycle16|center|opt=fx:'fade'&gt; : diaporama sur toutes les images de l'article 16 avec un effet de transition &quot;fade&quot; qui remplace celui défini globalement pour le site entier<br />
						</li>
						<li style="padding:10px 5px;margin:10px 0px;background:white;">
							<h4  style="margin:0px;color:#666">&lt;sjcycleN|ALIGN|<span style="color:red">id_diapo=X</span>&gt;</h4>
							<strong>Cinquième param&egrave;tre, "<span style="color:#666">|id_diapo=X</span>", optionnel :</strong>
							<br />
							Permet d'afficher plusieurs diaporamas dans le même article, chacun devant avoir un id_diapo différent
							<br />
							<strong>Exemple :</strong><br />
						   &lt;sjcycle16|center|docs=101,102,103,104|id_diapo=1&gt;<br />
						   &lt;sjcycle16|center|docs=105,106,107,108,109|id_diapo=2|opt=fx:'shuffle',timeout:2000&gt;<br />						   
						   Ceci affiche 2 diaporamas dans le même article, le premier sur 4 images avec les paramètres par défaut du site et le second sur 5 images, avec des paramètres différents de ceux par défaut.<br />
						</li>
						<li style="padding:10px 5px;margin:10px 0px;background:white;">
							<h4  style="margin:0px;color:#666">&lt;sjcycleN|ALIGN|<span style="color:red">width=XXX</span>&gt; / &lt;sjcycleN|ALIGN|<span style="color:red">height=XXX</span>&gt;</h4>
							<strong>Sixième param&egrave;tre, &quot;<span style="color:#666">|width=XXX</span>&quot; et/ou &quot;<span style="color:#666">|height=XXX</span>&quot;, optionnel :</strong>
							<br />
							Ecrase la valeur width et/ou height
							 définie dans la page de configuration du plugin.<br />
						</li>
					</ol>
</div>
</div>
</body>
</html>
