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

class SpipImport extends Command {
	protected function configure() {
		$this
			->setName('conversion:spipimport')
			->setDescription('Importe des fichiers d\'import SPIP.')
			->setAliases(array(
				'import' // abbréviation commune pour "synchro"
			))
			->addOption(
				'source',
				's',
				InputOption::VALUE_OPTIONAL,
				'Répertoire source',
				'conversion_spip'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		global $spip_racine;
		global $spip_loaded;
		
		include_spip("iterateur/data");
		
		$source = $input->getOption('source') ;
				
		// Répertoire source, ou arrivent les fichiers Quark (/exports_quark par défaut).
		if(!is_dir($source))
			mkdir($source);
		
		if ($spip_loaded) {
			chdir($spip_racine);

			if (!function_exists('passthru')){
				$output->writeln("<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>");
			}
			// Si c'est bon on continue
			else{
				$output->writeln("<info>C'est parti pour un petit import !</info>");
					
				$fichiers_xml = preg_files($source . "/", "(?:(?<!\.metadata\.)xml$)");
				
				$output->writeln("\n<info>" . sizeof($fichiers_xml) . " fichiers à importer dans $source</info>");

				foreach($fichiers_xml as $f){
					$fichier = 	preg_replace("/$source.*collections\//","",$f);							
					$output->writeln("$fichier");
				}	

			}
		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}
