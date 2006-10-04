<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_widgets_html_dist() {
	include_spip('inc/widgets');
	include_spip('inc/texte');

	header("Content-Type: text/html; charset=".$GLOBALS['meta']['charset']);

	// Est-ce qu'on a recu des donnees ?
	if (isset($_POST['widgets'])) {
		$modifs = post_widgets();
		if (is_array($modifs)
		AND count($modifs) >= 1) { // normalement, un seul pour l'instant...
			foreach($modifs as $m) {
				if (preg_match(
				',(article|rubrique)-(titre|surtitre|soustitre|chapo)-(\d+),',
				$m[0], $regs)) {
					if ($m[2]) {
						// TODO : appeler l'action/editer_article
						spip_query("UPDATE spip_".$regs[1]."s
						SET ".$regs[2]."='".
						addslashes($m[1])."' WHERE id_".$regs[1]."=".$regs[3]);
					}

					// type du widget
					if ($regs[2] == 'chapo')
						echo propre($m[1]);
					else
						echo typo($m[1]);
				}
			}
		} else if ($modifs === false) {
			echo "erreur";
		}
	}

	// sinon on affiche le formulaire demande
	else if (preg_match(
	',(article|rubrique)-(titre|surtitre|soustitre|chapo)-(\d+),',
	$_GET['class'], $regs)) {

		// type du widget
		if ($regs[2] == 'chapo')
			$type = 'texte';
		else
			$type = 'ligne';

		$s = spip_query("SELECT ".$regs[2]." AS val FROM spip_".$regs[1]."s
		WHERE id_".$regs[1]."=".$regs[3]);
		if ($t = spip_fetch_array($s)) {
			echo "<form method='post' action='".self()."'>\n";
			$n = new SecureWidget($regs[0], $t['val']);
			echo $n->code();
			echo $n->input($type);
			echo '<input type="submit" value="ok" />'."\n".'</form>'."\n";
		}
	}
}

?>