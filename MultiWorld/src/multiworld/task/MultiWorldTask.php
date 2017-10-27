<?php

declare(strict_types = 1);

namespace multiworld\task;

use MultiWorld\MultiWorld;
use pocketmine\scheduler\Task;

/**
 * Class MultiWorldTask
 * @package multiworld\task
 */
abstract class MultiWorldTask extends Task {

    /**
     * @return MultiWorld
     */
    public function getPlugin():MultiWorld {
        return MultiWorld::getInstance();
    }
}