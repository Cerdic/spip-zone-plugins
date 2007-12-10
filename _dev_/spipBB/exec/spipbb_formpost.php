<?php
/*
+-------------------------------------------+
| GAFoSPIP v. 0.5 - 21/08/07 - spip 1.9.2
+-------------------------------------------+
| Gestion Alternative des Forums SPIP
+-------------------------------------------+
| Hugues AROUX - SCOTY @ koakidi.com
+-------------------------------------------+
| popup de rédaction de message
+-------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_spipbb_formpost() {

	# initialiser spipbb
	include_spip('inc/spipbb_init');
	
	# requis de cet exec
	include_spip('spipbb_notifications');
	include_spip("inc/spipbb_inc_formpost");
	include_spip("inc/traiter_imagerie");

	# requis spip
	include_spip("inc/actions");
	
	// reconstruire .. var=val des get et post
	// var :
	// .. Option .. utiliser : $var = _request($var);
	/*
	foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
	foreach($_POST as $k => $v) { $$k=$_POST[$k]; }
*/

	// elements spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee;


	$forum = intval(_request('forum'));
	
	include_spip('inc/headers');
	http_no_cache();
	include_spip('inc/commencer_page');
		echo init_entete(_T('gaf:redige_post').' : '.$forum,'');
		
		echo "<body>\n";

		echo "<a name='haut_page'></a>";
	
		if ($connect_statut != '0minirezo') {
			echo _T('avis_non_acces_page');
			fin_page();
			exit;
		}
		
		if(_request('valid_post')) {
			// enregistrer le post
			enregistre_post_spipbb();
			
			?><script type='text/javascript'> self.close(); </script> <?php
			
		}
		else {
			// affiche formulaire
			echo "<div style='padding:10px;'>";	
			debut_cadre_relief("");
			
			# bouton fermer popup
			echo "<div style='float:right; padding:2px;'>\n";
			icone(_T('gaf:icone_ferme'), "javascript:window.close();", _DIR_IMG_SPIPBB."gaf_post.gif", "supprimer.gif");
			echo "</div><br>\n";
	
			affiche_form_post();
			
			fin_cadre_relief();
			echo "</div>\n";
		}
	
	
	//
	echo "\n</body>\n</html>";
}
?>
