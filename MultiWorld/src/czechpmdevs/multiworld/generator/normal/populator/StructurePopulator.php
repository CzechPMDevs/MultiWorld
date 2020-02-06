<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\generator\normal\populator;

use czechpmdevs\multiworld\structure\StructureManager;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

/**
 * Class StructurePopulator
 * @package czechpmdevs\multiworld\generator\normal\populator
 */
class StructurePopulator extends Populator {
    use PopulatorTrait;

    /**
     * @inheritDoc
     */
    public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) {
        if($random->nextBoundedInt(10) !== 5) {
            return;
        }

        $x = ($chunkX << 4) + $random->nextBoundedInt(16);
        $z = ($chunkZ << 4) + $random->nextBoundedInt(16);
        $y = $this->getHighestWorkableBlock($level, $x, $z);

        if(is_null(StructureManager::getPillagerOutpost())) {
            StructureManager::loadPillagerOutpost();
        }

        $pillagerOutpost = StructureManager::getPillagerOutpost();
        $pillagerOutpost->placeAt($level, $x, $y, $z, $random);
    }
}