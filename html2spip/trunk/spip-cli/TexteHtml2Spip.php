<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TexteHtml2Spip extends Command {
    protected function configure() {
        $this
            ->setName('texte:html2spip')
            ->setDescription('Converti du texte en HTML vers des raccourcis SPIP via la librairie html2spip')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        global $spip_racine;
        global $spip_loaded;

        if ($spip_loaded) {
            chdir($spip_racine);

            $contenu = stream_get_contents(STDIN);

            $html2spip = charger_fonction('html2spip', 'inc/');
            $output->write($html2spip($contenu));

        }
        else{
            $output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
        }
    }
}
