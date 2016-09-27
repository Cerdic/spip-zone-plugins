<?php

/***

Exporter la table spip_articles en format txt

Lancer la commande spip-cli : spip export -d `repertoire destination`

Les fichiers txts sont placés dans le repertoire `repertoire destination` sur le disque dur.

Si un repertoire git est trouvé dans /dest alors on prend le repertoire. todo

Voir aussi fichiersImporter.

*/

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class fichiersExporter extends Command {
	protected function configure() {
		$this
			->setName('convertisseur:exporter')
			->setDescription('Exporter la table spip_articles (ou autre) au format SPIP txt.')
			->setAliases(array(
				'export'
			))
			->addOption(
				'source',
				's',
				InputOption::VALUE_OPTIONAL,
				'Table à exporter',
				'spip_articles'
			)
			->addOption(
				'dest',
				'd',
				InputOption::VALUE_OPTIONAL,
				'Répertoire où exporter au format texte',
				'spip_articles'
			)
			->addOption(
				'branche',
				'b',
				InputOption::VALUE_OPTIONAL,
				'branche à exporter (id_secteur ou id_rubrique)',
				'0'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		global $spip_racine;
		global $spip_loaded;
		global $spip_version_branche ;		
	
		include_spip("iterateur/data");
		
		$source = $input->getOption('source') ;
		$dest = $input->getOption('dest') ;	
		$branche = $input->getOption('branche') ;

		// Secteur ou rubrique à exporter.
		if(!$branche OR !intval($branche)){
			$output->writeln("<error>Préciser l'id du secteur ou de la rubrique à exporter. spip export -b 123 </error>");
			exit();
		}	

		
		// demande t'on un secteur ou une rubrique ?
		$parent = sql_getfetsel("id_parent", "spip_rubriques", "id_rubrique=$branche");
		
		if($parent == 0)
			$critere_export = "where id_secteur=" . intval($branche) ;		
		else
			$critere_export = "where id_rubrique=" . intval($branche) ;
		
		// Répertoire dest, ou arrivent les fichiers txt.
		if(!is_dir($dest)){
			$output->writeln("<error>Préciser le répertoire où exporter les fichiers de $source au format txt. spip export -d `repertoire` </error>");
			exit();
		}	

		
		if ($spip_loaded) {
			chdir($spip_racine);

			if (!function_exists('passthru')){
				$output->writeln("<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>");
			}
			// Si c'est bon on continue
			else{

				// chopper les articles en sql.
				$query = sql_query("select * from spip_articles $critere_export order by date_redac asc"); 

				// start and displays the progress bar
				$progress = new ProgressBar($output, sql_count($query));
				$progress->setBarWidth(100);
				$progress->setRedrawFrequency(1);
				$progress->setMessage(" Export de `spip_articles` en cours dans $dest ... ", 'message');
				$progress->start();

				while($f = sql_fetch($query)){
					
					$id_article = $f['id_article'] ;
					$id_rubrique = $f['id_rubrique'] ;
				
					// Exporter les champs spip_articles
					$fichier = "" ;
					$ins_auteurs = array();
					$ins_mc = array();
					$ins_doc = array();
					$progress->setMessage('', 'motscles');
					$progress->setMessage('', 'docs');
					$progress->setMessage('', 'auteurs');


					foreach($f as $k => $v){
						if($k == "texte" or $v == "" or $v == "0" or $v == "non" or $v == "0000-00-00 00:00:00")
							continue ;
						$fichier .= "<ins class='$k'>" . trim($v) ."</ins>\n" ;
					}
					$fichier .= "\n\n" . $f['texte'] . "\n\n" ;

					// métadonnées (hierarchie, auteurs, mots-clés...)

					// hierarchie
					$titre_rubrique = sql_getfetsel("titre", "spip_rubriques", "id_rubrique=$id_rubrique");
					$id_parent = sql_getfetsel("id_parent", "spip_rubriques", "id_rubrique=$id_rubrique");
					if($id_parent)
						$titre_parent = sql_getfetsel("titre", "spip_rubriques", "id_rubrique=$id_parent");
					
					// auteurs spip 3
					if($spip_version_branche > "3")
						$auteurs = sql_allfetsel("a.nom, a.bio", "spip_auteurs_liens al, spip_auteurs a", "al.id_objet=$id_article and al.objet='article' and al.id_auteur=a.id_auteur");
					else // spip 2
						$auteurs = sql_allfetsel("a.nom, a.bio", "spip_auteurs_articles aa, spip_auteurs a", "aa.id_article=$id_article and aa.id_auteur=a.id_auteur");
					
					foreach($auteurs as $a)
						if($a['nom'])
							$ins_auteurs[] = $a ;
				
					$auteurs = "" ;
					foreach($ins_auteurs as $k => $a){
							if($k == 0)
								$sep = "" ;
							else
								$sep = "@@" ;
							$bio = ($a['bio'] != "") ? "::" . $a['bio'] : "" ;
								
							$auteurs .= $sep . $a['nom'] . $bio ;
					}					
										
					$auteurs_m = substr($auteurs, 0, 100) ;
					$progress->setMessage($auteurs_m, 'auteurs');
					
					// mots-clés
					if($spip_version_branche > "3")
						$motscles = sql_allfetsel("*", "spip_mots_liens ml, spip_mots m", "ml.id_objet=$id_article and ml.objet='article' and ml.id_mot=m.id_mot");
					else // spip 2
						$motscles = sql_allfetsel("*", "spip_mots_articles ma, spip_mots m", "ma.id_article=$id_article and ma.id_mot=m.id_mot");
					
					foreach($motscles as $mc){
						if($mc['titre'])
							$ins_mc[] = $mc['type'] . "::" . $mc['titre'] ;
					}	
					if(is_array($ins_mc)){
						$motscles = join("@@", $ins_mc) ;
						$motscles_m = substr($motscles, 0, 100) ;
						$progress->setMessage($motscles_m, 'motscles');
					}

					// documents joints
					$documents = sql_allfetsel("*", "spip_documents d", "dl.id_objet=$id_article and dl.objet='article' and dl.id_document=d.id_document");
					foreach($documents as $doc)
							$ins_doc[] = json_encode($doc) ;
					if(is_array($ins_doc)){
						$documents = join("@@", $ins_doc) ;
						$docs_m = substr($documents, 0, 100) ;
						$progress->setMessage($docs_m, 'docs');
					}

					// Ajouter les métadonnées
					if($auteurs)
						$fichier = "<ins class='auteurs'>$auteurs</ins>\n" . $fichier ;				
					if($motscles)
						$fichier = "<ins class='mots_cles'>$motscles</ins>\n" . $fichier ;
					if($documents)
						$fichier = "<ins class='documents'>$documents</ins>\n" . $fichier ;
					if($titre_parent && $titre_rubrique)
						$fichier = "<ins class='hierarchie'>$titre_parent@@$titre_rubrique</ins>\n" . $fichier ;
				
					// Créer un fichier txt
					$date = ($f['date_redac'] != "0000-00-00 00:00:00")? $f['date_redac'] : $f['date'] ;
					preg_match("/^(\d\d\d\d)-(\d\d)/", $date, $m);
					$annee = $m[1] ;
					$mois = $m[2] ;

					include_spip("inc/charset");
					$nom_fichier = translitteration($f['titre']) ;
					$nom_fichier = preg_replace("/[^a-zA-Z0-9]/i", "-", $nom_fichier);
					$nom_fichier = preg_replace("/-{2,}/i", "-", $nom_fichier);
					$nom_fichier = preg_replace("/^-/i", "", $nom_fichier);
					$nom_fichier = preg_replace("/-$/i", "", $nom_fichier);
					
					$nom_fichier = "$dest/$annee/$annee-$mois/$annee-$mois"."_$nom_fichier.txt" ;

					// Créer les répertoires
					if(!is_dir("$dest/$annee"))
						mkdir("$dest/$annee");
					if(!is_dir("$dest/$annee/$annee-$mois"))
						mkdir("$dest/$annee/$annee-$mois");	
					
					if(ecrire_fichier("$nom_fichier", $fichier)){
						// Si tout s'est bien passé, on avance la barre
						$nom_fichier_m = substr($nom_fichier, 0, 100) ;
						$progress->setMessage($nom_fichier_m, 'filename');
						$progress->setFormat("<fg=white;bg=blue>%message%</>\n" . '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%' . "\n %auteurs% %motscles% \n %filename% \n\n");
						$progress->advance();
					
					}	
					else{
						$output->writeln("<error>échec de l'export de $nom_fichier</error>");
						exit ;
					}			
				}
				
				// ensure that the progress bar is at 100%
				$progress->finish();

			}
			$output->writeln("\n");
		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}

	
