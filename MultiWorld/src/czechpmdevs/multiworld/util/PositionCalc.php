<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\util;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

/**
 * Trait PositionCalc
 * @package czechpmdevs\multiworld\util
 */
trait PositionCalc {

    /**
     * @param ChunkManager $level
     * @param int $chunkX
     * @param int $chunkZ
     * @param Random $random
     *
     * @param int &$x
     * @param int &$y
     * @param int &$z
     */
    public function getRandomSpawnPosition(ChunkManager $level, int $chunkX, int $chunkZ, Random $random, ?int &$x, ?int &$y, ?int &$z): void {
        $x = ($chunkX << 4) + $random->nextBoundedInt(16);
        $z = ($chunkZ << 4) + $random->nextBoundedInt(16);
        $y = $this->getHighestWorkableBlock($level, $x, $z);
    }

    /**
     * @param ChunkManager $level
     * @param int $x
     * @param int $z
     *
     * @return int
     */
    public function getHighestWorkableBlock(ChunkManager $level, int $x, int $z): int {
        for($y = 127; $y >= 0; --$y){
            $b = $level->getBlockIdAt($x, $y, $z);
            if($b !== Block::AIR and $b !== Block::LEAVES and $b !== Block::LEAVES2 and $b !== Block::SNOW_LAYER){
                break;
            }
        }

        return $y === 0 ? -1 : ++$y;
    }
}