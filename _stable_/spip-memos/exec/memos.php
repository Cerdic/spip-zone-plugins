<?php


	/**
	 * SPIP-Mémos
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('inc/presentation');
	include_spip('inc/config');
	include_spip('inc/meta');


	function exec_memos() {

		if (!autoriser('configurer', 'memos')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'memos'),'data'=>''));

		if (!empty($_POST['valider'])) {
			if (isset($_POST['spip_memos_client'])) {
				$spip_memos_client = $_POST['spip_memos_client'];
				ecrire_meta('spip_memos_client', $spip_memos_client);
			}

			if (isset($_POST['spip_memos_serveur'])) {
				$spip_memos_serveur = addslashes($_POST['spip_memos_serveur']);
				ecrire_meta('spip_memos_serveur', $spip_memos_serveur);
			}

			if (isset($_POST['spip_memos_fond'])) {
				$spip_memos_fond = addslashes($_POST['spip_memos_fond']);
				ecrire_meta('spip_memos_fond', $spip_memos_fond);
			}

			ecrire_metas();
		}

		$spip_memos_client	= $GLOBALS['meta']['spip_memos_client'];
		$spip_memos_serveur	= $GLOBALS['meta']['spip_memos_serveur'];
		$spip_memos_fond	= $GLOBALS['meta']['spip_memos_fond'];

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('titre_configuration'), "configuration", "configuration");

		echo "<br /><br /><br />\n";
		echo gros_titre(_T('titre_configuration'),'',false);
		echo barre_onglets("configuration", "memos");

		echo debut_gauche('', true);

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'memos'),'data'=>''));

		echo debut_droite('', true);

		echo '<form method="post" action="'.generer_url_ecrire('memos').'" >';

		echo debut_cadre_trait_couleur("", true, "", _T('memos:configuration'));

		echo '<table>';

	    echo '<tr>';
		echo '<td><label for="spip_memos_serveur">'._T('memos:spip_memos_serveur').'</label></td>';
		echo '<td><input type="text" class="text" name="spip_memos_serveur" id="spip_memos_serveur" value="'.$spip_memos_serveur.'" /></td>';
		echo '</tr>';

	    echo '<tr>';
		echo '<td width="250"><label for="spip_memos_client">'._T('memos:spip_memos_client').'</label></td>';
		echo '<td><input type="text" class="text" name="spip_memos_client" id="spip_memos_client" value="'.$spip_memos_client.'" /></td>';
		echo '</tr>';

	    echo '<tr>';
		echo '<td><label for="spip_memos_fond">'._T('memos:spip_memos_fond').'</label></td>';
		echo '<td><input type="text" class="text" name="spip_memos_fond" id="spip_memos_fond" value="'.$spip_memos_fond.'" /></td>';
		echo '</tr>';

		echo '</table>';

		echo '<p style="text-align: right;"><input class="fondo" name="valider" type="submit" value="'._T('memos:valider').'" /></p>';

		echo fin_cadre_trait_couleur(true);
		
		echo '</form>';

		echo pipeline('affiche_milieu', array('args'=>array('exec'=>'memos'),'data'=>''));
		
		echo fin_gauche();

		echo fin_page();

	}


?>