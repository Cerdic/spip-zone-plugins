<?php
function switcher_affichage_final($texte){

	global 	$html;
	global $squelettes_alternatifs;
	global $styleListeSwitcher;

	if ($html) {
	
		if (SWITCHER_AFFICHER) {
			
			// Insertion du Javascript de rechargement de page
			$code='<script type="text/javascript">
						//<![CDATA[
						function gotof(url) {
						window.location=url;
						}//]]>
						</script>';	  
			
			// Insertion du selecteur de squelettes			
			$code.='<div id="plugin_switcher" style="top: 0;left: 20px; position: absolute; background-color: transparent;z-index: 100;">';
			$code.='<form action="" method="post">';
			$code.='<fieldset style="margin:0;padding:0;border:0">';
			$code.='<select name="selecteurSkel" style="'.$styleListeSwitcher.'" onchange="gotof(this.options[this.selectedIndex].value)">';
			$code.='<option selected="selected" value="">Squelettes</option>';
            if (is_array($squelettes_alternatifs))
    			foreach( $squelettes_alternatifs as $key => $value)	{
    			    $selected = ($key == $_COOKIE['spip_skel']) ? " selected='selected' " : "";
    			    $code.='<option value="'.parametre_url(self(),'var_skel',$key).'"'.$selected.'>&nbsp;-> '.$key.'</option>';
    			}
			$code.='</select>';
			$code.='</fieldset>';
			$code.='</form>';
			$code.='</div>';
			}

			
		// On rajoute le code du selecteur de squelettes avant la balise </body>
		$texte=preg_replace("</body>","$code</body>",$texte);
	}
	return($texte);
}
?>
