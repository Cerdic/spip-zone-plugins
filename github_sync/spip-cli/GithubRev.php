<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GithubRev extends Command {
	protected function configure() {
		$this
			->setName('github:revision')
			->setDescription("\tCommit de fichiers de contenu sur Github (svn status, up, add, commit etc…)\n\t\t\t\tInitialiser avec un dépot Github avec la commande : spip rev -g https://github.com/xx/xx.git\n")
			->setAliases(array(
				'rev' // abbréviation commune pour "révision"
			))
			->addOption(
				'dest',
				'd',
				InputOption::VALUE_OPTIONAL,
				'Répertoire de destination',
				'conversion_spip'
			)
			->addOption(
				'depot',
				'g',
				InputOption::VALUE_OPTIONAL,
				'Dépot GIT ou synchroniser les conversions',
				''
			)			
			->addOption(
				'action',
				'a',
				InputOption::VALUE_OPTIONAL,
				'Actions possibles : up, status, commit, diff, ...',
				''
			)			

		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		global $spip_racine;
		global $spip_loaded;

		include_spip("iterateur/data");

		$depot = $input->getOption('depot') ;
		
		// Répertoire de destination ou l'on effectue le svn checkout (/convertion_spip par défaut).
		$dest = $input->getOption('dest') ;
		if(!is_dir($dest)) mkdir($dest);
				
		if ($spip_loaded) {
			
			chdir($spip_racine);
			
			exec('/usr/local/bin/svn info ' . $dest , $r);
			
			// var_dump($dest, $r);
			
			// vérifions si on a un depot GIT 				
			if($r[0] == "Path: $dest"){
				$c = inc_ls_to_array_dist($dest . "/*/collections") ;
				$collections = $c[0]['dirname'] . "/" . $c[0]['basename'] ;
				$dest = "$collections" ;
				$output->writeln("<info>Dépot Git OK : $dest</info>");
			}else{ // pas de dépot GIT, on checkout
				if($depot){
					$output->writeln("<error>Checkout du dépot $depot dans $dest</error>");
					passthru("/usr/local/bin/svn co $depot $dest");
					$output->writeln("<info>Relancez la commande.</info>");
					die() ;
				}else{
					$output->writeln("<info>Relancez la commande avec l'option : spip rev -g https://github.com/xx/xx.git</info>");
					die() ;
				}	
			}

			if (!function_exists('passthru')){
				$output->writeln("<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>");
			}
			/* Si c'est bon on continue */
			else{
				
				chdir($dest);
				
				$output->writeln(
					array(
					"<info>C'est parti pour une vérif de commit.</info>"
				));

				// ou en est-on dans les commit ?
				exec('/usr/local/bin/svn up', $results, $err);
									
				if ($err) {
					$output->writeln(array("<error>Erreur SVN.</error>"));
				} else {
					$output->writeln(array(
							'<info>Update</info>',
							 join("\n", $results)
					));
				}
				
				$results = array();

				// Quelques vérifs en svn status.
				exec('/usr/local/bin/svn status .', $results, $err);
									
				if ($err) {
					$output->writeln(array("<error>Erreur SVN.</error>"));
				} else {						

					// Pas de modification de fichier notable => RAS, on quitte.	
					if(sizeof($results) == 0){
						$output->writeln(array(
							 '<info>Status : RAS</info>'
						));	
						die();
					}

					// Sinon détaillons le status
					$output->writeln(array(
							'<info>Status</info>',
							 join("\n", $results)
					));

					// Ajouts ?
					$results_n = array_filter($results, function ($line) {
						return preg_match(',^\?,', $line);
					});

					if (count($results_n) > 0) {
						
						/* nettoyage */
						foreach($results_n as $a){
							$a = preg_replace("/^\?\s*/", "", $a) ;
							
							if(is_dir($a))
								$dossiers_ajoutes[] = $a ;
							else
								$fichiers_ajoutes[] = $a ;
						}

						if(sizeof($dossiers_ajoutes) > 0){
							$output->writeln(array(
								"<info>Svn add des dossiers</info>",
								 join("\n", $dossiers_ajoutes)
							));
							
							foreach($dossiers_ajoutes as $dos){
								exec("/usr/local/bin/svn add " . $dos, $results, $err);
								if ($err) {
									$output->writeln(array("<error>Erreur SVN add $dos</error>"));
								} else {
									$output->writeln(array(
										"<info>Svn add $dos</info>"
									));
								}
							}	
						}
						if(sizeof($fichiers_ajoutes) > 0){

							$output->writeln(array(
								"<info>Svn add des fichiers</info>",
								 join("\n", $fichiers_ajoutes)
							));
							exec("/usr/local/bin/svn add " . join(" , ", $fichiers_ajoutes), $results, $err);
							if ($err) {
								$output->writeln(array("<error>Erreur SVN add fichiers.</error>"));
							} else {
								$output->writeln(array(
									"<info>Svn add </info>",
									 join(",", $fichiers_ajoutes)
								));
							}
						}
						
						$output->writeln(array(
							 "<info>Relancez la commande.</info>"
						));
						die();
					}
											
					// Modif ?
					$results_m = array_filter($results, function ($line) {
						return preg_match(',^M,', $line);
					});

					// Ajout ?
					$results_a = array_filter($results, function ($line) {
						return preg_match(',^A,', $line);
					});

					// Suppression ?
					$results_d = array_filter($results, function ($line) {
						return preg_match(',^D,', $line);
					});


					if (count($results_m) > 0 OR count($results_a) > 0 OR count($results_d) > 0) {
						
						if($input->getOption('action') != "commit"){
							$output->writeln(array(
								"<error>Des fichiers ont été modifié localement.</error>\n<info> Pour commiter : spip rev -a commit</info>"
							));
							die();
						}else{
							$output->writeln(array(
								"<info>Commit…</info>"
							));

							exec('/usr/local/bin/svn commit -m "Ajouts de fichiers"', $results, $err);
							if ($err) 
								$output->writeln(array("<error>Erreur SVN.</error>"));
							
						}
					}	
				}
			}

		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}
