<?php

/***

	Convertir des fichiers PDF format JPG avec imagemagick convert.
	Installer Image Magick pour que cela fonctionne. (brew install imagemagick sous Mac)

	// Info bonus, si on veut extraire le texte d'un PDF, on peut utiliser la commande pdftotext de poppler (brew install poppler sous Mac)
	
	// On l'appelle de plusieurs facons
	1) mode un PDF (une ou plusieurs pages) spip pdf2img path/to/fichier.pdf
	2) un pdf multipages avec format de pages particulier en destination : spip pdf2img -d path/to/pdf_%02d.jpg path/to/fichier.pdf
	3) traitements par lot des pdf (une page) d'un repertoire source vers un repertoire dest : spip pdf2img -s path -d path
	
	On peut aussi rogner en haut ou sur les cotés avec l'option shave : -c XxY. (exemples : -c 40x40 ou bien -c x40, ou encore -c 40x)
	
	On obtient des jpg de 1500 px de large en sortie.
	
	// a fusionner avec optimg ??
*/


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertisseurPdf2img extends Command {
	protected function configure() {
		$this
			->setName('convertisseur:pdf2img')
			->setDescription('Conversion d\'un PDF en image(s).')
			->setAliases(array(
				'pdf2img' // abbréviation commune pour ca
			))
			->addArgument(
                'pdf',
                InputArgument::OPTIONAL,
                'PDF à convertir en image(s) de 1500 px de large.',
                ''
            )			
			->addOption(
				'source',
				's',
				InputOption::VALUE_OPTIONAL,
				'Répertoire source avec des PDF dedans pour un traitement par lot',
				''
			)
			->addOption(
				'dest',
				'd',
				InputOption::VALUE_OPTIONAL,
				'Répertoire de destination d\'un traitement par lot exemple -d /path/to/jpg, ou format pour un pdf multipages, exemple -d path/to/pdf_%02d.jpg',
				''
			)
			->addOption(
				'shave',
				'c',
				InputOption::VALUE_OPTIONAL,
				'Rogner en hauteur ou largeur avec -c XxY. (exemples : -c 40x40 ou bien -c x40, ou encore -c 40x)',
				'0'
			)
		;
	}

	// prevoir une option crop du genre : convert -verbose -colorspace RGB -interlace none -density 300 -resize 2000 -background white -alpha remove -crop 1734x2574+0+0 +repage

	protected function execute(InputInterface $input, OutputInterface $output) {
		global $spip_racine;
		global $spip_loaded;
		
		include_spip("iterateur/data");
		
		$source = $input->getOption('source') ;
		$dest = $input->getOption('dest') ;
		$shave = $input->getOption('shave') ;
		$pdf = $input->getArgument('pdf');
		
		// var_dump($shave);
		
		if ($spip_loaded) {
			chdir($spip_racine);

			if (!function_exists('passthru')){
				$output->writeln("<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>");
			}
			// Si c'est bon on continue
			else{
				$output->writeln("<info>C'est parti pour une petite conversion de PDF en images !</info>");
				
				// Répertoire dest, ou arrivent les fichiers txt.
				// attention au cas PDF complet : jpg/2016-08/LMDES_2016-08_%02d.jpg
				if(preg_match('/\.jpg$/', $dest))
					$dirdest = dirname($dest);
				elseif($dest == "" AND $pdf)
						$dirdest = dirname($pdf);
					else
						$dirdest = $dest;	
				
				if(!is_dir($dirdest)){
					$output->writeln("<error>Créer le répertoire $dirdest où exporter les fichiers de $source. spip pdf2img -d `repertoire` </error>");
					exit();
				}	

				//var_dump($dirdest, $pdf, $shave);
				//exit ;

				
				# Conversion d'un pdf  ?
				if($pdf !== ""){
					$output->writeln("<info>conversion d'un $pdf spécifique dans $dirdest/ ($dest)</info>");

					// var_dump('plugins/convertisseur/scripts/pdf2img.sh ' . "$pdf" . ' ' . $dest  . ' ' . $shave);
					// Conversion imagemagick
					passthru('plugins/convertisseur/scripts/pdf2img.sh ' . escapeshellarg($pdf) . ' ' . escapeshellarg($dest) . ' ' . escapeshellarg($shave));

				}else{	
					$fichiers_pdf = preg_files($source . "/", "\.pdf$");
					$output->writeln("<info>" . sizeof($fichiers_pdf) . " PDF(s) à convertir dans $source/</info>");

					foreach($fichiers_pdf as $f){
						// Conversion imagemagick
						passthru('plugins/convertisseur/scripts/pdf2img.sh ' . escapeshellarg($f) . ' ' . escapeshellarg($dest) . ' ' . escapeshellarg($shave));
					}
				}
			}
		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}
