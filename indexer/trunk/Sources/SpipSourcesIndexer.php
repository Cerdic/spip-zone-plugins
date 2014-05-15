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


    public function loadIndexesInfos() {
        include_spip('inc/config');
        $data = lire_config('indexer/indexing/last', []);
        if (!is_array($data)) {
            $data = [];
        }
        return $data + [
            'source' => 0,
            'start'  => 0,
            'timeout' => 0,
            'sources' => [],
        ];
    }

    public function saveIndexesInfos($data) {
        include_spip('inc/config');
        ecrire_config('indexer/indexing/last', $data);
    }

    public function resetIndexesInfos() {
        include_spip('inc/config');
        effacer_config('indexer/indexing/last');
    }


    /**
     * Indexe toutes les sources en prenant en compte le timeout
     */
    public function indexAll() {

        $this->initTimeout();

        $infos = $this->loadIndexesInfos();
        $this->resetIndexesInfos();

        echo "<h1>Indexer tous les contenus :</h1>\n";
        echo "\n<pre>"; print_r($infos); echo "</pre>\n";

        $sources = $this->sources->getIterator();
        if ($infos['sources']) {
            $sources->seek($infos['sources']);
        }

        while ($sources->valid()) {
            $key    = $sources->key();
            $source = $sources->current();


            $source->setTablesLiens($this->tables_liens); // pour SPIP 2.1
            echo "<h2>Analyse de $source :</h2>\n";
            spip_timer('source');

            $parts = new \ArrayIterator($source->getParts(1000));
            if ($infos['start']) {
                $parts->seek($infos['start']);
            }

            while ($parts->valid()) {
                $part = $parts->current();

                $documents = $source->getDocuments($part['start'], $part['end']);

                if (count($documents)) {
                    spip_timer('indexage');
                    $this->indexer->replaceDocuments($documents);

                    echo "<br /><strong>Temps pour indexer " . count($documents). "</strong>\n";
                    echo "<br /><i>ids entre $part[start] et $part[end] :</i><br />\n";
                    echo spip_timer('indexage');
                }

                if ($this->isTimeout()) {
                    $this->saveIndexesInfos([
                        'timeout' => true,
                        'source' => $key,
                        'sourceClass' => (string)$source,
                        'sourceTime'  => spip_timer('source'),
                        'start' => $parts->key(),
                    ]);
                    return false;
                }

                $parts->next();
            }


            echo "<hr /><strong>Temps pour $source :</strong><br />";
            echo $t = spip_timer('source');


            if ($this->isTimeout()) {
                $this->saveIndexesInfos([
                    'timeout' => true,
                    'source' => $key,
                    'sourceClass' => (string)$source,
                    'sourceTime'  => $t,
                    'start' => 0,
                ]);
                return false;
            }


            $sources->next();
        }



        $this->resetIndexesInfos();
        return true;
    }
}
