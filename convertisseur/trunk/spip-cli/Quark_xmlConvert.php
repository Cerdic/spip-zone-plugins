<?php

/***

Convertir des fichiers au format Quark XML en fichiers en format SPIP conversion.

Mettre les fichiers XML dans le repertoire /exports_quark/%COLLECTION%/%NUMERO% du SPIP

Lancer la commande spip-cli : spip convert

Les fichiers convertis sont placés dans le repertoire /conversion_spip/%COLLECTION%/%NUMERO% du SPIP

Si un repertoire git est trouvé dans /dest alors on prend le repertoire */ // /*.git/*/collections comme répertoire dest. 


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Quark_xmlConvert extends Command {
	protected function configure() {
		$this
			->setName('conversion:quarkxmlconvert')
			->setDescription('Convertion des fichiers Quarks XML en fichiers d\'import SPIP.')
			->setAliases(array(
				'convert' // abbréviation commune pour "synchro"
			))
			->addOption(
				'source',
				's',
				InputOption::VALUE_OPTIONAL,
				'Répertoire source',
				'exports_quark'
			)
			->addOption(
				'dest',
				'd',
				InputOption::VALUE_OPTIONAL,
				'Répertoire de destination',
				'conversion_spip'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		global $spip_racine;
		global $spip_loaded;
		
		include_spip("iterateur/data");
		
		$source = $input->getOption('source') ;
		$dest = $input->getOption('dest') ;
				
		// Répertoire source, ou arrivent les fichiers Quark (/exports_quark par défaut).
		if(!is_dir($source))
			mkdir($source);

		// Répertoire de destination ou l'on enregistre les fichiers spip (/convertion_spip par défaut).
		if(!is_dir($dest))
			mkdir($dest);
		
		if ($spip_loaded) {
			chdir($spip_racine);

			if (!function_exists('passthru')){
				$output->writeln("<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>");
			}
			// Si c'est bon on continue
			else{
				$output->writeln("<info>C'est parti pour la convertion des fichiers Quark XML dans /$dest !</info>");


				// trouve t'on un repertoire trunk/collections dans $dest ?
				if($ls_depot = inc_ls_to_array_dist($dest ."/trunk/collections")){
					$dest = $ls_depot[0]['dirname'] . "/" .  $ls_depot[0]['basename'] ;
					$output->writeln("<info>GIT : dest = $dest</info>");
				}
				
				$ls_sources = inc_ls_to_array_dist($source ."/*/");
				
				foreach($ls_sources as $s)
					$sources[] = $s['dirname'] . "/" . $s['basename'] ; 
								
				include_spip("inc/utils");
				
				// chopper des fichiers xml mais pas xxx.metatada.xml
				foreach($sources as $s){
					
					$fichiers_xml = preg_files($s, "(?:(?<!\.metadata\.)xml$)");
					
					// plugin convertisseur
					include_spip("extract/quark_xml");
					
					foreach($fichiers_xml as $f){
						
						$c = preg_match(",.*/(.*)/.*,U", dirname($f), $m);
						$collection = $m[1] ;
						
						$n = preg_match(",.*/.*/(.*)/.*,U", dirname($f), $m);
						$numero = $m[1] ;
						
						if(!is_dir("$dest" . "/"  . $collection)){
							mkdir("$dest" . "/" . $collection) ;
						}
						if(!is_dir("$dest" . "/" . $collection . "/" . $numero)){
							mkdir("$dest" . "/" . $collection . "/" . $numero) ;
						}
						
						$article = basename($f);
											
						$contenu = extracteur_quark_xml($f);
						
						include_spip("inc/flock");
						
						// nettoyer les noms de fichiers
						include_spip("inc/charsets");
						$article = translitteration($article);
						
						$article = preg_replace(',[^\w-]+,', '_', $article);
						$article = preg_replace(',_xml$,', '.xml', $article);

						
						ecrire_fichier("$dest" . "/" . $collection . "/" . $numero . "/" . $article, $contenu);
									
						$output->writeln("Nouvelle conversion : $dest/" . $collection . "/" . $numero . "/" . $article);
					}	

				}
				
			
			}
		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}
