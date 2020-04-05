<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

class PolyhierarchieMigrerMots extends Command {
	protected function configure() {
		$this
			->setName('polyhierarchie:migrer_mots')
			->setDescription('Migrer les mots-clés et groupes vers des rubriques polyhiérarchiques.')
			->addOption(
                'id_secteur',
                '',
                InputOption::VALUE_OPTIONAL,
                'Identifiant du secteur où créer les rubriques.',
                0
            )
            ->addOption(
                'titre_secteur',
                '',
                InputOption::VALUE_OPTIONAL,
                'Nom du secteur racine qui contiendra les rubriques si on veut englober dans un secteur.',
                ''
            )
            ->addOption(
                'supprimer',
                's',
                InputOption::VALUE_NONE,
                'Supprimer les mots et groupes durant la migration.'
            )
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		include_spip('action/editer_objet');
		include_spip('inc/autoriser');
		include_spip('inc/polyhier');
		global $spip_racine;
		global $spip_loaded;
		
		if ($spip_loaded) {
			chdir($spip_racine);
			// Récupérer les options
			$id_secteur = $input->getOption('id_secteur');
			$titre_secteur = $input->getOption('titre_secteur');
			$supprimer = $input->getOption('supprimer');
			
			$nb_a_migrer = 0;
			$rubriques_a_publier = array();
			
			// On va chercher tous les groupes de mots
			if ($groupes = sql_allfetsel('*', 'spip_groupes_mots') and is_array($groupes)) {
				$nb_a_migrer += count($groupes); // tous les groupes
				$nb_a_migrer += sql_countsel('spip_mots'); // tous les mots
				$nb_a_migrer += sql_countsel('spip_mots_liens'); // tous les liens
				
				// On crée si demandé le secteur conteneur
				autoriser_exception('creer', 'rubrique', 0, true);
				if (!$id_secteur and $titre_secteur) {
					$id_secteur = objet_inserer('rubrique', 0, array('titre'=>$titre_secteur));
				}
				autoriser_exception('creer', 'rubrique', $id_secteur, true);
				
				$progress = $this->getHelperSet()->get('progress');
				$progress->setFormat(ProgressHelper::FORMAT_VERBOSE);
				$progress->setBarWidth(100);
				$progress->setRedrawFrequency(10);
				$progress->start($output, $nb_a_migrer);
				
				foreach ($groupes as $groupe) {
					$id_groupe = intval($groupe['id_groupe']);
					
					// On migre ce groupe
					$id_rubrique_groupe = objet_inserer('rubrique', $id_secteur);
					objet_modifier(
						'rubrique',
						$id_rubrique_groupe,
						array(
							'titre' => $groupe['titre'],
							'descriptif' => $groupe['descriptif'],
							'texte' => $groupe['texte']
						)
					);
					$progress->advance();
					
					// On va chercher tous les mots de ce groupe
					if ($mots = sql_allfetsel('*', 'spip_mots', 'id_groupe = '.$id_groupe) and is_array($mots)) {
						autoriser_exception('creer', 'rubrique', $id_rubrique_groupe, true);
						
						foreach ($mots as $mot) {
							$id_mot = intval($mot['id_mot']);
							
							// On migre ce mot dans la rubrique-groupe
							$id_rubrique_mot = objet_inserer('rubrique', $id_rubrique_groupe);
							objet_modifier(
								'rubrique',
								$id_rubrique_mot,
								array(
									'titre' => $mot['titre'],
									'descriptif' => $mot['descriptif'],
									'texte' => $mot['texte']
								)
							);
							$progress->advance();
							
							// On va chercher tous les liens de ce mot
							if ($liens = sql_allfetsel('*', 'spip_mots_liens', 'id_mot = '.$id_mot)) {
								foreach ($liens as $lien) {
									sql_insertq('spip_rubriques_liens', array('id_parent' => $id_rubrique_mot, 'objet' => $lien['objet'], 'id_objet' => $lien['id_objet']));
									$progress->advance();
								}
								$rubriques_a_publier[] = $id_rubrique_mot;
							}
							
							// On supprime le mot et tous ses liens
							if ($supprimer) {
								sql_delete('spip_mots_liens', 'id_mot = '.$id_mot);
								sql_delete('spip_mots', 'id_mot = '.$id_mot);
							}
						}
					}
					
					// On supprime le groupe
					if ($supprimer) {
						sql_delete('spip_groupes_mots', 'id_groupe = '.$id_groupe);
					}
					
					$progress->clear();
					$output->writeln("\nLe groupe <info>« {$groupe['titre']} »</info> a été migré dans la rubrique <info>$id_rubrique_groupe</info>.");
					$progress->display();
				}
				
				// On publie les rubriques qui ont des liens
				if ($nb_publier = count($rubriques_a_publier)) {
					polyhier_calculer_rubriques_if($rubriques_a_publier, array('statut'=>'publie', 'add'=>array(), 'remove'=>array()));
					$output->writeln("\n<info>$nb_publier</info> rubriques publiées.");
				}
				
				$output->writeln("\n<info>Migration des mots en rubriques terminée !</info>");
			}
		}
		else {
            $output->writeln("<comment>Vous n'êtes pas dans un installation de SPIP.</comment>");
        }
	}
}
