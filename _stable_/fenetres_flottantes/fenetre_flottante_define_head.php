<?php
function FenFlo_affichage_final($flux)
{
	//on teste si les dimensions de la fenetre sont enregistrees dans les cookies
	$valeur_top_FenFlo = $_COOKIE['top_FenFlo'];
	$valeur_left_FenFlo = $_COOKIE['left_FenFlo'];
	$valeur_width_FenFlo = $_COOKIE['width_FenFlo'];
	$valeur_height_FenFlo = $_COOKIE['height_FenFlo'];
	
	//si pas de cookies, on applique les valeurs de configuration
	if ($valeur_top_FenFlo == "")
		$valeur_top_FenFlo = lire_config('FenFlo/posy_FenFlo','100px');
	if ($valeur_left_FenFlo == "")
		$valeur_left_FenFlo = lire_config('FenFlo/posx_FenFlo','100px');
	if ($valeur_width_FenFlo == "")
		$valeur_width_FenFlo = lire_config('FenFlo/largeur_FenFlo','400');
	if ($valeur_height_FenFlo == "")
		$valeur_height_FenFlo = lire_config('FenFlo/hauteur_FenFlo','300');

	
	$marge_haut= $valeur_height_FenFlo-lire_config('FenFlo/hauteurentete_FenFlo', '30');
	
	$marge2_haut= $marge_haut-15;
	$marge_larg= $valeur_width_FenFlo-25;


	return str_replace( "</head>", "<script type=\"text/javascript\">
	jQuery(document).ready(
	function()
	{
		$('#window').css({left:\"".$valeur_left_FenFlo."\", top:\"".$valeur_top_FenFlo."\", width:\"".$valeur_width_FenFlo."px\", height:\"".$valeur_height_FenFlo."px\"});
		$('#windowBottom').css({height:\"".$marge_haut."px\"});
		$('#windowBottomContent').css(\"height\",\"".$marge_haut."px\");
		$('#windowContent').css(\"height\",\"".$marge2_haut."px\");
		$('#windowContent').css(\"width\",\"".$marge_larg."px\");
	});
	</script></head>", $flux);
	
}

function FenFlo_insertion_in_head($flux)
{
	
	

	
	

	$afficher_close = "none";
	$afficher_redim = "none";
	
	$pos_bouton_close = "10";
	if (lire_config('FenFlo/close_FenFlo') == "on")
	{
		$afficher_close = "block";
		$pos_bouton_close = "25";
	}

	if (lire_config('FenFlo/redimensionne_FenFlo') == "on")
	{
		$afficher_redim = "block";
		$afficher_redim_instruction = "$('#windowResize').show();";
	}

	
	$script_open = "$('#window').show();";
	
	
	if(lire_config('FenFlo/zoom_fermeture_FenFlo') == "on")
	{
		$script_close = "$('#window').TransferTo(
					{
						to:'windowOpen',
						className:'transferer2', 
						duration: 400
					}
				).hide();";
	}
	else
	{
		$script_close = "$('#window').hide();";
	}

	$ajout_script="

		
	  


<link rel=\"stylesheet\" href=\""._DIR_PLUGIN_FENFLO."floating-windows.css\" type=\"text/css\" media=\"all\" />
<script type=\"text/javascript\" src=\""._DIR_PLUGIN_FENFLO."interface.js\"></script>
<script type=\"text/javascript\" src=\""._DIR_PLUGIN_FENFLO."jquery.cookie.js\"></script>
	

<script type=\"text/javascript\">

jQuery(document).ready(
		
	function()
	{
	
	$('".lire_config('FenFlo/attribut_FenFlo','contenu')."').show();
	$('".lire_config('FenFlo/attribut_FenFlo','contenu')."').parent().append(\"<div id='window'></div>\");
	$('#window').append(\"<div id='windowTop'></div>\");
	$('#windowTop').append(\"<div id='windowTopContent'></div>\");
	$('#windowTop').append(\"<img src='"._DIR_PLUGIN_FENFLO."images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_min.gif' class='format_png' id='windowMin' alt='minimiser la fenetre' />\");
	$('#windowTop').append(\"<img src='"._DIR_PLUGIN_FENFLO."images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_max.gif' class='format_png' id='windowMax' alt='agrandir la fenetre'/>\");
	$('#windowTop').append(\"<img src='"._DIR_PLUGIN_FENFLO."images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_close.gif' class='format_png' id='windowClose' alt='fermer la fenetre'/>\");
	$('#window').append(\"<div id='windowBottom'></div>\");
	$('#windowBottom').append(\"<div id='windowBottomContent'>&nbsp;</div>\");
	$('#window').append(\"<div id='windowContent'></div>\");
	$('#window').append(\"<img src='"._DIR_PLUGIN_FENFLO."images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_resize.gif' class='format_png'  id='windowResize' alt='resize' />\");
	
	
	$('#windowContent').css(\"border\",\"1px solid ".lire_config('FenFlo/couleurbordure_FenFlo','#6caf00')."\");
	$('#windowContent').css(\"top\",\"".lire_config('FenFlo/hauteurentete_FenFlo', '30')."px\");
	$('#windowTop').css(\"background-image\",\"url("._DIR_PLUGIN_FENFLO."images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_top_end.png)\");
	$('#windowTop').css(\"height\",\"".lire_config('FenFlo/hauteurentete_FenFlo', '30')."px\");
	$('#windowTopContent').css(\"background-image\",\"url("._DIR_PLUGIN_FENFLO."images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_top_start.png)\");
	$('#windowTopContent').css(\"height\",\"".lire_config('FenFlo/hauteurentete_FenFlo', '30')."px\");
	$('#windowTopContent').css(\"line-height\",\"".lire_config('FenFlo/hauteurentete_FenFlo', '30')."px\");
	$('#windowBottomContent').css(\"background-image\",\"url("._DIR_PLUGIN_FENFLO."images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_bottom_start.png)\");
	$('#windowBottom').css(\"background-image\",\"url("._DIR_PLUGIN_FENFLO."images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_bottom_end.png)\");
	$('#windowBottom').append('<a href=\"#\"  id=\"windowOpen\">&nbsp;</a>');
	
	$('#windowMax').css(\"right\",\"".$pos_bouton_close."px\");
	$('#windowMin').css(\"right\",\"".$pos_bouton_close."px\");
	
	$('#windowClose').css(\"display\",\"".$afficher_close."\");
	$('#windowResize').css(\"display\",\"".$afficher_redim."\");
	
	$('".lire_config('FenFlo/attribut_FenFlo','contenu')."').appendTo(\"#windowContent\");
	$('".lire_config('FenFlo/attribut_FenFlo','contenu')."').css(\"margin\",\"10px\");
	".$script_open."
	
		$('#windowClose').bind(
			'click',
			function()
			{
				".$script_close."
			}
		);
		$('#windowMin').bind(
			'click',
			function()
			{
				$('#windowContent').SlideToggleUp(300);
				$('#windowBottom, #windowBottomContent').animate({height: 10}, 300);
				$('#window').animate({height:".(lire_config('FenFlo/hauteurentete_FenFlo', '30')+13)."},300).get(0).isMinimized = true;
				$(this).hide();
				$('#windowResize').hide();
				$('#windowMax').show();
			}
		);
		
		$('#windowMax').bind(
			'click',
			function()
			{
				var windowSize = $.iUtil.getSize(document.getElementById('windowContent'));
				$('#windowContent').SlideToggleUp(300);
				$('#windowBottom, #windowBottomContent').animate({height: windowSize.hb + 13}, 300);
				$('#window').animate({height:windowSize.hb+".(lire_config('FenFlo/hauteurentete_FenFlo', '30')+13)."}, 300).get(0).isMinimized = false;
				$(this).hide();
				$('#windowMin').show();
				".$afficher_redim_instruction."
			}
		);
		$('#window').Resizable(
			{
				minWidth: 200,
				minHeight: 60,
				maxWidth: 1800,
				maxHeight: 1800,
				dragHandle: '#windowTop',
				handlers: {
					se: '#windowResize'
				},
				onResize : function(size, position) {
					$('#windowBottom, #windowBottomContent').css('height', size.height-".(lire_config('FenFlo/hauteurentete_FenFlo', '30')+3)." + 'px');
					var windowContentEl = $('#windowContent').css('width', size.width - 25 + 'px');
					if (!document.getElementById('window').isMinimized) {
						windowContentEl.css('height', size.height - ".(lire_config('FenFlo/hauteurentete_FenFlo', '30')+18)." + 'px');
					}
					$.cookie('width_FenFlo', size.width);
					$.cookie('height_FenFlo', size.height);
					
						
				},
				onDragStop : function() {
					var topFenFlo = $('#window').css('top');
					var leftFenFlo = $('#window').css('left');
					$.cookie('top_FenFlo', topFenFlo);
					$.cookie('left_FenFlo', leftFenFlo);
				}
			}
		);
		
		
	}
);
</script>
";
	
	return $flux.$ajout_script;
}

?>