<?php
//include_spip('inc/presentation');
include_spip('inc/texte');
include_spip('inc/layer');

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_TWEAK_SPIP',(_DIR_PLUGINS.end($p)));

function tweak_styles() {
	global $couleur_claire;
	echo "<style type='text/css'>\n";
	echo <<<EOF
div.cadre-padding ul li {
	list-style:none ;
}
div.cadre-padding ul {
	padding-left:1em;
	margin:.5em 0 .5em 0;
}
div.cadre-padding ul ul {
	border-left:5px solid #DFDFDF;
}
div.cadre-padding ul li li {
	margin:0;
	padding:0 0 0.25em 0;
}
div.cadre-padding ul li li div.nomplugin, div.cadre-padding ul li li div.nomplugin_on {
	border:1px solid #AFAFAF;
	padding:.3em .3em .6em .3em;
	font-weight:normal;
}
div.cadre-padding ul li li div.nomplugin a, div.cadre-padding ul li li div.nomplugin_on a {
	outline:0;
	outline:0 !important;
	-moz-outline:0 !important;
}
div.cadre-padding ul li li div.nomplugin_on {
	background:$couleur_claire;
	font-weight:bold;
}
div.cadre-padding div.droite label {
	padding:.3em;
	background:#EFEFEF;
	border:1px dotted #95989F !important;
	border:1px solid #95989F;
	cursor:pointer;
	margin:.2em;
	display:block;
	width:10.1em;
}
div.cadre-padding input {
	cursor:pointer;
}
div.detailplugin {
	border-top:1px solid #B5BECF;
	padding:.6em;
	background:#F5F5F5;
}
div.detailplugin hr {
	border-top:1px solid #67707F;
	border-bottom:0;
	border-left:0;
	border-right:0;
	}
EOF;
	echo "</style>";
}

function exec_tweak_spip_admin() {
  global $connect_statut, $connect_toutes_rubriques;
  global $spip_lang_right;
  global $couleur_claire;
  global $tweaks;

  include_spip('tweak_spip_config');
  include_spip("inc/presentation");
//  include_spip ("base/abstract_sql");

  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
	echo _T('avis_non_acces_page');
	fin_page();
	exit;
  }
/*
	// mise a jour des donnees si envoi via formulaire
	// sinon fait une passe de verif sur les plugin
	if (_request('changer_tweaks')=='oui'){
		enregistre_modif_plugin();
		// pour la peine, un redirige, 
		// que les plugin charges soient coherent avec la liste
//		redirige_par_entete(generer_url_ecrire('tweak_spip_admin'));
	}
	else
		verif_plugin();
	if (isset($_GET['surligne']))
		$surligne = $_GET['surligne'];
*/
  debut_page(_T('tweak:titre'), 'configuration', 'tweak_spip');
  tweak_styles();

	echo '<br><br><br>';

	gros_titre(_T('tweak:titre'));

	/*Affichage*/
	debut_gauche();	
	
	debut_boite_info();
	echo propre(_T('tweak:help'));
	fin_boite_info();

	debut_droite();

	debut_cadre_relief();

	global $couleur_foncee;
	echo "\n<table border='0' cellspacing='0' cellpadding='5' width='100%'>";
	echo "<tr><td bgcolor='$couleur_foncee' background='' colspan='4'><b>";
	echo "<font face='Verdana,Arial,Sans,sans-serif' size='3' color='#ffffff'>";
	echo _T('tweak:tweaks_liste')."</font></b></td></tr>";

	echo "<tr><td class='serif' colspan=4>";
	echo _T('tweak:texte_presente_tweaks');

	echo generer_url_post_ecrire("tweak_spip_admin");

	echo "<ul>";
	
	foreach($temp = $tweaks as $tweak) {
		echo "<li>";
		echo ligne_tweak($tweak);
		echo "</li>\n"; 
	}
	
//	echo "\n<input type='hidden' name='id_auteur' value='$connect_id_auteur' />";
//	echo "\n<input type='hidden' name='hash' value='" . calculer_action_auteur("valide_plugin") . "'>";
	echo "\n<input type='hidden' name='changer_tweaks' value='oui'>";

	echo "\n<p>";

	echo "<div style='text-align:$spip_lang_right'>";
	echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' onclick=\"alert('à faire, si vous trouvez un moyen simple de stocker l\'état des tweaks !')\">";
	echo "</div>";

# ce bouton est trop laid :-)
# a refaire en javascript, qui ne fasse que "decocher" les cases
#	echo "<div style='text-align:$spip_lang_left'>";
#	echo "<input type='submit' name='desactive_tous' value='"._T('bouton_desactive_tout')."' class='fondl'>";
#	echo "</div>";

	echo "</form></td></tr></table>\n";

