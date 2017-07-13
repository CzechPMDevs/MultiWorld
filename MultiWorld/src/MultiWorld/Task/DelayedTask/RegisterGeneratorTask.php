<?php

namespace MultiWorld\Task\DelayedTask;

use MultiWorld\MultiWorld;
use pocketmine\level\generator\Generator;
use pocketmine\scheduler\PluginTask;

class RegisterGeneratorTask extends PluginTask {

    /** @var  MultiWorld */
    public $plugin;

    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function onRun($currentTick) {
        // WILL BE ADDED VOID GENERATOR
    }
}
