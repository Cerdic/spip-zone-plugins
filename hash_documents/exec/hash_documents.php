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
	echo $commencer_page("Hash documents", 'hash_documents');
	echo "<br /><br /><br />\n";

	echo gros_titre(_L('Hash documents'), '', false);
	
	// colonne gauche
	echo debut_gauche('', true);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'hash_documents'),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'hash_documents'),'data'=>''));

	echo debut_droite("", true);

	echo debut_cadre_trait_couleur('', true, '', 'Documents du site');

	if (($hasher = intval(_request('hasher'))) > 0)
		$modif = hasher_deplacer_n_documents($hasher);

	if (($hasher = intval(_request('hasher'))) < 0)
		$modif = hasher_deplacer_n_documents(-$hasher, true);

	if ($modif) {
		echo "<p>Documents modifiés : ".join(', ', $modif)."</p>";
	}

	// centre de la page
	list($oui, $non) = hasher_compter_documents();
	echo "Ce site comporte $oui documents hashés, et $non qui ne le sont pas encore (ou ne peuvent pas l'être).";

	if (intval($non) > 0) {
		$n = min(intval($non), 100);
		echo "<p><a href='".parametre_url(self(), 'hasher', $n)."'>hasher $n documents</a></p>";
	}

	if (intval($oui) > 0) {
		$n = min(intval($oui), 100);
		echo "<p><a href='".parametre_url(self(), 'hasher', -$n)."'>déhasher $n documents</a></p>";
	}

	echo fin_cadre_trait_couleur(true);


	echo "<br /><br />\n";

	echo debut_cadre_trait_couleur('', true, '', 'Redirections');
	$htaccess = _DIR_IMG.'.htaccess';
	if (!lire_fichier($htaccess, $contenu)
	OR !preg_match(',hash_404,', $contenu)) {
		echo "<p>Veuillez installer dans $htaccess un fichier contenant les codes suivants :</p>";
	} else {
		echo "<p>Le fichier $htaccess semble correctement installé ; pour mémoire, il doit contenir les codes suivants :</p>";
	}
	echo propre('<cadre>
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule .* /plugins/hash_documents/hash_404.php [L]
	</cadre>');

	echo fin_cadre_trait_couleur(true);


	// pied
	echo fin_gauche() . fin_page();
}

?>
