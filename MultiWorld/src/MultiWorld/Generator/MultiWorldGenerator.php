<?php

namespace MultiWorld\Generator;

use MultiWorld\Generator\ender\Ender;
use MultiWorld\Generator\void\Void;
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
        $this->plugin = $plugin;
    }

    public function loadGenerators() {
        Generator::addGenerator(Ender::class, "ender");
        Generator::addGenerator(Void::class, "void");
    }
}