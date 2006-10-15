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


/**
    * Transform a variable into its javascript equivalent (recursive)
    * @access private
    * @param mixed the variable
    * @return string js script | boolean false if error
    */
function var2js( $var)
{
	$asso = false;
    switch (true) {
        case is_null($var) :
            return 'null';
        case is_string($var) :
            return '"' . addcslashes($var, "\"\\\n\r") . '"';
        case is_bool($var) :
            return $var ? 'true' : 'false';
        case is_scalar($var) :
            return $var;
	    case is_object( $var) :
	        $var = get_object_vars($var);
	        $asso = true;
        case is_array($var) :
		    $keys = array_keys($var);
		    $ikey = count($keys);
		    while (!$asso && $ikey--) {
		    	$asso = $ikey !== $keys[$ikey];
		    }
            $sep = '';
		    if ($asso) {
	            $ret = '{';
	            foreach ($var as $key => $elt) {
	                $ret .= $sep . '"' . $key . '":' . var2js($elt);
	                $sep = ',';
	            }
	            return $ret ."}\n";
		    } else {
	            $ret = '[';
	            foreach ($var as $elt) {
	                $ret .= $sep . var2js($elt);
	                $sep = ',';
	            }
	            return $ret ."]\n";
            }
    }
    return false;
}

function ecco_widgets($texte, $status=null) {
	$return = array('valeur' => $texte);
	if ($status) {
		$return['erreur'] = $status ;
	}
	return var2js($return);
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
		if (!is_array($modifs)) {
			die(ecco_widgets(_T('widgets:donnees_mal_formatees'), 1));
		}
		$anamod = $anaupd = array();
		foreach($modifs as $m) {
			if ($m[2] && preg_match(_PREG_WIDGET, 'widget '.$m[0], $regs)) {
				list(,$widget,$type,$champ,$id) = $regs;
				if (!autoriser_modifs($type, $id)) {
					die(ecco_widgets("$type $id: " . _T('widgets:non_autorise'), 2));
				}

				// alias temporaire pour titreurl, en attendant un modele
				if ($champ == 'titreurl') $champtable = 'titre';
				else $champtable = $champ;
				if (md5(valeur_colonne_table($type, $champtable, $id)) != $m[2]) {
					die(ecco_widgets("$type $id $champtable: " .
						_T('widgets:modifie_par_ailleurs'), 3));
				}
				$anamod[] = array($widget,$type,$champ,$id,$m[1]);
				if (!isset($anaupd[$type])) {
					$anaupd[$type] = array();
				}
				if (!isset($anaupd[$type][$id])) {
					$anaupd[$type][$id] = array();
				}
				$anaupd[$type][$id][$champtable] = $m[1];
			}
		}
		if (!$anamod) {
			die(ecco_widgets(_T('widgets:pas_de_modification'), 4));
		}
		foreach($anaupd as $type => $idschamps) {
			foreach($idschamps as $id => $champsvaleurs) {

				// Enregistrer dans la base
				// MODELE
				switch($type) {
					case 'article':
						include_spip('action/editer_article');
						revisions_articles($id, false, $champsvaleurs);
						break;
					default :
						die(ecco_widgets("$type: " . _T('widgets:non_implemente'), 5));
				}
			}
		}
		foreach($anamod as $m) {
			list($widget,$type,$champ,$id,$valeur) = $m;

			// VUE
			// chercher vues/article_toto.html
			// sinon vues/toto.html
			if (find_in_path( ($fond = 'vues/' . $type . '_' . $champ) . '.html')
			 || find_in_path( ($fond = 'vues/' . $champ) .'.html') ) {
				$contexte = array(
					'id_' . $type => $id,
					'lang' => $GLOBALS['spip_lang']
				);
				include_spip('public/assembler');
				echo ecco_widgets(recuperer_fond($fond, $contexte));
			}
			// vues par defaut
			elseif (in_array($champ, array('chapo', 'texte', 'descriptif', 'ps'))) {
				echo ecco_widgets(propre($valeur));
			} else {
				echo ecco_widgets(typo($valeur));
			}
		}
	}

	// CONTROLEUR
	// sinon on affiche le formulaire demande
	else if (preg_match(_PREG_WIDGET, $_GET['class'], $regs)) {
		list(,$widget,$type,$champ,$id) = $regs;
		if (!autoriser_modifs($type, $id)) {
			die(ecco_widgets("$type $id: " . _T('widgets:non_autorise'), 2));
		}
		$f = charger_fonction($type.'_'.$champ, 'controleurs', true)
		OR $f = charger_fonction($champ, 'controleurs', true)
		OR $f = 'controleur_dist';
		list($html,$status) = $f($regs);
		echo ecco_widgets($html, $status);
		exit;
	} else {
		die(ecco_widgets(_T('widgets:donnees_mal_formatees'), 1));
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
		$html = "$type $id $champ: " . _T('widgets:pas_de_valeur');
		$status = 6;
	}

	return array($html,$status);
}

?>
