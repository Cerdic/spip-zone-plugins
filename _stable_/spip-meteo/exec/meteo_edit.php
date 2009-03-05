<?php


	/**
	 * SPIP-Météo
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
	include_spip('meteo_fonctions');
	include_spip('genie/meteo');


	function exec_meteo_edit() {

		if (!autoriser('editer', 'meteo')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$id_meteo = intval($_GET['id_meteo']);

		pipeline('exec_init',array('args'=>array('exec'=>'meteo_edit','id_meteo'=>$id_meteo),'data'=>''));

		if (!empty($_POST['enregistrer'])) {
			$id_meteo	= $_GET['id_meteo'];
			$ville		= $_POST['ville'];
			$code		= $_POST['code'];

			if ($id_meteo == -1) {
				$id_meteo = sql_insertq('spip_meteo', array('ville' => $ville, 'code' => $code, 'maj' => 'NOW()', 'statut' => 'hors_ligne'));
			} else {
				sql_updateq('spip_meteo', array('ville' => $ville, 'code' => $code, 'maj' => 'NOW()'), "id_meteo=$id_meteo");
			}
			
			genie_meteo($dummy);

			$url = generer_url_ecrire('meteo', 'id_meteo='.$id_meteo, true);
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}


		if (!empty($_GET['id_meteo']) AND $_GET['id_meteo'] != -1) {
			$res = sql_select("*", "spip_meteo", "id_meteo=$id_meteo");
			$meteo = sql_fetch($res);
			$id_meteo	= $meteo['id_meteo'];
			$ville		= $meteo['ville'];
			$code		= $meteo['code'];
		}

		if (!empty($_POST['chercher'])) {
			$ville = rawurlencode($_POST['ville']);
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('meteoprive:meteo'), "naviguer", "meteo_tous");

		echo debut_gauche('', true);

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'meteo_edit','id_meteo'=>$id_meteo),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'meteo_edit','id_meteo'=>$id_meteo),'data'=>''));

		echo debut_droite('', true);

		echo '<div class="cadre-formulaire-editer">';
		echo '<div class="entete-formulaire">';
		if ($id_meteo == -1)
			echo icone_inline(_T('icone_retour'), generer_url_ecrire("meteo"), _DIR_PLUGIN_METEO.'/prive/images/meteo-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
		else
			echo icone_inline(_T('icone_retour'), generer_url_ecrire("meteo", "id_meteo=$id_meteo"), _DIR_PLUGIN_METEO.'/prive/images/meteo-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
		echo _T('meteoprive:ville_note');
		echo '<h1>'.sinon(rawurldecode($ville), _T('meteoprive:nouvelle_ville')).'</h1>';
		echo '</div>';

		echo '<div class="formulaire_spip formulaire_editer">';
		echo '<form method="post" action="'.generer_url_ecrire("meteo_edit", "id_meteo=$id_meteo").'"><div>';
	  	echo '<ul>';
	    echo '<li>';
		echo '<label for="ville">'._T('meteoprive:ville').'</label>';
		echo '<input type="text" class="text" name="ville" id="ville" value="'.rawurldecode($ville).'" />';
		echo '</li>';
		echo '</ul>';
	  	echo '<p class="boutons"><input type="submit" class="submit" name="chercher" value="'._T('meteoprive:chercher').'" /></p>';

		$url_recherche = 'http://xoap.weather.com/search/search?where=' . rawurlencode(strtolower(supprimer_numero(rawurldecode($ville))));
		$chaine = @file_get_contents($url_recherche);
		if ($chaine) preg_match_all("@<loc id=\"(.*?)\" type=(.*?)>(.*?)</loc>@s", $chaine, $regs, PREG_SET_ORDER);
		if (count($regs)) {
		  	echo '<ul>';
		    echo '<li>';
			echo '<label for="code">'._T('meteoprive:code_ville').'</label>';
			echo '<select class="list" name="code" id="code">';
			foreach ($regs as $tableau) {
				$code_recherche = $tableau[1];
				$nom_recherche = $tableau[3];
				echo '<option value="'.$code_recherche.'" ';
				if ($code == $code_recherche) echo 'selected';
				echo '>'.$code_recherche.' - '.$nom_recherche.'</option>';
			}
			echo '</select>';
			echo '</li>';
			echo '</ul>';
		  	echo '<p class="boutons"><input type="submit" class="submit" name="enregistrer" value="'._T('meteoprive:enregistrer').'" /></p>';
		}

		echo '</div>';
		echo '</div>';
	 	
		echo fin_gauche();

		echo fin_page();

	}


?>