//	echo "<br />";

/*
	echo '<form action="'.generer_url_ecrire('config_chercher_squelettes_mots').'" method="post">';

	$groupes_mots = '';
	$select = array('id_groupe','titre');
	$from = array('spip_groupes_mots');

	//	include_ecrire('inc_filtres');
	$rez = spip_abstract_select($select,$from);
	while($row = spip_abstract_fetch($rez)) {
	  $groupes_mots[$row['id_groupe']] = extraire_multi($row['titre']);
	}
	spip_abstract_free($rez);

	//TODO: trouver automatiquement ces informations pour toutes les tables avec un jonction sur les mots
	$id_tables = array('articles' => 'id_article',
					   'rubriques' => 'id_rubrique',
					   'breves' => 'id_breve',
					   'sites' => 'id_site');
	

	$fonds = unserialize(lire_meta('SquelettesMots:fond_pour_groupe'));

	$field_fonds = $_REQUEST['fonds'];
	$id_groupes = $_REQUEST['tid_groupe'];
	$types = $_REQUEST['type'];
	$actif = $_REQUEST['actif'];
	
	// On transforme les _POST en jolie tableau
	if($field_fonds) {
	  $new_fonds = array();
	  foreach($field_fonds as $index => $fond) {		
		$index = intval($index);
		$fond = addslashes($fond);
		if($actif[$index]) {
		  $id_groupe = intval($id_groupes[$index]);
		  $type = addslashes($types[$index]);
		  $new_fonds[$fond] = array($id_groupe,$type,$id_tables[$type]);
		} 
	  }
	  $fonds = $new_fonds;
	}
	
	$index = 0;
	if (is_array($fonds))
	foreach($fonds as $fond => $a) {
	  list($id_groupe,$type,$id_table) = $a;
	  $index++;
	  echo '<fieldset class="regle">';
	  echo '<legend>'._T('squelettesmots:reglei',array('id'=>$index)).'</legend>';
	  echo '<div class="champs">';
	  echo "<input type=\"checkbox\" class=\"actif\" name=\"actif[$index]\" checked=\"true\"/>";
	  echo "<label for=\"fond_$index\" class=\"fond\">"._T('squelettesmots:fond')."</label>";
	  echo "<input type=\"text\" name=\"fonds[$index]\" class=\"fond\" value=\"$fond\" id=\"fond_$index\"/>";
	  echo "<label for=\"id_groupe_$index\" class=\"id_groupe\">"._T('squelettesmots:groupe')."</label>";
	  echo "<select name=\"tid_groupe[$index]\" class=\"id_groupe\" id=\"id_groupe_$index\">";
	  foreach($groupes_mots as $id => $titre) {
		echo "<option value=\"$id\"".(($id_groupe == $id)?' selected="true"':'').">$titre</option>";
	  }
	  echo '</select>';
	  echo "<label for=\"type_$index\" class=\"type\">"._T('squelettesmots:type')."</label>";
	  echo "<select name=\"type[$index]\" class=\"type\" id=\"type_$index\">";
	  foreach($id_tables as $t => $x) {
		echo "<option value=\"$t\"".(($type == $t)?' selected="true"':'').">$t</option>";
	  }
	  echo '</select>';
	  echo '</div>';
	  $select1 = array('titre');
	  $from1 = array('spip_mots AS mots');
	  $where1 = array("id_groupe=$id_groupe");
	  $rez =spip_abstract_select($select1,$from1,$where1);
	  $liste_squel = '<ul>';
	  $ext = 'html'; //On force a html, c'est pas beau, mais je vois pas la solution actuellement.
	  $cnt_actif = 0;
	  $cnt_inactif = 0;
	  while ($r = spip_abstract_fetch($rez)) {
		include_ecrire("inc_charsets");
		$n = translitteration(preg_replace('/["\'.\s]/','_',extraire_multi($r['titre'])));
		if ($squel = find_in_path("$fond-$n.$ext")) {
		  $cnt_actif++;
		  $liste_squel .= "<li><a href=\"$squel\">$fond-$n.$ext</a></li>";
		} else {
		  $cnt_inactif++;
 		  $liste_squel .= "<li>$fond-$n.$ext</li>";
		}
		if ($squel = find_in_path("$fond=$n.$ext")) {
		  $cnt_actif++;
		  $liste_squel .= "<li><a href=\"$squel\">$fond=$n.$ext</a></li>";
		} else {
		  $cnt_inactif++;
 		  $liste_squel .= "<li>$fond=$n.$ext</li>";
		}
	  }
	  spip_abstract_free($rez);
	  $liste_squel .= '</ul>';

	  
	  echo '<div class="possible">';
	  if($cnt_actif+$cnt_inactif > 0) echo bouton_block_invisible("regle$index");
	  echo _T('squelettesmots:possibilites',array('total_actif' => $cnt_actif, 'total_inactif'=>$cnt_inactif));
	  if ($cnt_actif+$cnt_inactif > 0) {
		echo debut_block_invisible("regle$index");
		echo $liste_squel;
		echo fin_block();
	  }
	  echo '</div>';

	  echo '</fieldset>';
	}
	
	$index++;
	
	echo '<hr/>';
	echo '<fieldset class="nouvelle_regle">';
	echo '<legend>'._T('squelettesmots:nouvelle_regle').'</legend>';
	echo "<input type=\"checkbox\" class=\"actif\" name=\"actif[$index]\"/>";
	echo "<label for=\"fond_$index\" class=\"fond\">"._T('squelettesmots:fond')."</label>";
	echo "<input type=\"text\" name=\"fonds[$index]\" class=\"fond\" value=\"article\"/>";
	echo "<label for=\"id_groupe_$index\" class=\"id_groupe\">"._T('squelettesmots:groupe')."</label>";
	echo "<select name=\"tid_groupe[$index]\" class=\"id_groupe\" id=\"id_groupe_$index\">";
	foreach($groupes_mots as $id => $titre) {
	  echo "<option value=\"$id\">$titre</option>";
	}
	echo '</select>';
	echo "<label for=\"type_$index\" class=\"type\">"._T('squelettesmots:type')."</label>";
	echo "<select name=\"type[$index]\" class=\"type\" id=\"type_$index\">";
	foreach($id_tables as $t => $x) {
	  echo "<option value=\"$t\">$t</option>";
	}
	echo '</select>';
	echo '</fieldset>';
	
	echo '<input type="submit" value="'._T('valider').'"/>';
	echo '</form>';
*/	
  
