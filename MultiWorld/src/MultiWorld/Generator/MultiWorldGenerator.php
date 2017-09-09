<?php

namespace MultiWorld\Generator;

use MultiWorld\Generator\ender\EnderGenerator;
use MultiWorld\Generator\void\VoidGenerator;
use MultiWorld\MultiWorld;
use pocketmine\level\generator\Generator;

/**
 * Class MultiWorldGenerator
 * @package MultiWorld\Generator
 */
class MultiWorldGenerator {

    /** @var  MultiWorld $plugin */
    public $plugin;

    /**
     * MultiWorldGenerator constructor.
     * @param MultiWorld $plugin
     */
    public function __construct(MultiWorld $plugin) {
        $this->loadGenerators();
        $this->plugin = $plugin;
    }

    function loadGenerators() {
        Generator::addGenerator(EnderGenerator::class, "ender");
        Generator::addGenerator(VoidGenerator::class, "void");
    }
}