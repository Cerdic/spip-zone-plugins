<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TexteSale extends Command {
	protected function configure() {
		$this
			->setName('texte:sale')
			->setDescription('Converti du texte en HTML vers des raccourcis SPIP via la fonction "sale"')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		global $spip_racine;
		global $spip_loaded;

		if ($spip_loaded) {
			chdir($spip_racine);

			$contenu = stream_get_contents(STDIN);

			$contenu = htmlspecialchars_decode($contenu);
			include_spip('sale_fonctions');
			$output->write(sale($contenu));

		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}
