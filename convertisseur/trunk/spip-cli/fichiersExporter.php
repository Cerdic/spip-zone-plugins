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
			->setName('fichiers:exporter')
			->setDescription('Exporter spip_articles (ou autre) en fichiers texte.')
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
				'branche à exporter (id_secteur)',
				'0'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		global $spip_racine;
		global $spip_loaded;
		
		include_spip("iterateur/data");
		
		$source = $input->getOption('source') ;
		$dest = $input->getOption('dest') ;	
		$id_secteur = $input->getOption('branche') ;
		
		if(intval($id_secteur) > 0)
			$secteur = "where id_secteur=" . intval($id_secteur) ;		
				
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
				$query = sql_query("select * from spip_articles $secteur order by date_redac asc"); 

				// start and displays the progress bar
				$progress = new ProgressBar($output, sql_count($query));
				$progress->setBarWidth(100);
				$progress->setRedrawFrequency(1);
				$progress->setMessage(" Export de `spip_articles` en cours dans $dest ... ", 'message');
				$progress->start();

				while($f = sql_fetch($query)){
				
					$id_article = $f['id_article'] ;
					include_spip("inc/charset");
					
					$nom_fichier = translitteration($f['titre']) ;
					$nom_fichier = preg_replace("/[^a-zA-Z0-9]/i", "-", $nom_fichier);
					$nom_fichier = preg_replace("/-{2,}/i", "-", $nom_fichier);
					$nom_fichier = preg_replace("/^-/i", "", $nom_fichier);
					$nom_fichier = preg_replace("/-$/i", "", $nom_fichier);
					
					$date = ($f['date_redac'] != "0000-00-00 00:00:00")? $f['date_redac'] : $f['date'] ;
					
					preg_match("/^(\d\d\d\d)-(\d\d)/", $date, $m);
					$annee = $m[1] ;
					$mois = $m[2] ;
					
					$fichier = "" ;
					foreach($f as $k => $v){
						if($k == "texte" or $v == "" or $v == "0")
							continue ;
						$fichier .= "<ins class='$k'>$v</ins>\n" ;
					}
					$fichier .= "\n\n" . $f['texte'] . "\n\n" ;
					
					$nom_fichier = "$dest/$annee/$mois/$annee-$mois"."_$nom_fichier.txt" ;

					// créer les répertoires
					if(!is_dir("$dest/$annee"))
						mkdir("$dest/$annee");
					if(!is_dir("$dest/$annee/$mois"))
						mkdir("$dest/$annee/$mois");	
					
					if(ecrire_fichier("$nom_fichier", $fichier)){				
						
						// Si tout s'est bien passé, on avance la barre
						$progress->setMessage($nom_fichier, 'filename');
						$progress->setFormat("\n<fg=white;bg=blue>%message%</>\n" . '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%' . "\n  %filename%\n\n");
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

	
