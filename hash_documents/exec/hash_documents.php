<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_hash_documents_dist($class = null)
{
	include_spip('hash_fonctions');

	
	//
	// affichages
	//
	include_spip("inc/presentation");

	pipeline('exec_init',array('args'=>array('exec'=>'hash_documents'),'data'=>''));

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('hasher:titre'), 'hash_documents');
	echo "<br /><br /><br />\n";

	echo gros_titre(_T('hasher:titre'), '', false);
	
	// colonne gauche
	echo debut_gauche('', true);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'hash_documents'),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'hash_documents'),'data'=>''));

	echo debut_droite("", true);

	echo debut_cadre_trait_couleur('', true, '', _T('hasher:documents_site'));
	
	$corriger = intval(_request('corriger')) ;

	if (($hasher = intval(_request('hasher'))) > 0)
		$modif = hasher_deplacer_n_documents($hasher, $corriger);

	if (($hasher = intval(_request('hasher'))) < 0)
		$modif = hasher_deplacer_n_documents(-$hasher, $corriger, true);

	if ($modif) {
		echo "<p>"._T('hasher:documents_modifies').join(', ', $modif)."</p>";
	}

	// centre de la page
	list($oui, $non) = hasher_compter_documents();
	echo _T('hasher:bilan',array('oui'=>$oui,'non'=>$non)) ;

	if (intval($non) > 0) {
		$n = min(intval($non), 100);
		echo "<p><a href='".parametre_url(parametre_url(self(), 'hasher', $n), 'corriger', '')."'>"._T('hasher:action_hasher',array('n'=>$n))."</a></p>";
		echo "<p>"._T('hasher:action_corriger_explication')."<a href='".parametre_url(parametre_url(self(), 'hasher', $n), 'corriger', '1')."'>"._T('hasher:action_corriger',array('n'=>$n))."</a></p>";
	}

	if (intval($oui) > 0) {
		$n = min(intval($oui), 100);
		echo "<p><a href='".parametre_url(self(), 'hasher', -$n)."'>"._T('hasher:action_dehasher',array('n'=>$n))."</a></p>";
	}

	echo fin_cadre_trait_couleur(true);


	echo "<br /><br />\n";

	echo debut_cadre_trait_couleur('', true, '', _T('hasher:redirections'));
	$htaccess = _DIR_IMG.'.htaccess';
	if (!lire_fichier($htaccess, $contenu)
	OR !preg_match(',hash_404,', $contenu)) {
		echo "<p>"._T('hasher:htaccess_a_installer',array('htaccess'=>$htaccess))."</p>";
	} else {
		echo "<p>"._T('hasher:htaccess_installe',array('htaccess'=>$htaccess))."</p>";
	}
	echo propre('<cadre>
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule .* ../index.php?action=hash_404 [L]
	</cadre>');

	echo fin_cadre_trait_couleur(true);


	// pied
	echo fin_gauche() . fin_page();
}

?>
