<?php

namespace MultiWorld\Generator;

use MultiWorld\Generator\Flat\FlatGenerator;
use MultiWorld\MultiWorld;
use pocketmine\level\generator\Generator;

class AdvancedGenerator {

    /** @var  MultiWorld */
    public $plugin;

    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
    }

    public function registerGenerators() {
        Generator::addGenerator(FlatGenerator::class, "FlatGenerator");
        $this->plugin->getLogger()->debug("[MW] Generator FlatGenerator is actived!");
    }
}
