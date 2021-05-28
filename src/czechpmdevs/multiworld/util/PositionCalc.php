<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\util;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

trait PositionCalc {

    public function getRandomSpawnPosition(ChunkManager $level, int $chunkX, int $chunkZ, Random $random, ?int &$x, ?int &$y, ?int &$z): void {
        $x = ($chunkX << 4) + $random->nextBoundedInt(16);
        $z = ($chunkZ << 4) + $random->nextBoundedInt(16);
        $y = $this->getHighestWorkableBlock($level, $x, $z);
    }

    public function getHighestWorkableBlock(ChunkManager $level, int $x, int $z): int {
        for ($y = 127; $y >= 0; --$y) {
            $b = $level->getBlockIdAt($x, $y, $z);
            if ($b !== Block::AIR and $b !== Block::LEAVES and $b !== Block::LEAVES2 and $b !== Block::SNOW_LAYER) {
                break;
            }
        }

        return $y === 0 ? -1 : ++$y;
    }
}