<?php

/***

Importer en masse des fichiers txt dans spip_articles.

*/


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class fichiersImporter extends Command {
	protected function configure() {
		$this
			->setName('fichiers:importer')
			->setDescription('Importer des fichiers texte SPIP dans spip_articles. `spip import -s %source% -d %id_rubrique%`')
			->setAliases(array(
				'import' // abbréviation commune pour "import"
			))
			->addOption(
				'source',
				's',
				InputOption::VALUE_OPTIONAL,
				'Répertoire source',
				'conversion_spip'
			)
			->addOption(
				'dest',
				'd',
				InputOption::VALUE_OPTIONAL,
				'id_rubrique de la rubrique où importer les numéros et leurs articles',
				'0'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		global $spip_racine;
		global $spip_loaded;
		
		include_spip("iterateur/data");
		
		$source = $input->getOption('source') ;
		$id_parent = $input->getOption('dest') ;		
				
		// Répertoire source, ou arrivent les fichiers Quark (/exports_quark par défaut).
		if(!is_dir($source)){
			$output->writeln("<error>Préciser le répertoire avec les fichiers à importer. spip import -s repertoire </error>\n");
			exit ;
		}	

		if($id_parent == 0){
			$output->writeln("<error>Préciser le secteur cible pour importer. spip import -d `id_secteur` </error>\n");
			exit ;
		}	

		
		if ($spip_loaded) {
			chdir($spip_racine);

			if (!function_exists('passthru')){
				$output->writeln("<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>");
			}
			// Si c'est bon on continue
			else{
								
				$fichiers = preg_files($source . "/", "(?:(?<!\.metadata\.)txt$)", 100000);

				// start and displays the progress bar
				$progress = new ProgressBar($output, sizeof($fichiers));
				$progress->setBarWidth(100);
				$progress->setRedrawFrequency(1);
				$progress->setMessage(" Import de $source/*.txt en cours dans la rubrique $id_parent ... ", 'message');
				$progress->setMessage("", 'inforub');
				$progress->start();

				foreach($fichiers as $f){
					$fichier = 	basename($f) ;
					preg_match("/^(\d{4})-\d{2}/", $fichier, $m);
					$numero = $m[0];
					
					// chopper l'id_parent dans le fichier ?
					include_spip("inc/flock");										
					lire_fichier($f, $texte);
					
					// menage
					//@@COLLECTION:esRetour ligne automatique
					//@@SOURCE:article914237.html
					
					$texte = preg_replace("/@@COLLECTION.*/", "", $texte);
					$texte = preg_replace("/@@SOURCE.*/", "", $texte);
										
					// faut il creer des rubriques ?
					$id_rubrique = sql_getfetsel("id_rubrique", "spip_rubriques", "titre='$numero'");
					
					if(!$id_rubrique){
						include_spip("inc/rubriques");
						$annee = $m[1] ;
						$id_rubrique = creer_rubrique_nommee($annee . "/" . $numero, $id_parent);
						$progress->clear();
						$progress->setMessage(" Création de la rubrique $annee-$numero => $id_rubrique ", 'inforub');
						$progress->display();
											
					}
					// inserer l'article
					include_spip("inc/convertisseur");
					$GLOBALS['auteur_session']['id_auteur'] = 1 ;				
					if(inserer_conversion($texte, $id_rubrique, $f)){
						
						// Si tout s'est bien passé, on avance la barre
						$progress->setMessage($f, 'filename');
						$progress->setFormat("<fg=white;bg=blue>%message%</>\n" . "<fg=white;bg=red>%inforub%</>\n" . '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%' . "\n  %filename%\n\n");
						$progress->advance();
											
					}else{
						$output->writeln("<error>échec de l'import de $f</error>");
						exit ;
					}
				}	

				// ensure that the progress bar is at 100%
				$progress->finish();
			}
		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}
