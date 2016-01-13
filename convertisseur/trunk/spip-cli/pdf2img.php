<?php

/***

	Convertir des fichiers PDF format JPG avec imagemagick convert.
	Installer Image Magick pour que cela fonctionne. (brew install imagemagick sous Mac)
	
	Mettre un PDF dans conversion_spip/ et lancer la commande spip-cli  : spip pdf2img
	Si votre PDF source est ailleurs ou si vous vouler mettre les images ailleurs, lancer la commande : spip pdf2img -s "path/to/pdf" -d "path/to/dest"

*/


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class pdf2img extends Command {
	protected function configure() {
		$this
			->setName('conversion:pdf2img')
			->setDescription('Conversion d\'un PDF en images')
			->setAliases(array(
				'pdf2img' // abbréviation commune pour ca
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
				
		// Répertoire source ou sont les PDFs
		if(!is_dir($source))
			mkdir($source);
		
		if ($spip_loaded) {
			chdir($spip_racine);

			if (!function_exists('passthru')){
				$output->writeln("<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>");
			}
			// Si c'est bon on continue
			else{
				$output->writeln("<info>C'est parti pour une petite conversion de PDF en images !</info>");
					
				$fichiers_pdf = preg_files($source . "/", "\.pdf$");
				
				$output->writeln("<info>" . sizeof($fichiers_pdf) . " PDF(s) à convertir dans $source/</info>");

				foreach($fichiers_pdf as $f){
					// Conversion imagemagick
					passthru('plugins/convertisseur/scripts/pdf2img.sh ' . $f . ' ' . $dest);
				}
			}
		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}
