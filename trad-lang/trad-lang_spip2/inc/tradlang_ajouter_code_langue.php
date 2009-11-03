<?php
    function inc_tradlang_ajouter_code_langue($module,$lang){
		/**
		 * Insertion des chaines de la langue mère avec le statut NEW
		 */
		$nom_mode = sql_quote($module['nom_mod']);
		spip_log($nom_mode);
		$chaines_mere = sql_select('*','spip_tradlang',"module=$nom_mode AND orig=1");
		while($chaine = sql_fetch($chaines_mere)){
			spip_log($chaine['id']);
			$res = sql_insertq('spip_tradlang',array(
					'id' => $chaine['id'],
					'module' => $module["nom_mod"],
					'str' => $chaine['str'],
					'lang' => $lang,
					'status' => 'NEW',
					'md5' => md5($chaine['str']),
					'orig' => 0
				));
		}
		
		/**
		 * On génère le fichier correspondant
		 */
    	$fichier = $module['dir_lang'].'/'.$module['nom_mod'].'_'.$lang.'.php';
		spip_log($fichier);
		if ($fd = fopen($fichier, "a")){
			fclose($fd);
			$sauvegarde = charger_fonction('tradlang_sauvegarde_module','inc');
			$sauvegarde($module,$lang);
		}else{
			echo 'peu pas ecrire';
		}
	}
?>