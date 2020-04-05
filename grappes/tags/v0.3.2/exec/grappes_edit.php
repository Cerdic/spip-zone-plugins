<?php
/**
 * Plugin Groupes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

// https://code.spip.net/@exec_mots_type_dist
function exec_grappes_edit_dist()
{
	$id_grappe= intval(_request('id_grappe'));

	if (!$id_grappe) {
	  $type = $titre = filtrer_entites(_T('grappes:titre_nouvelle_grappe'));
	  $row = array();
	} else {
		$row = sql_fetsel("id_grappe,titre", "spip_grappes", "id_grappe=$id_grappe");
		if ($row) {
			$id_grappe = $row['id_grappe'];
			$type = $row['titre'];
			$titre = typo($type);
		}
	}

	if (($id_grappe AND !$row) OR
	    !autoriser($id_grappe?'modifier' : 'creer', 'grappes', $id_grappe)) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

	pipeline('exec_init',array('args'=>array('exec'=>'grappes_edit','id_grappe'=>$id_grappe),'data'=>''));
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page("&laquo; $titre &raquo;", "naviguer", "groupes");
	
	echo debut_gauche('', true);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'grappes_edit','id_groupe'=>$id_grappe),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'grappes_edit','id_groupe'=>$id_grappe),'data'=>''));
	echo debut_droite('', true);

	
	$out .= "";
	$contexte = array(
		'icone_retour'=>icone_inline(_T('icone_retour'), generer_url_ecrire("grappes",""), find_in_path("images/grappe-24.gif"), "rien.gif",$GLOBALS['spip_lang_left']),
		'titre'=>$type,
		'redirect'=>generer_url_ecrire("grappes",""),
		'new'=>_request('new') == "oui"?"oui":$id_grappe,
		'config_fonc'=>'grappes_edit_config',
	);

	$out .= recuperer_fond("prive/editer/grappe", $contexte);
	echo $out;

	echo pipeline('affiche_milieu',
		array('args' => array(
			'exec' => 'grappes_edit',
			'id_grappe' => $id_grappe
		),
		'data'=>'')
	),
	fin_gauche(),
	fin_page();
	}
}
?>
