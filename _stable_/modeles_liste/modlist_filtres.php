<?php
/*
 * Modeles liste
 *
 * Auteur :
 * Cedric MORIN
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */

	function modlist_liste_modeles(){
		$dir_modeles=array();
		$liste_modeles=array();
		$maxfiles = 100;
		
		// Parcourir le chemin
		foreach (creer_chemin() as $dir)
			if (@is_dir($f = $dir."modeles/"))
				$dir_modeles[] = $f;
				
		// retrouver chaque modele
		// en tenant compte des modeles masques par une surcharge prioritaire ...
		foreach($dir_modeles as $dir){
			if (@is_dir($dir) AND is_readable($dir) AND $d = @opendir($dir)) {
				while (($f = readdir($d)) !== false && (count($liste_modeles)<$maxfiles)) {
					if ($f[0] != '.' # ignorer . .. .svn etc
						AND $f != 'CVS'
						AND $f != 'remove.txt'
						AND is_readable("$dir$f")
						AND is_file("$dir$f"))
					if (!isset($liste_modeles[$f])) {
						$liste_modeles[$f] = "$dir$f";
					}
				}
			}
		}
		return $liste_modeles;
	}
	function documente_modele($nom,$chemin){
		
		$s = "";
		
		// ouvrir et lire le modele
		$contenu = "";
		lire_fichier ($chemin, &$contenu);
		if (preg_match(",^\[\(#REM\)([^\]]*)\],Us",$contenu,$regs)){
			$doc = $regs[1];
			if (preg_match(",<template>(.*)</template>,Uims",$doc,$regs2)){
				$template = texte_backend($regs2[1]);
				$doc = str_replace($regs2[0],"",$doc);
			}
			else{
				// fabriquer un template par defaut
				$t=preg_replace(",\.html$,i","",$nom);
				$t = explode("_",$t);
				$template = "&lt;".array_shift($t)."2";
				if (count($t)) $template .= "|".implode("_",$t);
				$template .= "&gt;";
			}
			$template = "<span
	ondblclick='barre_inserer(\"$template\", document.formulaire.texte);'
	title=\""._T("double_clic_inserer_doc")."\">$template</span>";

			$s .= bouton_block_invisible($nom).$template;
			$s .= debut_block_invisible($nom).texte_backend($doc).fin_block();
		}
	
		return $s; 
	}
	
	function affiche_boite_modeles($liste){
		$s .= "\n<p>";
		$s .= debut_cadre_relief("vignette-24.png", true);
	
		$s .= "<div style='padding: 2px; background-color: $couleur_claire; text-align: center; color: black;'>";
		$s .= bouton_block_invisible("ajouter_modele");
		$s .= "<strong class='verdana3' style='text-transform: uppercase;'>"
			._T("modlist:article_inserer_un_modele")."</strong>";
		$s .= "</div>\n";
	
		$s .= debut_block_invisible("ajouter_modele");
		$s .= "<div class='verdana2'>";
		$s .= _T("modlist:article_inserer_un_modele_detail");
		$s .= "</div>";
		
		foreach($liste as $nom=>$doc){
			$s.="<div>$doc</div>";
		}

		$s .= fin_block();
	
		$s .= fin_cadre_relief(true);
		return $s;
	}

	function modlist_affiche_droite($flux){
		if (in_array($flux['args']['exec'],array('articles_edit','breves_edit','rubriques_edit','mots_edit'))){
			
			$liste_modeles = modlist_liste_modeles();
			//$liste_modeles = find_all_in_path("modeles/","\.html$"); // valable en 1.9.2 uniquement
			foreach ($liste_modeles as $nom=>$chemin){
				$doc = documente_modele($nom,$chemin);
				if (strlen($doc))
					$liste_modeles[$nom] = $doc;
				else 
					unset($liste_modeles[$nom]);
			}
			$flux['data'] .= affiche_boite_modeles($liste_modeles);
		
		}
		return $flux;
	}

?>