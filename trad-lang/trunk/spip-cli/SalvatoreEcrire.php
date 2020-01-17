<?php

/*
    This file is part of Salvatore, the translation robot of Trad-lang (SPIP)

    Salvatore is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Trad-Lang is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Trad-Lang; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    Copyright 2003-2020
        Florent Jugla <florent.jugla@eledo.com>,
        Philippe Riviere <fil@rezo.net>,
        Chryjs <chryjs!@!free!.!fr>,
        kent1 <kent1@arscenic.info>
        Cerdic <cedric@yterium.com>
*/

/**
 * Ce script va exporter les fichiers de traduction définis dans le fichier traductions/traductions.txt
 * vers sa copie locale a partir de la base de donnees
 *
 */


use Spip\Cli\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;


class SalvatoreEcrire extends Command {
	protected function configure(){
		$this
			->setName('salvatore:ecrire')
			->setDescription('Exporte les fichiers de traduction dans salvatore/modules/ à partir de la base de données')
			->addOption(
				'traductions',
				null,
				InputOption::VALUE_REQUIRED,
				'Chemin vers le fichier traductions.txt a utiliser [salvatore/traductions/traductions.txt]',
				null
			)
			->addOption(
				'message',
				null,
				InputOption::VALUE_REQUIRED,
				'Message de commit',
				null
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		global $spip_racine;
		global $spip_loaded;

		include_spip('inc/salvatore');
		include_spip('inc/salvatore_ecriveur');

		salvatore_init(array($output, 'writeln'));


		$output->writeln("<comment>=======================================</comment>");
		$output->writeln("<comment>ECRIVEUR [Exporte les fichiers de traduction dans sa copie locale a partir de la base de donnees]</comment>");
		$output->writeln("<comment>=======================================</comment>");


		$traductions = $input->getOption('traductions');
		$liste_trad = salvatore_charger_fichier_traductions($traductions);
		$n = count($liste_trad);
		$output->writeln("<info>$n modules dans le fichier traductions " . ($traductions ? $traductions : '') . "</info>");

		$message = $input->getOption('message');

		salvatore_ecrire($liste_trad, null, $message ? $message : '');
	}
}

