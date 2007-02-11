<?php
//
// exec/editer_digg.php
//
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/presentation");

function exec_editer_digg(){
	echo debut_page(_T('spipdigg:spip_diggs'));
		echo debut_gauche();
			echo debut_boite_info();
				echo "Hello World !";
			echo fin_boite_info();
			echo debut_raccourcis();
				echo '<a href="?exec=editer_digg&new=oui">'._T('spipdigg:ajouter_un_digg').'</a>';
			echo fin_raccourcis();
		echo debut_droite();
			
			echo debut_cadre_formulaire();
				echo '<form action="'.lire_meta('adresse_site').'/spip.php" method="post"><input type="hidden" name="redirect" value="'.lire_meta('adresse_site').'/spip.php" />';
				echo "
					<input name='action' type='hidden' value='editer_digg' />
					<input name='redirect' type='hidden' value='".lire_meta('adresse_site').'/ecrire/?exec=editer_digg'."' />
					<input type='hidden' name='editer_digg' value='oui' />
				";
				
				echo '<b>'._T('spipdigg:titre_digg').'</b>';
				echo '<br /><input type="text" name="titre" value="" class="formo spip_small" />';
				echo '<br />';
				echo '<b>'._T('spipdigg:url_digg').'</b>';
				echo '<br /><input type="text" name="url_digg" value="" class="forml" />';
				echo '<br />';
				echo '<b>'._T('spipdigg:descriptif_digg').'</b>';
				echo '<br />'.barre_textarea ( $texte, '10', $cols, $lang='' );
				if (_request('new') == "oui") echo '<input type="hidden" name="id_digg" value="new" />';
				echo '<br />';
				echo '<div align="right"><input type="submit" name="new" value="'._T('spipdigg:enregistrer').'" class="fondo"/></div>';
				echo '</form>';
			echo fin_cadre_formulaire();
			
	if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
	echo fin_page();
}
?>
