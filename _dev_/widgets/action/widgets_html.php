<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

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
					// TODO: on pourrait tester aussi le md5 envoye contre celui
					// qui correspond a la base actuelle : dans ce cas avertir
					// que "le contenu a ete modifie entre temps", et renvoyer
					// un formulaire ad-hoc.
					) {
						include_spip('action/editer_article');
						revisions_articles($regs[3], false,
							array($regs[2] => $m[1]));

						// type du widget
						if (in_array($regs[2], array('chapo', 'texte', 'descriptif')))
							echo propre($m[1]);
						else
							echo typo($m[1]);
					} else {
						die();
					}
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

		// taille du widget
		$w = intval($_GET['w']);
		$h = intval($_GET['h']);
		if ($w<100) $w=100;
		if ($w>700) $w=700;
		if ($type == 'texte') {
			if ($h<36) $h=36; #ici on pourrait mettre minimum 3*$_GET['em']
		}
		else // ligne, hauteur naturelle
			$h='';#$hx = htmlspecialchars($_GET['em']);

		if ($h>700) $h=700; // hauteur maxi d'un textarea -- pas assez ? trop ?

#		if (!isset($hx)) $hx = $h.'px';
		$style = "width:${w}px;";
		if ($h) $style.="height:${h}px;";

		$s = spip_query("SELECT ".$regs[2]." AS val FROM spip_".$regs[1]."s
		WHERE id_".$regs[1]."=".$regs[3]);
		if ($t = spip_fetch_array($s)) {
			echo "<form method='post' action='".self()."' onkeyup=\"\$(&quot;.bouton-mobile&quot;,this).show();\"
onsubmit=\"\$(&quot;.bouton-mobile&quot;,this).hide();\">\n";
			$n = new Widget($regs[0], $t['val']);
			echo $n->code();
			echo inserer_attribut($n->input($type), 'style', $style);
			echo '<div style="float:right; width:150px">';
			echo '<div style="position:absolute;">';
			echo '<input type="submit" value="ok" class="bouton-mobile" style="display:none;" />'."\n";
			echo '<input class="cancel_widget bouton-mobile" type="button" value="cancel" style="display:none;" />'."\n";
			echo '<input class="hide_widget bouton-mobile" type="button" value="hide" style="display:none;" />'."\n";
			echo '</div>'."\n";
			echo '</div>'."\n";
			echo '</form>'."\n";
		}
	}
}

?>
