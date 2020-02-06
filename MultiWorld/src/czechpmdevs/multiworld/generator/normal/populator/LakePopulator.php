<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\generator\normal\populator;

use pocketmine\block\Block;
use pocketmine\item\enchantment\FireAspectEnchantment;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\Populator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

/**
 * Class LakePopulator
 * @package vixikhd\customgen\populator
 */
class LakePopulator extends Populator {
    use PopulatorTrait;

    public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) {
        if($random->nextRange(0, 16) != 0) {
            return;
        }

        $x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
        $z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
        $y = $this->getHighestWorkableBlock($level, $x, $z)-4;

        $blocks = [];

        /** @var Vector3 $vec */
        foreach ($this->getRandomShape($random) as [$xx, $yy, $zz]) {
            $xx += $x;
            $yy += $y;
            $zz += $z;

            $id = $yy <= $y+2 ? Block::WATER : Block::AIR;

            $blocks[] = [$xx, $yy, $zz, $id];
            if($id == Block::WATER && in_array(Block::AIR, [$level->getBlockIdAt($xx, $yy, $zz), $level->getBlockIdAt($xx+1, $yy, $zz),$level->getBlockIdAt($xx+1, $yy, $zz+1), $level->getBlockIdAt($xx, $yy, $zz+1)])) {
                return;
            }
        }

        foreach ($blocks as [$x, $y, $z, $id]) {
            $level->setBlockIdAt($x, $y, $z, $id);
            $level->setBlockDataAt($x, $y, $z, 0);
        }
    }

    /**
     * @return \Generator
     */
    private function getRandomShape(Random $random): \Generator {
        for($x = -($random->nextRange(12, 20)); $x < $random->nextRange(12, 20); $x++) {
            $xsqr = $x*$x;
            for($z = -($random->nextRange(12, 20)); $z < $random->nextRange(12, 20); $z++) {
                $zsqr = $z*$z;
                for($y = $random->nextRange(0, 1); $y < $random->nextRange(4, 5); $y++) {
                    if(($xsqr*1.5)+($zsqr*1.5) <= $random->nextRange(12, 22)) {
                        yield [$x, $y, $z];
                    }
                }
            }
        }
    }
}