//  ecrire_meta('SquelettesMots:fond_pour_groupe',serialize($fonds));
//  ecrire_metas();
  
  fin_page();
  
}

function ligne_tweak($tweak){
	static $id_input=0;
	$inc = $tweak['include'];
	$actif = $tweak['actif'];
	$puce = $actif?'puce-verte.gif':'puce-rouge.gif';
	$titre_etat = _T('tweak:'.($actif?'':'in').'actif');
	$tweak_id = $inc.$id_input;
	
	$s = "<div id='$tweak_id' class='nomplugin".($actif?'_on':'')."'>";
/*
	if (isset($info['erreur'])){
		$s .=  "<div style='background:".$GLOBALS['couleur_claire']."'>";
		$erreur = true;
		foreach($info['erreur'] as $err)
			$s .= "/!\ $err <br/>";
		$s .=  "</div>";
	}

	// puce d'etat du plugin
	// <etat>dev|experimental|test|stable</etat>
	$etat = 'dev';
	if (isset($info['etat']))
		$etat = $info['etat'];
	switch ($etat) {
		case 'experimental':
			$puce = 'puce-rouge.gif';
			$titre_etat = _T('plugin_etat_experimental');
			break;
		case 'test':
			$puce = 'puce-orange.gif';
			$titre_etat = _T('plugin_etat_test');
			break;
		case 'stable':
			$puce = 'puce-verte.gif';
			$titre_etat = _T('plugin_etat_stable');
			break;
		default:
			$puce = 'puce-poubelle.gif';
			$titre_etat = _T('plugin_etat_developpement');
			break;
	}
*/
	$s .= "<img src='"._DIR_IMG_PACK."$puce' width='9' height='9' style='border:0;' alt=\"$titre_etat\" title=\"$titre_etat\" />&nbsp;";

	$s .= "<input type='checkbox' name='tweak_$inc' value='O' id='label_$id_input'";
	$s .= $actif?" checked='checked'":"";
	$s .= " onclick='verifchange.apply(this,[\"$inc\"])' /> <label for='label_$id_input' style='display:none'>"._T('tweak:activer_tweak')."</label>";
	$id_input++;

	$s .= bouton_block_invisible($tweak_id) . propre($tweak['nom']);

	$s .= "</div>";

	$s .= debut_block_invisible($tweak_id);

	$s .= "\n<div class='detailplugin'>";
	if (isset($tweak['description'])) $s .= propre($tweak['description']);
	if (isset($tweak['auteur'])) $s .= "<br/><br/>" . _T('auteur') .' '. propre($tweak['auteur']) . "<hr/>";
	$s .= _T('tweak:tweak') ." $inc.php | ". $tweak['pipeline'];
	if (isset($tweak['fonction']))  $s .= " | " . $tweak['fonction'];
	$s .= "</div>";

	$s .= fin_block();

	return $s;
}
?>
