<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\structure\object;

use czechpmdevs\multiworld\util\SimpleBlockData;
use pocketmine\utils\Random;

/**
 * Class StructureBlock
 * @package czechpmdevs\multiworld\structure\object
 */
class StructureBlock {

    /** @var SimpleBlockData[] $blocks */
    public $blocks = [];

    /**
     * @param SimpleBlockData $block
     */
    public function addBlock(SimpleBlockData $block) {
        $this->blocks[] = $block;
    }

    /**
     * @param Random $random
     * @return SimpleBlockData|null
     */
    public function getBlock(Random $random): ?SimpleBlockData {
        if(count($this->blocks) === 0) {
            return null;
        }
        return $this->blocks[$random->nextBoundedInt(count($this->blocks))];
    }

    /**
     * @param Random $random
     * @return array
     */
    public function toArray(Random $random) {
        $targetBlock = $this->getBlock($random);

        return [$targetBlock->getId(), $targetBlock->getMeta()];
    }
}