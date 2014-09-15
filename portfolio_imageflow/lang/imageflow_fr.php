<?php

// lang/imageflow_fr.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

$GLOBALS['i18n_imageflow_fr'] = array(

	'portfolio_imageflow' => "Portfolio ImageFlow"
	
	, 'imageflow_aide' => "<strong>Portfolio ImageFlow</strong> est un plugin pour SPIP 
		compos&#233; de plusieurs scripts.<br /><br />
		Les divers scripts ont une licence d&#39;utilisation sp&#233;cifique.<br /><br />
		Avant d&#39;utiliser <strong>Portfolio ImageFlow</strong> sur votre site, consultez :<br />
		- <a href='http://contrib.spip.net/ImageFlow-pour-SPIP'>Spip-contrib</a><br />
		- <a href='http://194.95.111.244/~countzero/scripts/_myImageFlow/'>ImageFlow 0.9</a><br />
		- <a href='http://194.95.111.244/~countzero/myCMS/index.php?tag=ImageFlow'>DragonFly</a><br />
		- <a href='http://reflection.corephp.co.uk/v3.php'>Reflections</a>
		"
	, 'imageflow_aide_install' => "<p>Bienvenue dans le monde de <strong>Portfolio ImageFlow</strong>.</p>
		<p class='verdana2'>Pour valider les diff&eacute;rentes options de ImageFlow, rendez-vous 
		<a href='@url_config@'>sur la page de configuration</a>.</p>"
	, 'pas_acces_a_la_page' => "L&#39;acc&#232;s &#224; cette page ne vous est pas autoris&#233;e."
	
	, 'configuration_imageflow' => "Configuration du portfolio ImageFlow"
	
	, 'height' => "Hauteur du reflet"
	, 'height_label' => "La hauteur du reflet peut &#234;tre exprim&#233;e en pourcentage ou en pixels, 
		par exemple&#58; &#34;50%&#34;, ou &#34;16&#34;. 
		Par d&#233;faut&#58; &#34;@height@&#34;."
	, 'bgc' => "Couleur de fond"
	, 'bgc_label' => "Option uniquement disponible via le filtre <strong>image_avec_reflet</strong>.
		La couleur de fond de l&#39;image et de son reflet
		est &#224; exprimer en RVB, par exemple : &#34;#FF00FF&#34; ou &#34;#F0F&#34;
		ou 'none' pour un fond transparent. 
		Par d&#233;faut&#58; &#34;@bgc@&#34;."
	, 'fade_start' => "Opacit&#233; de d&#233;but du reflet"
	, 'fade_start_label' => "Valeur du d&#233;gr&#233; d&#39;opacit&#233; appliqu&#233;e en d&#233;but de reflet. 
		A exprimer en pourcentage ou en valeur, par exemple&#58; &#34;50%&#34; ou un chiffre entre 0 et 126.
		Par d&#233;faut&#58; &#34;@fade_start@&#34;."
	, 'fade_end' => "Opacit&#233; de fin du reflet"
	, 'fade_end_label' => "Valeur du d&#233;gr&#233; d&#39;opacit&#233; appliqu&#233;e en fin de reflet. 
		A exprimer en pourcentage, par exemple&#58; &#34;50%&#34;.
		Par d&#233;faut&#58; &#34;@fade_end@&#34;."
	, 'jpeg' => "Qualit&#233; JPEG"
	, 'jpeg_label' => "Niveau de compression JPEG. Un chiffre de 0 &#224; 100. 
		Par d&#233;faut&#58; &#34;@jpeg@&#34;."
	, 'tint' => "Teinte du reflet"
	, 'tint_label' => "Vous pouvez modifer la teinte du reflet en pr&#233;cisant ici un couleur RVB. 
		Par exemple &#34;#7F0000&#34;.
		Par d&#233;faut &#58; &#34;@tint@&#34;."
	, 'slider_select' => "S&#233;lection du bouton ascenseur (slider)"
	, 'preloader' => "Pr&#233;charger les images"
	, 'preloader_label' => "Pr&#233;charger les images du portfolio 
		lors de la consultation afin d&#39;acc&#233;lerer l&#39;affichage."
	, 'slideshow' => "Fondu enchain&#233;"
	, 'slideshow_label' => "Ajouter un effet de fondu enchain&#233; entre les images projet&#233;es."
	
	, 'active_link' => "Lien sur l&#39;image projet&#233;e (longdesc)"
	, 'active_link_label' => "Activer l&#39;URL sur l&#39;image projet&#233;e."
	, 'active_description' => "Si longdesc n&#39;est pas une URL mais une description, 
		afficher cette description lors du click sur l&#39;image projet&#233;e"
	, 'active_desc_effets' => "Description avec effet graphique"
	, 'active_alert' => "Description dans une boite alerte au lieu d&#39;une l&#233;gende"
	
	, 'reset' => "Reset"
	, 'reset_title' => "R&#233;tablir les r&#233;glages par d&#233;faut"
	
	, 'error_php_old' => "La version de PHP install&#233;e n&#39;est pas pleinement support&#233;e. 
		Vous devez utiliser PHP 4.3.2 ou sup&#233;rieur."
	, 'error_gd_missing' => "L&#39;extension GD pour PHP est manquante. D&#233;sol&#233;, impossible de continuer."
	, 'error_gd_not_png' => "La version de l&#39;extension GD pour PHP install&#233;e ne peut pas produire d&#39;image au format PNG."
	, 'error_gd_old' => "La librairie GD install&#233;e est trop ancienne. La version 2.0.1 ou sup&#233;rieure est n&#233;cessaire,
		et 2.0.28 est fortement recommand&#233;e."
);

?>