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

	$meta_paquet = unserialize($GLOBALS['meta']['spip_loader']);

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

	if($le_paquet = _request('paquet')) {
		$meta_paquet[$le_paquet] = serialize(array(date('Y-m-d H:i:s'),$connect_id_auteur));
		ecrire_meta('spip_loader', serialize($meta_paquet));
		echo "<p>"._L("Vous venez de mettre le paquet \"".$le_paquet.
			"\" &agrave; jour avec succ&egrave;s.")."</p>";
		ecrire_metas();
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
			$selected = $paquet==$le_paquet?' selected="selected"':'';
			echo "<option value='$paquet'$selected>".$paquet
				." depuis ".$url."</option>\n";
		}
		echo "</select>
		<input type='submit' value='Update' />
		</form>
		";

	} else
		echo _L("D&eacute;sol&eacute;, aucune contribution &grave; mettre à jour.");

	//Suivi des maj
	if(!empty($spip_loader_liste)) {
		$liste = '';
		foreach ($spip_loader_liste as $paquet => $url) {
			$liste .= "\t<li>".$paquet." : ";
			if($meta_paquet[$paquet]) {
				list($date,$id_auteur) = unserialize($meta_paquet[$paquet]);
				if($r=spip_fetch_array(spip_query("SELECT nom FROM spip_auteurs WHERE id_auteur=$id_auteur")))
					$nom = $r['nom'];
				else
					$nom = _L('Inconnu');
				$liste .= affdate($date).' &agrave; '.heures_minutes($date).' par '.$nom;
			}
			else
				$liste .= _L("jamais");
			echo "</li>\n";
		}
		echo "<ul>\n".$liste."</ul>\n";
	}

	echo fin_page();

}

?>