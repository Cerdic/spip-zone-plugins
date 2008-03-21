<?php

	// inc/player_affiche_config_form.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

// CP-20080321 : déplacement de la fonction initiale ici. Peut-être à recoler en page admin ?

function player_affiche_config_form ($exec_page){
		global $spip_lang_right, $spip_lang_left;
		
	  if ($player = _request('player')) {
			ecrire_meta('player', $player);
			ecrire_metas();
		}
		
		$out = "";
		$out .= debut_cadre_trait_couleur(_DIR_PLUGIN_PLAYER_IMAGES."player-son-24.png", true, "", "Player Audio");
		
		$out .= "<form action='".generer_url_ecrire($exec_page)."' method='post'><div>";
	
	
		$player_ = $GLOBALS['meta']['player'];
		$out .= bouton_radio("player", "neoplayer", "Neolao player", $player_ == "neoplayer");
		$out .= "<br />";
		$out .= bouton_radio("player", "dewplayer", "Dew player", $player_ == "dewplayer");
		$out .= "<br />";
		$out .= bouton_radio("player", "pixplayer", "One pixel out player", $player_ == "pixplayer");
		$out .= "<br />";
		$out .= bouton_radio("player", "eraplayer", "Erational player", $player_ == "eraplayer");
	
		$out .= "<div style='text-align:$spip_lang_right'><input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' /></div>";
		
		$out .= "</div></form>";	
		$out .= fin_cadre_trait_couleur(true);

		return ($out);
}

?>