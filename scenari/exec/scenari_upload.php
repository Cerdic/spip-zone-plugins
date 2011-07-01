<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	include_spip('inc/presentation');

	function exec_scenari_upload_dist(){

		// si pas autorise : message d'erreur
		if (!autoriser('voir', 'scenari_upload')) {
			include_spip('inc/minipres');
			print minipres();
			exit;
		}

		// pipeline d'initialisation
		pipeline('exec_init', array('args'=>array('exec'=>'scenari_upload'),'data'=>''));

		//Traitement de l'upload
		$result='';
		if(!isset($_POST['scenari_name'])||!strlen(trim($_POST['scenari_name']))){
			$destination='';
			$error=true;
		} else {
			$destination=trim($_POST['scenari_name']);
			$error=false;
		}

		if(!$error&&!$_FILES['scenari_zip']['error']&&$_FILES['scenari_zip']['type']=='application/zip'){
			$error=false;
			$source=$_FILES['scenari_zip']['tmp_name'];
		}	else {
			$error=true;
			$source='';
		}

		if($error){
			$result .= "<p class='error'>"._T('scenari:uploadfail')."</p>";
		}else{
			$result .= "<p class='success'>"._T('scenari:uploadok')."</p>";
			if (!is_dir(_DIR_IMG.'scenari/'.$destination)) mkdir(_DIR_IMG.'scenari/'.$destination);
			// dézippe le fichier
			$zip = new ZipArchive;
			$res = $zip->open($source);
			if ($res === TRUE) {
				$zip->extractTo(_DIR_IMG.'scenari/'.$destination);
				$zip->close();
				#$result .= "<p class='success'>"._T('scenari:extractok')." <a href=\""._DIR_IMG."scenari/".$destination."\" target='_blank'>IMG/scenari/".$destination."</a></p>";
				unlink($source);
			} else {
				$result .= "<p class='error'>"._T('scenari:extractfail')."</p>";
				// Efface le répertoire si ça foire
				rmdir(_DIR_IMG.'scenari/'.$destination);
			}
		}

		// entetes
		$commencer_page = charger_fonction('commencer_page', 'inc');

		// titre, partie, sous_partie (pour le menu)
		print $commencer_page(_T('scenari:scenari'), "editer", "editer");

		// titre
		print gros_titre(_T('scenari:titre2'),'', false);

		// colonne gauche
		print debut_gauche('', true);
		print pipeline('affiche_gauche', array('args'=>array('exec'=>'scenari_upload'),'data'=>''));

		// colonne droite
		print creer_colonne_droite('', true);
		print pipeline('affiche_droite', array('args'=>array('exec'=>'scenari_upload'),'data'=>''));
		include("scenari_form.php");

		// centre
		print debut_droite('', true);

		// contenu
		print $result;
		include("scenari_list.php");

		// fin contenu
		print pipeline('affiche_milieu', array('args'=>array('exec'=>'scenari_upload'),'data'=>''));
		echo fin_gauche(), fin_page();

	}

?>
