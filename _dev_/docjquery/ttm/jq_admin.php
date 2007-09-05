<?php

function traitement_jq_admin(&$ENV) {
	global $tablejq, $langRef;

	if($GLOBALS['auteur_session']['statut']!='0minirezo') {
		$ENV['message']= "PB : acces non autorise";
		return;
	}

	switch($ENV['quoi']) {
	case null: return;

	case 'new':
		$lang= $ENV['lang'];

		include_spip("base/abstract_sql");

		$query="INSERT
		  INTO $tablejq(reference, nom, params, nbparams, lang, etat, modif, xml)
		SELECT id, nom, params, nbparams, '$lang', 'new', NOW(), xml
		  FROM $tablejq
		 WHERE lang='$langRef'";
		spip_query($query);

		$ENV['message']= "LANGUE $lang AJOUTEE\n";
		break;

	case 'update':
		$xml = simplexml_load_file($ENV['xmlref']);
		if(!$xml) {
			$ENV['message']= "Erreur lecture fichier\n";
			return;
		}

		include_spip("base/abstract_sql");

		$version= (string)$xml['version'];
		$ajouts= $modifs= $supprs= 0;
		
		$ENV['message']="Mise a jour vers la version $version<br/>\n";

		// lister les langues destination
		$langues= array();
		$query="SELECT DISTINCT lang FROM $tablejq WHERE lang!='$langRef'";
		$r= spip_query($query);
		if (!$r) {
			die("echec d'acces a la bdd (".mysql_error($r).")\n");
		}
		while($l=spip_fetch_array($r)) {
			$langues[]=$l['lang'];
		}

		// et reperer l'heure debut
		$r= spip_query('SELECT NOW() AS n');
		$l=spip_fetch_array($r);
		$debut=$l['n'];

		foreach($xml->method as $m) {
			if($m['private']) continue;

			$name= mysql_real_escape_string($m['name']);
			$nb=0; $params= array();
			foreach($m->params as $p) {
				$nb++;
				$params[]= $p['type'].' '.$p['name'];
			}
			$params= mysql_real_escape_string(join(', ', $params));
			$mXml= $m->asXML();

			$signature= "$name ( $params )";

			// chercher un bloc de meme signature
			$query= "SELECT id, xml
		  FROM $tablejq
		 WHERE lang='$langRef'
		   AND nom='$name'
		   AND nbparams=$nb
		   AND params='$params'";
			$r= spip_query($query);
			$num= spip_num_rows($r);

			// trouve => passe a l'etat OK ou MOD selon le contenu
			if($num==1) {
				$bdd=spip_fetch_array($r);
				if($mXml==$bdd['xml']) {
					$query="UPDATE $tablejq SET etat='ok', modif=NOW() WHERE id=".$bdd['id'];
					spip_query($query);
				} else {
					$modifs++;
					$ENV['message'].= "MODIF de $signature\n";
					$query="UPDATE $tablejq
		   SET etat='mod', modif=NOW(), xml='".mysql_real_escape_string($mXml)."'
		 WHERE id=".$bdd['id'];
					spip_query($query);
					if(!empty($langues)) {
						$query="UPDATE $tablejq SET etat='new' WHERE reference=".$bdd['id'];
						spip_query($query);
					}
				}
			} elseif($num==0) {
				$ajouts++;
				$ENV['message'].= "AJOUT de $signature\n";
				$id= spip_mysql_insert($tablejq,
					"(nom, params, nbparams, lang, etat, modif, xml)",
					"('".$name."', '$params', $nb, '$langRef', 'new', now(), '".mysql_real_escape_string($mXml)."')");
				foreach($langues as $l) {
					spip_mysql_insert($tablejq,
						"(reference, nom, params, nbparams, lang, etat, modif, xml)",
						"($id, '".$name."', '$params', $nb, '$l', 'new', now(), '".mysql_real_escape_string($mXml)."')");
				}
			} else {
				die("Incoherence en bdd sur $signature\n");
			}
		}

		// a la fin, tous les enregistrements plus vieux que
		// $debut sont des blocs supprimes
		$query="SELECT id, nom, params
		  FROM $tablejq
		 WHERE lang='$langRef'
		   AND etat!='sup'
		   AND modif<'$debut'";
		$r=spip_query($query);

		// passer à 'sup' chacun d'eux et ses trad
		while($l=spip_fetch_array($r)) {
			$signature= $l['nom']." ( ".$l['params']." )";
			$id=$l['id'];
			$supprs++;
			$ENV['message'].= "SUPPR de $signature\n";
			// ce qui est dommage, c'est qu'on perd l'etat des trad, alors qu'en
			// cas de renommage, on peut vouloir récupérer leur contenu.
			$query="UPDATE $tablejq
		    SET etat='sup'
		 WHERE (id=$id OR reference=$id)";
			spip_query($query);
		}

		$ENV['message'].= "FIN : $ajouts ajouts, $modifs modifs et $supprs suppressions\n";
		include_spip('inc/meta');
		ecrire_meta('docjq', serialize(array('version' => $version)));
		ecrire_metas();
		break;

	default:
		$ENV['message']= 'action invalide';
		break;
	}
}
?>
