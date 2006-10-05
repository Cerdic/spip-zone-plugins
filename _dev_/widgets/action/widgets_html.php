<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction d'API manquante a SPIP...
function autoriser_modifs($quoi = 'article', $id = 0) {
	global $connect_id_auteur;
	$connect_id_auteur = $GLOBALS['auteur_session']['id_auteur'];

	if ($quoi != 'article') {
		echo "pas implemente";
		return false;
	}

	include_spip('inc/auth');
	auth_rubrique(); # definit $connect_toutes_rubriques (argh)
	return acces_article($id);
}

function action_widgets_html_dist() {
	include_spip('inc/widgets');
	include_spip('inc/texte');
	include_spip('inc/rubriques');

	header("Content-Type: text/html; charset=".$GLOBALS['meta']['charset']);

	// Est-ce qu'on a recu des donnees ?
	if (isset($_POST['widgets'])) {
		$modifs = post_widgets();
		if (is_array($modifs)
		AND count($modifs) >= 1) { // normalement, un seul pour l'instant...
			foreach($modifs as $m) {
				if (preg_match(
				',(article)-(titre|surtitre|soustitre|descriptif|chapo|texte|ps)-(\d+),',
				$m[0], $regs)) {
					// Enregistrer dans la base
					if ($m[2]
					AND autoriser_modifs('article', $regs[3])
					) {
						include_spip('action/editer_article');
						revisions_articles($regs[3], false,
							array($regs[2] => $m[1]));
					}

					// type du widget
					if (in_array($regs[2], array('chapo', 'texte', 'descriptif')))
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
	',(article)-(titre|surtitre|soustitre|descriptif|chapo|texte|ps)-(\d+),',
	$_GET['class'], $regs)) {

		// type du widget
		if (in_array($regs[2], array('chapo', 'texte', 'descriptif')))
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
			echo '<div style="position:absolute">';
      echo '<input type="submit" value="ok" />'."\n";
			echo '<input class="cancel_widget" type="button" value="cancel" />'."\n";
			echo '</div>'."\n";
      echo '</form>'."\n";
		}
	}
}

?>
