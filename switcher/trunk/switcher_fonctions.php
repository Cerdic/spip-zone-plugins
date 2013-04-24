<?php
function switcher_affichage_final($texte = ""){

    include_spip('inc/config');

	global 	$html;
	global $squelettes_alternatifs;
	global $styleListeSwitcher;

    $code = "";
    $texte = trim($texte);

	if ($texte) {

        //Contrôler le cas visiteur authentifié
	    $auteur_autorise = in_array($GLOBALS['visiteur_session']['id_auteur'],lire_config('switcher/auteurs_autorises',array())) ? true : false;

	    //Contrôler le cas "tout public"
	    if (lire_config('switcher/switcher_public') == "on")
    	    $auteur_autorise = true;
	
		if (SWITCHER_AFFICHER || $auteur_autorise) {
			
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
		$texte=preg_replace("/<\/body>/","$code</body>",$texte);
	}
	return($texte);
}
?>
