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
 * Prend les fichiers de langue de référence de salvatore/modules/ et les traite completement un a un
 * pour les modules decrits dans le fichier traductions/traductions.txt
 *
 */


use Spip\Cli\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;


class SalvatoreOuvrager extends Command {
	protected function configure(){
		$this
			->setName('salvatore:ouvrager')
			->setDescription('Prend les fichiers de langue de référence de salvatore/modules/ et fait un traitement complet tirer/lire/ecrire/pousser de chaque module')
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
				'Un ou plusieurs modules à traiter (par defaut tous les modules du fichier de traduction seront traités)',
				null
			)
			->addOption(
				'force',
				null,
				InputOption::VALUE_NONE,
				'Forcer la relecture du ou des modules et la mise a jour en base indépendament de la date de dernière mise a jour des fichiers',
				null
			)
			->addOption(
				'message',
				null,
				InputOption::VALUE_REQUIRED,
				'Message de commit',
				null
			)
			->addOption(
				'time',
				null,
				InputOption::VALUE_NONE,
				'Ajouter date/heure sur les sorties pour les logs',
				null
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		global $spip_racine;
		global $spip_loaded;

		include_spip('inc/salvatore');
		include_spip('salvatore/tireur');
		include_spip('salvatore/lecteur');
		include_spip('salvatore/ecriveur');
		include_spip('salvatore/pousseur');

		$time = $input->getOption('time');
		salvatore_init(array($output, 'writeln'), !!$time);

		$output->writeln("<comment>=======================================</comment>");
		$output->writeln("<comment>OUVRAGER [Traiter complètement les fichiers de reference de salvatore/modules/]</comment>");
		$output->writeln("<comment>=======================================</comment>");


		$traductions = $input->getOption('traductions');
		$liste_trad = salvatore_charger_fichier_traductions($traductions);
		$n = count($liste_trad);
		$output->writeln("<info>$n modules dans le fichier traductions " . ($traductions ? $traductions : '') . "</info>");

		$modules = $input->getOption('module');
		if ($modules = trim($modules)) {
			if ($modules === 'bon_a_pousser') {
				$modules = sql_allfetsel('DISTINCT module', 'spip_tradlang_modules', 'bon_a_pousser>0');
				$modules = array_column($modules, 'module');
			}
			$liste_trad = salvatore_filtrer_liste_traductions($liste_trad, $modules);
			$n = count($liste_trad);
			$output->writeln("<info>$n modules à traiter : " . $modules . "</info>");
		}

		$force = $input->getOption('force');
		$message = $input->getOption('message');

		foreach ($liste_trad as $une_trad) {
			salvatore_log("\n<comment>--- Module " . $une_trad['module'] . " | " . $une_trad['dir_module'] . " | " . $une_trad['url']."</comment>");
			salvatore_log("<info>TIRER</info>");
			salvatore_tirer([$une_trad]);
			salvatore_log("<info>LIRE</info>");
			salvatore_lire([$une_trad], $force);
			salvatore_log("<info>ECRIRE</info>");
			salvatore_ecrire([$une_trad], $message ? $message : '');
			salvatore_log("<info>POUSSER</info>");
			salvatore_pousser([$une_trad]);
		}
	}
}

