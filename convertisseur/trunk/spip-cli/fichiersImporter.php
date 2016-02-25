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

class fichiersImporter extends Command {
	protected function configure() {
		$this
			->setName('fichiers:importer')
			->setDescription('Importer des fichiers texte SPIP dans spip_articles.')
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
				'1'
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
		if(!is_dir($source))
			$output->writeln("<error>Préciser le répertoire avec les fichiers à importer. spip import -s repertoire </error>");

		
		if ($spip_loaded) {
			chdir($spip_racine);

			if (!function_exists('passthru')){
				$output->writeln("<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>");
			}
			// Si c'est bon on continue
			else{
				$output->writeln("<info>C'est parti pour un petit import de '$source/' dans la rubrique $id_rubrique...</info>");
					
				$fichiers = preg_files($source . "/", "(?:(?<!\.metadata\.)txt$)");
				$output->writeln("\n<info>" . sizeof($fichiers) . " fichiers à importer dans $source/</info>");

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
						$output->writeln("<info>Creation de la rubrique $annee / $numero => $id_rubrique</info>");						
					}
					// inserer l'article
					include_spip("inc/convertisseur");
					$GLOBALS['auteur_session']['id_auteur'] = 1 ;				
					inserer_conversion($texte, $id_rubrique, $f);
					$output->writeln("<info>Insertion de l'article $f</info>");						
										
				}	

			}
		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}
