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
	return $return . '}';
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
				if (preg_match(_PREG_WIDGET, 'widget '.$m[0], $regs)) {
					list(,$widget,$type,$champ,$id) = $regs;

					// alias temporaire pour titreurl, en attendant un modele
					if ($champ == 'titreurl') $champtable = 'titre';
					else $champtable = $champ;

					// Enregistrer dans la base
					if ($m[2] // cle md5
					AND autoriser_modifs($type, $id)
					AND (md5(valeur_colonne_table($type, $champtable, $id)) == $m[2])
					) {
						// MODELE
						switch($type) {
							case 'article':
								include_spip('action/editer_article');
								revisions_articles($id, false,
									array($champtable => $m[1]));
								break;
						}

						// VUE
						// chercher vues/article_toto.html
						// sinon vues/toto.html
						if (find_in_path(
						($fond = 'vues/'.$type.'_'.$champ).'.html')
						OR find_in_path(
						($fond = 'vues/'.$champ).'.html')
						) {
							$contexte = array(
								'id_'.$type => $id,
								'lang' => $GLOBALS['spip_lang']
							);
							include_spip('public/assembler');
							echo ecco_widgets(recuperer_fond($fond, $contexte));
						}
						// vues par defaut
						else
						if (in_array($champ,
						array('chapo', 'texte', 'descriptif', 'ps')))
							echo ecco_widgets(propre($m[1]));
						else
							echo ecco_widgets(typo($m[1]));
					} else {
						echo ecco_widgets('erreur diverses', 1);
					}
				}
			}
		} else if ($modifs === false) {
			echo ecco_widgets("erreur", 2);
		}
	}

	// CONTROLEUR
	// sinon on affiche le formulaire demande
	else if (preg_match(_PREG_WIDGET, $_GET['class'], $regs)) {
		list(,$widget,$type,$champ,$id) = $regs;
		$f = charger_fonction($type.'_'.$champ, 'controleurs', true)
		OR $f = charger_fonction($champ, 'controleurs', true)
		OR $f = 'controleur_dist';
		list($html,$status) = $f($regs);
		echo ecco_widgets($html, $status);
		exit;
	} else {
		die ("euh ?");
	}

}


function controleur_dist($regs) {
	list(,$widget,$type,$champ,$id) = $regs;

	// type du widget
	if (in_array($champ, array('chapo', 'texte', 'descriptif', 'ps')))
		$mode = 'texte';
	else
		$mode = 'ligne';

	// taille du widget
	$w = intval($_GET['w']);
	$h = intval($_GET['h']);
	if ($w<100) $w=100;
	if ($w>700) $w=700;
	if ($mode == 'texte') {
		if ($h<36) $h=36; #ici on pourrait mettre minimum 3*$_GET['em']
	}
	else // ligne, hauteur naturelle
		$h='';#$hx = htmlspecialchars($_GET['em']);

	if ($h>700) $h=700; // hauteur maxi d'un textarea -- pas assez ? trop ?

	$inputAttrs = array(
		'style' => "width:${w}px;" . ($h ? " height:${h}px;" : ''));

	$valeur = valeur_colonne_table($type, $champ, $id);
	if ($valeur !== false) {
		$n = new Widget($widget, $valeur);
		$widgetsAction = self();
		$widgetsCode = $n->code();
		$widgetsInput = $n->input($mode, $inputAttrs);

		$html =
		<<<FIN_FORM

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

FIN_FORM;
		$status = NULL;

	}
	else {
		$html = "$type-$champ n'a pas de contr&ocirc;leur";
		$status = 3;
	}

	return array($html,$status);
}

?>
