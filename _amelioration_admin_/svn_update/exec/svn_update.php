<?php

# redefinissables dans ecrire/mes_options ; si on veut en mettre
# plusieurs separer par des virgules
define('_SVN_UPDATE_AUTEURS', '1');
define('_SVN_UPDATE_DIRS', './');


# securite
if (!defined("_ECRIRE_INC_VERSION")) return;


// la fonction qui fait le travail
function update_svn($dir) {

	$user = ''; # TODO si on veut

	$out = array();
	$command = "svn $user update ".$dir;
	spip_log($command);

	exec($command,$out);

	return end($out);
}


function exec_svn_update() {
	global $connect_statut, $connect_id_auteur, $connect_toutes_rubriques;
	global $exec;
	include_spip('inc/presentation');
	include_spip('inc/config');

	pipeline('exec_init',
		array('args'=>array('exec'=>'configuration'),'data'=>''));

	debut_page(_L('Update SVN'), "configuration", "configuration");
	echo "<br><br><br>";
	gros_titre(_L('Update SVN'));


	if ($connect_statut != '0minirezo'
	OR !in_array($connect_id_auteur, explode(':', _SVN_UPDATE_AUTEURS))) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	debut_gauche();
	debut_droite();

	$dirs = explode(':', _SVN_UPDATE_DIRS);
	$dirs_ok = array();
	foreach ($dirs as $dir) {
		if (is_dir($dir.'.svn') AND is_writeable($dir.'.svn'))
			$dirs_ok[] = $dir;
	}

	if ($dirs_ok) {

		// Appliquer la demande
		if ($dir_svn = _request('dir_svn')
		AND in_array($dir_svn, $dirs_ok)) {
			echo "Update $dir_svn:<br />\n";
			
			$retour = update_svn(_DIR_RACINE.$dir_svn);
			if (!$retour)
				$retour = "Erreur SVN";
			
			echo "<tt>$retour</tt><hr />\n";
		}


		// Menu

		echo _L("Choisir le r&eacute;pertoire &agrave; mettre &agrave; jour&nbsp;: ");
		echo "<form action='./?exec=$exec' method='post'>
		<input type='hidden' name='exec' value='$exec' />
		<select name='dir_svn'>";
		foreach ($dirs_ok as $dir) {
			echo "<option value='$dir'>".$dir."</option>\n";
		}
		echo "</select>
		<input type='submit' value='Update' />
		</form>
		";

	} else
		echo _L("D&eacute;sol&eacute;, aucun r&eacute;pertoire n'est accessible en SVN.");


	fin_page();

}

?>