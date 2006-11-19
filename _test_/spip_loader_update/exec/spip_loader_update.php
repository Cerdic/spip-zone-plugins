<?php

include_spip('inc/spip_loader_update');

# securite
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_spip_loader_update() {
	global $connect_statut, $connect_id_auteur, $connect_toutes_rubriques;
	$spip_loader_liste = array();	
	include_spip('inc/presentation');
	include_spip('inc/config');

	pipeline('exec_init',
		array('args'=>array('exec'=>'configuration'),'data'=>''));

	debut_page(_L('Update Spip_Loader'), "configuration", "configuration");
	echo "<br><br><br>";
	gros_titre(_L('Update Spip_Loader'));


	if ($connect_statut != '0minirezo'
	OR !in_array($connect_id_auteur, explode(':', _SPIP_LOADER_UPDATE_AUTEURS))) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	debut_gauche();
	debut_droite();

	if($paquet = _request('paquet')) {
		echo "<p>"._L("Vous venez de mettre le paquet \"".$paquet.
			"\" &agrave; jour avec succ&egrave;s.")."</p>";
	}

	$spip_loader_liste = spip_loader_liste();

	if (!empty($spip_loader_liste)) {

		// Menu

		echo "<p>"._L("Choisir le paquet &agrave; mettre &agrave; jour&nbsp;: ")."</p>";
		echo "<form action='../spip.php?action=spip_loader' method='get'>
		<input type='hidden' name='action' value='spip_loader' />
		<select name='paquet'>
		<option value=''></option>";
		foreach ($spip_loader_liste as $paquet => $url) {
			echo "<option value='$paquet'>".$paquet
				." depuis ".$url."</option>\n";
		}
		echo "</select>
		<input type='submit' value='Update' />
		</form>
		";

	} else
		echo _L("D&eacute;sol&eacute;, aucun r&eacute;pertoire n'est accessible en SVN.");

	echo fin_page();

}

?>