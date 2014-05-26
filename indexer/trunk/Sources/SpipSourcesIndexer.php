<?php

namespace Spip\Indexer\Sources;

use \Indexer\Indexer;
use \Indexer\Sources\Sources;

class SpipSourcesIndexer {

    /** @var \Indexer\Indexer */
    private $indexer = null;

    /** @var \Indexer\Sources\Sources */
    private $sources = null;

    /** @var bool Tables de liens présentes (spip 3 ?) */
    private $tables_liens = true;

    /** @var string clé de config */
    private $meta_stats = 'indexer/indexing/stats';

    /**
     *
     *
     * @param \Indexer\Indexer $indexer
     * @param \Indexer\Sources\Sourcesr $sources
    **/
    public function __construct(Indexer $indexer, Sources $sources) {
        $this->indexer = $indexer;
        $this->sources = $sources;
    }


    public function setTablesLiensAuto() {
        include_spip('inc/plugin');
        $liens = spip_version_compare($GLOBALS['spip_version_branche'], '3.0', '>=');
        $this->setTablesLiens($liens);
    }

    public function setTablesLiens($bool) {
        $this->tables_liens = (bool)$bool;
    }



    public function isTimeout() {
        static $timeout = null;

        if (is_null($timeout)) {
            include_spip('base/upgrade');
            if (defined('_TIME_OUT')) {
                $timeout = _TIME_OUT;
            } else {
                $timeout = time() + _UPGRADE_TIME_OUT;
            }
        }

        return time() >= $timeout;
    }

    public function initTimeout() {
        $this->isTimeout(); // le premier lancement initialise les temps
    }


    public function loadIndexesStats() {
        include_spip('inc/config');
        $stats = lire_config($this->meta_stats, []);
        if (!is_array($stats)) {
            $stats = [];
        }
        return $stats + [
            'last' => [
                'sourceClass' => '',
                'source'      => 0,
                'part'        => 0,
                'documents'   => 0,
                'time' => [
                    'documents'   => 0,
                    'indexing'    => 0,
                ],
            ],
            'sources' => [],
        ];
    }

    public function loadIndexesStatsClean() {
        $stats = $this->loadIndexesStats();
        $stats['last']['documents'] = 0;
        $stats['last']['time'] = [
            'documents'   => 0,
            'indexing'    => 0,
        ];
        return $stats;
    }

    public function saveIndexesStats($stats) {
        include_spip('inc/config');
        ecrire_config($this->meta_stats, $stats);
    }

    public function resetIndexesStats() {
        include_spip('inc/config');
        effacer_config($this->meta_stats);
    }


    /**
     * Indexe toutes les sources en prenant en compte le timeout
     */
    public function indexAll() {

        $this->initTimeout();

        $stats = $this->loadIndexesStatsClean();
        $this->resetIndexesStats();

        $sources = $this->sources->getIterator();
        // se replacer à la dernière source renseignée (cas d'une indexation non terminée)
        if ($stats['last']['source']) {
            $sources->seek($infos['last']['source']);
        }

        while ($sources->valid()) {
            $skey    = $sources->key();
            $source  = $sources->current();

            $stats['last']['source'] = $skey;
            $stats['last']['sourceClass'] = (string)$source;

            if (!isset($stats['sources'][$skey])) {
                $stats['sources'][$skey] = [
                    'sourceClass' => (string)$source,
                    'documents' => 0,
                    'time' => [
                        'documents' => 0,
                        'indexing' => 0,
                        'total' => 0
                    ]
                ];
            }

            $this->indexSource($source, $skey, $stats);

            if ($this->isTimeout()) {
                break;
            }

            $sources->next();
        }

        if ($this->isTimeout()) {
            $this->saveIndexesStats($stats);
            return false;
        }

        $this->resetIndexesStats();
        return $stats;
    }



    private function indexSource($source, $skey, &$stats) {

        $source->setTablesLiens($this->tables_liens); // pour SPIP 2.1

        echo "<h2>Analyse de $source :</h2>\n";
        spip_timer('source');

        // on découpe les documents de cette sources en parts d'un certain nombre
        // afin d'éviter un timeout et une surcharge mémoire
        $parts = new \ArrayIterator($source->getParts(1000));

        // on se replace à la dernière part renseignée (cas d'une indexation non terminée)
        if ($stats['last']['part']) {
            $parts->seek($stats['last']['part']);
        }

        while ($parts->valid()) {
            $part = $parts->current();
            $stats['last']['part'] = $parts->key();
            
            // on regarde s'il reste du temps AVANT d'indexer les 1000 suivants
            if ($this->isTimeout()) {
                $t = spip_timer('source', true);
                $stats['sources'][$skey]['time']['total'] += $t;
                return false;
            }
            
            $this->indexSourcePart($source, $skey, $part, $stats);
            $parts->next();
        }

        echo "<hr /><strong>Temps pour $source :</strong><br />";
        $t = spip_timer('source', true);
        $stats['sources'][$skey]['time']['total'] += $t;
        echo $this->getNiceTime( $stats['sources'][$skey]['time']['total'] );
    }



    private function indexSourcePart($source, $skey, $part, &$stats) {

        spip_timer('documents');
        $documents = $source->getDocuments($part['start'], $part['end']);
        $t = spip_timer('documents', true);
        $nb = count($documents);

        $stats['last']['documents'] += $nb;
        $stats['last']['time']['documents'] += $t;

        $stats['sources'][$skey]['documents'] += $nb;
        $stats['sources'][$skey]['time']['documents'] += $t;

        if ($nb) {
            echo "<br /><strong>Temps pour indexer $nb documents (ids $part[start] à $part[end])</strong>\n";
            echo "<br />Documents: " . $this->getNiceTime($t) . "\n";

            spip_timer('indexing');
            $this->indexer->replaceDocuments($documents);
            $t = spip_timer('indexing', true);

            $stats['last']['time']['indexing'] += $t;
            $stats['sources'][$skey]['time']['indexing'] += $t;
            echo "<br />Enregistrement dans l'index: " . $this->getNiceTime($t) . "\n";
        }
    }



    /** Retourne un temps formaté pour une belle lecture */
    public function getNiceTime($p) {
        if ($p < 1000)
            $s = '';
        else {
            $s = sprintf("%d ", $x = floor($p/1000));
            $p -= ($x*1000);
        }
        return $s . sprintf($s?"%07.3f ms":"%.3f ms", $p);
    }
}
