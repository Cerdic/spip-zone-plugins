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
	echo "<br /><br /><br />";
	gros_titre(_L('Update Spip_Loader'));


	if ($connect_statut != '0minirezo'
	OR !in_array($connect_id_auteur, explode(':', _SPIP_LOADER_UPDATE_AUTEURS))) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	debut_gauche();

		debut_boite_info();

			echo '<p>'._L('Pr&eacute;sentation du plugin:<br />Il s\'agit d\'une interface graphique
			permettant la mise &agrave; jour de SPIP ou d\'autres contributions.').'</p>';

	if($test_local_script = file_exists($f = _SPIP_LOADER_LOCAL_SCRIPT)) {

		$date_locale = filemtime($f);
		$date_affichee = date('Y-m-d H:i:s', $date_locale);
		echo '<p><span style="font-size: 1.1em; font-family: monospace; font-weight: bold;">' .
			joli_repertoire($f) .
			'</span>' .
			_L(' a &eacute;t&eacute; modifi&eacute; ') .
			date_relative($date_affichee) .
			' sur votre site.</p>';

		//recuperer la date de vérification du fichier distant
		$date_verif_distant = $meta_paquet['spip_loader'];
		echo '<p>Derni&egrave;re date de v&eacute;rification du script distant:<br />' .
			($date_verif_distant ? date_relative($date_verif_distant) : _L('Jamais')) .
			'</p>';
		echo '<p>' .
			(strtotime($date_affichee)>strtotime($date_verif_distant) ?
				'' :
				_L('<b>Attention !</b> Votre script a &eacute;t&eacute; mis &agrave; jour depuis votre derni&egrave;re v&eacute;rification.')) .
			'</p>';

		if(_request('verif_script_reference')=='oui') {
			include_spip('inc/distant');
			if($source_script = recuperer_page(
				_SPIP_LOADER_SOURCE_SCRIPT,
				false, true, 1048576, '', '', false,
				$date_affichee
			)){
				if(preg_match(',Last-Modified: (.*),', $source_script, $r)){
					$date_reference = $r[1];
					echo '<p>Derni&egrave;re mise &agrave; jour du script de r&eacute;f&eacute;rence:<br />' .
					date_relative($date_reference) .
					'</p>';
					echo '<p>' .
					(strtotime($date_affichee)>strtotime($date_reference) ?
						_L('Votre script semble &ecirc;tre &agrave; jour.') :
						_L('Le script de r&eacute;f&eacute;rence a &eacute;t&eacute; modifi&eacute; depuis votre derni&egrave;re mise &agrave; jour.<br />Nous vous conseillons de mettre &agrave; jour votre script par ftp.')) .
					'</p>';
				}
				$meta_paquet['spip_loader'] = date('Y-m-d H:i:s');
				ecrire_meta('spip_loader', serialize($meta_paquet));
				ecrire_metas();
			}
			else {
				echo _L('<p>Impossible de v&eacute;rifier le script distant.</p>');
			}
		}
		else {
			echo '<p>'._L('Si vous le souhaitez, vous pouvez v&eacute;rifier que votre script est &agrave; jour par rapport au script de r&eacute;f&eacute;rence en cliquant sur le bouton ci-dessous:').'</p><form action="./" method="get">
				<input type="hidden" name="exec" value="spip_loader_update" />
				<input type="hidden" name="verif_script_reference" value="oui" />
				<input type="submit" value="Verifier Maintenant" />
			</form>';
		}
	}
	else{
			echo _L('<p>Script de t&eacute;l&eacute;chargement introuvable.</p>
			<p>Vous ne pouvez pas utiliser ce plugin.</p>');
	}

		fin_boite_info();

	creer_colonne_droite();
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
		<option value=''>&nbsp;</option>";
		foreach ($spip_loader_liste as $paquet => $url) {
			$selected = $paquet==$le_paquet?' selected="selected"':'';
			echo "<option value='$paquet'$selected>".$paquet
				." depuis ".$url."</option>\n";
		}
		echo "</select>
		<input type='submit' value='Update' ".($test_local_script?'':'disabled="disabled"')."/>
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

	echo fin_gauche(), fin_page();

}

?>
