<?php

function FenFlo_insertion_in_head($flux)
{
	
//on teste si les dimensions de la fenêtre sont enregistrées dans les cookies
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

	

	$marge_haut= $valeur_height_FenFlo-30;
	
	$marge2_haut= $valeur_height_FenFlo-45;
	$marge_larg= $valeur_width_FenFlo-25;

	
	$afficher_close = "none";
	$pos_bouton_close = "10";
	if (lire_config('FenFlo/close_FenFlo') == "on")
	{
		$afficher_close = "block";
		$pos_bouton_close = "25";
	}


	if(lire_config('FenFlo/zoom_ouverture_FenFlo') == "on")
	{	$script_open = "if($('#window').css('display') == 'none') {
					$(this).TransferTo(
						{
							to:'window',
							className:'transferer2', 
							duration: 400,
							complete: function()
							{
								$('#window').show();
							}
						}
					);
				}
				this.blur();";

	}
	else
	{
		$script_open = "$('#window').show();";
	}
	
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

	
	  


<link rel=\"stylesheet\" href=\""._DIR_PLUGINS."fenetres_flottantes/floating-windows.css\" type=\"text/css\" media=\"all\" />
<script type=\"text/javascript\" src=\""._DIR_PLUGINS."fenetres_flottantes/interface.js\"></script>
<script type=\"text/javascript\" src=\""._DIR_PLUGINS."fenetres_flottantes/jquery.cookie.js\"></script>
	

<script type=\"text/javascript\">

$(document).ready(
		
	function()
	{
	
	$('".lire_config('FenFlo/attribut_FenFlo','contenu')."').parent().append(\"<div id='window'></div>\");
	$('#window').append(\"<div id='windowTop'></div>\");
	$('#windowTop').append(\"<div id='windowTopContent'></div>\");
	$('#windowTop').append(\"<img src='"._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_min.jpg' class='format_png' id='windowMin' alt='minimiser la fenetre' />\");
	$('#windowTop').append(\"<img src='"._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_max.jpg' class='format_png' id='windowMax' alt='agrandir la fenetre'/>\");
	$('#windowTop').append(\"<img src='"._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_close.jpg' class='format_png' id='windowClose' alt='fermer la fenetre'/>\");
	$('#window').append(\"<div id='windowBottom'></div>\");
	$('#windowBottom').append(\"<div id='windowBottomContent'>&nbsp;</div>\");
	$('#window').append(\"<div id='windowContent'></div>\");
	$('#window').append(\"<img src='"._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_resize.gif' class='format_png'  id='windowResize' alt='resize' />\");
	$('#conteneur').append(\"<a href='#' id='windowOpen'>&nbsp;</a>\");
	
	$('#window').css({left:\"".$valeur_left_FenFlo."\", top:\"".$valeur_top_FenFlo."\", width:\"".$valeur_width_FenFlo."px\", height:\"".$valeur_height_FenFlo."px\"});
	$('#windowBottom').css({height:\"".$marge_haut."px\"});
	$('#windowBottom').css(\"background-image\",\"url("._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_bottom_end.png)\");
	$('#windowBottomContent').css(\"background-image\",\"url("._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_bottom_start.png)\");
	$('#windowBottomContent').css(\"height\",\"".$marge_haut."px\");
	$('#windowTopContent').css(\"background-image\",\"url("._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_top_start.png)\");
	
	$('#windowTop').css(\"background-image\",\"url("._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_top_end.png)\");
	
	$('#windowContent').css(\"height\",\"".$marge2_haut."px\");
	$('#windowContent').css(\"width\",\"".$marge_larg."px\");
	$('#windowContent').css(\"border\",\"1px solid ".lire_config('FenFlo/couleurbordure_FenFlo','#6caf00')."\");
	
	$('#windowMax').css(\"right\",\"".$pos_bouton_close."px\");
	$('#windowMin').css(\"right\",\"".$pos_bouton_close."px\");
	
	$('#windowClose').css(\"display\",\"".$afficher_close."\");
	
	$('".lire_config('FenFlo/attribut_FenFlo','contenu')."').appendTo(\"#windowContent\");
	$('".lire_config('FenFlo/attribut_FenFlo','contenu')."').css(\"margin\",\"10px\");

	$('#windowOpen').bind(
			'click',
			function() {
				
				".$script_open."
				return false;
			}
		);
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
				$('#window').animate({height:40},300).get(0).isMinimized = true;
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
				$('#window').animate({height:windowSize.hb+43}, 300).get(0).isMinimized = false;
				$(this).hide();
				$('#windowMin, #windowResize').show();
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
					$('#windowBottom, #windowBottomContent').css('height', size.height-33 + 'px');
					var windowContentEl = $('#windowContent').css('width', size.width - 25 + 'px');
					if (!document.getElementById('window').isMinimized) {
						windowContentEl.css('height', size.height - 48 + 'px');
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
<script language=\"JavaScript\" type=\"text/javascript\">var client_id = 1;</script>

<script language=\"javascript\" type=\"text/javascript\">
function addLoadEvent(func) {
   var oldonload = window.onload;
   if (typeof window.onload != \"function\") {
      window.onload = func;
   } else {
      window.onload = function() {
         if (oldonload) {
            oldonload();
         }
         func();
      };
   }
}
addLoadEvent(function(){
		$('#windowOpen').click();

		
});


			

</script>";
	
	return $flux.$ajout_script;
}

?>
