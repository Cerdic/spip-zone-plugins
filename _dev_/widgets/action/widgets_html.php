<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function valeur_colonne_table($table, $col, $id) {
	$s = spip_query(
		'SELECT ' . $col .
		  ' AS val FROM spip_' . $table .'s	WHERE id_' . $table . '=' . $id);
	if ($t = spip_fetch_array($s)) {
		return $t['val'];
	}
	return false;
}
function ecco_widgets($texte, $status=null) {
	$return = '{ "valeur":"' . strtr($texte, 
		array('\\'=>'\\\\', "\n"=>'\n', "\r"=>'\r', '"'=>'\"')) . '"';
	if ($status) {
		$return .= ', "error":' . $status ;
	}
	die($return . '}');
}

function action_widgets_html_dist() {
	include_spip('inc/widgets');
	include_spip('inc/texte');
	include_spip('inc/rubriques');
	include_spip('tetewidgets'); # pour la fonction de droits, a isoler...

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
					AND (md5(valeur_colonne_table($regs[1], $regs[2], $regs[3]))
							== $m[2])
					) {
						include_spip('action/editer_article');
						revisions_articles($regs[3], false,
							array($regs[2] => $m[1]));

						// type du widget
						if (in_array($regs[2], array('chapo', 'texte', 'descriptif', 'ps')))
							ecco_widgets(propre($m[1]));
						else
							ecco_widgets(typo($m[1]));
					} else {
						ecco_widgets('erreur diverses', 1);
					}
				}
			}
		} else if ($modifs === false) {
			ecco_widgets("erreur", 2);
		}
	}

	// sinon on affiche le formulaire demande
	else if (preg_match(
	',(article)-(titre|surtitre|soustitre|descriptif|chapo|texte|ps)-(\d+),',
	$_GET['class'], $regs)) {

		// type du widget
		if (in_array($regs[2], array('chapo', 'texte', 'descriptif', 'ps')))
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

		$inputAttrs = array(
			'style' => "width:${w}px;" . ($h ? " height:${h}px;" : ''));

		$valeur = valeur_colonne_table($regs[1], $regs[2], $regs[3]);
		if ($valeur !== false) {
			$n = new Widget($regs[0], $valeur);
			$widgetsAction = self();
			$widgetsCode = $n->code();
			$widgetsInput = $n->input($type, $inputAttrs);
			ecco_widgets( <<<FIN_FORM

<form method="post" action="{$widgetsAction}"
	onkeyup="$(&quot;.widgets_boutons&quot;, this).show();"
	onsubmit="$(&quot;.widgets_boutons&quot;, this).hide();">
  {$widgetsCode}
  {$widgetsInput}
  <div class="widgets_boutons">
  <div style="position:absolute;">
  <input type="submit" value="ok" class="bouton-mobile" />
  <input class="cancel_widget bouton-mobile" type="button" value="cancel" />
  <input class="hide_widget bouton-mobile" type="button" value="hide" />
  </div>
  </div>
</form>

FIN_FORM
			);
		}
	}
}
?>
