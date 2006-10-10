<?php

function player_affiche_config_form($exec_page){
		global $spip_lang_right, $spip_lang_left;
		
	  if ($player = _request('player')) {
			ecrire_meta('player', $player);
			ecrire_metas();
		}
		
		$out = "";
		$out .= debut_cadre_trait_couleur("", true, "", "Player Audio");
		
		$out .= "<form action='".generer_url_ecrire('player_admin')."' method='post'>";
	
	
		$player_ = $GLOBALS['meta']['player'];
		$out .= bouton_radio("player", "neoplayer", "Neolao player", $player_ == "neoplayer");
		$out .= "<br />";
		$out .= bouton_radio("player", "dewplayer", "Dew player", $player_ == "dewplayer");
		$out .= "<br />";
		$out .= bouton_radio("player", "pixplayer", "One pixel out player", $player_ == "pixplayer");
		$out .= "<br />";
		$out .= bouton_radio("player", "neoplayer_multi", "Neolao player multi", $player_ == "neoplayer_multi");
	
		$out .= "<div style='text-align:$spip_lang_right'><input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'></div>";
		
		$out .= "</form>";	
		$out .= fin_cadre_trait_couleur(true);
		return $out;
}

function player_affiche_milieu($flux){
	$exec = $flux['args']['exec'];
	$out = "";
	if ($exec=='config_fonctions'){	
		$out .= player_affiche_config_form('config_fonctions');
	}
	$flux['data'].=$out;
	return $flux;
}

?>