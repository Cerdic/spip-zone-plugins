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
 * Prend les fichiers de langue de référence de salvatore/modules/ et met à jour la base de données
 * pour les modules decrits dans le fichier traductions/traductions.txt
 *
 */


use Spip\Cli\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;


class SalvatoreLire extends Command {
	protected function configure(){
		$this
			->setName('salvatore:lire')
			->setDescription('Prend les fichiers de langue de référence de salvatore/modules/ et met à jour la base de données')
			->addOption(
				'traductions',
				null,
				InputOption::VALUE_REQUIRED,
				'Chemin vers le fichier traductions.txt a utiliser [salvatore/traductions/traductions.txt]',
				null
			)
			->addOption(
				'module',
				null,
				InputOption::VALUE_REQUIRED,
				'Un ou plusieurs modules a traiter (par defaut tous les modules du fichier de traduction seront traites)',
				null
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		global $spip_racine;
		global $spip_loaded;

		include_spip('inc/salvatore');
		include_spip('inc/salvatore_lecteur');

		salvatore_init(array($output, 'writeln'));


		$output->writeln("<comment>=======================================</comment>");
		$output->writeln("<comment>LECTEUR [Prend les fichiers de reference de salvatore/modules/ et met a jour la base de donnees]</comment>");
		$output->writeln("<comment>=======================================</comment>");


		$traductions = $input->getOption('traductions');
		$liste_trad = salvatore_charger_fichier_traductions($traductions);
		$n = count($liste_trad);
		$output->writeln("<info>$n modules dans le fichier traductions " . ($traductions ? $traductions : '') . "</info>");

		$modules = $input->getOption('module');
		if ($modules = trim($modules)) {
			$liste_trad = salvatore_filtrer_liste_traductions($liste_trad, $modules);
			$n = count($liste_trad);
			$output->writeln("<info>$n modules à traiter : " . $modules . "</info>");
		}

		salvatore_lire($liste_trad);
	}
}

