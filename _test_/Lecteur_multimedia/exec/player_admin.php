<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_player_admin()
{

include_spip('inc/presentation');
include_spip('inc/meta');
include_spip('inc/config');

function configurer_spip_listes() {

  if ($player = _request('player')) {
 	                $player = addslashes($player);
 	                ecrire_meta('player', $player);
 	            } 

 
 	             	  
ecrire_metas();
}

configurer_spip_listes();

// Admin SPIP-Listes
debut_page("Lecteur multimedia");



debut_gauche();

creer_colonne_droite();


debut_droite("Lecteur multimedia");


echo "<form action='".generer_url_ecrire('player_admin')."' method='post'>";

echo '<br />';
		debut_cadre_trait_couleur("", false, "", "Configuration");

		debut_cadre_relief("", false, "", "Player audio");


$player_ = lire_meta('player');
		echo bouton_radio("player", "neoplayer", "Neolao player", $player_ == "neoplayer");
		echo "<br />";
		echo bouton_radio("player", "dewplayer", "Dew player", $player_ == "dewplayer");
		echo "<br />";
		echo bouton_radio("player", "pixplayer", "One pixel out player", $player_ == "pixplayer");
		echo "<br />";
		echo bouton_radio("player", "neoplayer_multi", "Neolao player multi", $player_ == "neoplayer_multi");

echo "<input type='submit' name='valid_smtp' value='"._T('valider')."' class='fondo' style='float:right'>";

		fin_cadre_relief();
		fin_cadre_trait_couleur();

echo "</form>";	


fin_page();

}

?>
