<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/meta');

function exec_metas() {
	if (!autoriser('voir', 'metas')) {
        include_spip('inc/minipres');
        echo minipres();
        exit;
    }

    pipeline('exec_init', array('args'=>array('exec'=>'metas'),'data'=>''));

	if (!empty($_POST['valider'])) {
		// title
		if (isset($_POST['spip_metas_title'])) {
			$spip_metas_title = $_POST['spip_metas_title'];
			ecrire_meta('spip_metas_title', $spip_metas_title);
		}

		// description
		if (isset($_POST['spip_metas_description'])) {
			$spip_metas_description = addslashes($_POST['spip_metas_description']);
			ecrire_meta('spip_metas_description', $spip_metas_description);
		}

		// keywords
		if (isset($_POST['spip_metas_keywords'])) {
			$spip_metas_keywords = addslashes($_POST['spip_metas_keywords']);
			ecrire_meta('spip_metas_keywords', $spip_metas_keywords);
		}

		// mots importants
		if (isset($_POST['spip_metas_mots_importants'])) {
			$spip_metas_mots_importants = $_POST['spip_metas_mots_importants'];
			ecrire_meta('spip_metas_mots_importants', $spip_metas_mots_importants);
		}
	}

	/***********************************************************************************************************************************************/

	$spip_metas_title				= $GLOBALS['meta']['spip_metas_title'];
	$spip_metas_description			= $GLOBALS['meta']['spip_metas_description'];
	$spip_metas_keywords			= $GLOBALS['meta']['spip_metas_keywords'];
	$spip_metas_mots_importants		= $GLOBALS['meta']['spip_metas_mots_importants'];

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('titre_configuration'), "configuration", "configuration");

	echo "<br /><br /><br />\n";
	echo gros_titre(_T('titre_configuration'),'',false);
	echo barre_onglets("configuration", "metas");

	echo debut_gauche('', true);
	echo debut_boite_info(true);
	echo _T('metas:aide_ecrire_metas');
	echo fin_boite_info(true);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'metas'),'data'=>''));

	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'config_lettres_formulaire'),'data'=>''));
	echo debut_droite('', true);

	echo '<form method="post" action="'.generer_url_ecrire('metas').'" >';
		echo debut_cadre_trait_couleur("", true, "", _T('metas:configuration_mots_importants'));
			echo '<p>';
				echo '<label for="spip_metas_mots_importants">'._T('metas:label_mots_importants').'</label>';
				echo "<textarea name=\"spip_metas_mots_importants\" cols=\"40\" rows=\"4\" class=\"forml\">$spip_metas_mots_importants</textarea>";
			echo '</p>';
			echo '<p style="text-align: right;"><input class="fondo" name="valider" type="submit" value="'._T('metas:valider').'" /></p>';
		echo fin_cadre_trait_couleur(true);

		echo debut_cadre_trait_couleur("", true, "", _T('metas:configuration_referencement'));
			echo '<p>';
				echo '<label for="spip_metas_title">'._T('metas:label_metas_title').'</label>';
				echo "<input type=\"text\" name=\"spip_metas_title\" value=\"$spip_metas_title\" class=\"forml\"/>";
			echo '</p>';
			echo '<p>';
				echo '<label for="spip_metas_description">'._T('metas:label_metas_description').'</label>';
				echo "<textarea name=\"spip_metas_description\" cols=\"40\" rows=\"4\" class=\"forml\">$spip_metas_description</textarea>";
			echo '</p>';
			echo '<p>';
				echo '<label for="spip_metas_keywords">'._T('metas:label_metas_keywords').'</label>';
				echo "<textarea name=\"spip_metas_keywords\" cols=\"40\" rows=\"4\" class=\"forml\">$spip_metas_keywords</textarea>";
			echo '</p>';
			echo '<p style="text-align: right;"><input class="fondo" name="valider" type="submit" value="'._T('metas:valider').'" /></p>';
		echo fin_cadre_trait_couleur(true);
	echo '</form>';

	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'metas'),'data'=>''));

	echo fin_gauche();

	echo fin_page();
}
?>