<?php

declare(strict_types=1);

namespace multiworld\generator\ender\populator;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

/**
 * Class EnderPilar
 * @package multiworld\Generator\ender\populator
 */
class EnderPilar extends Populator {

    /** @var ChunkManager */
    private $level;
    private $randomAmount;
    private $baseAmount;

    /**
     * @param $amount
     */
    public function setRandomAmount($amount) {
        $this->randomAmount = $amount;
    }

    /**
     * @param $amount
     */
    public function setBaseAmount($amount) {
        $this->baseAmount = $amount;
    }

    /**
     * @param ChunkManager $level
     * @param int $chunkX
     * @param int $chunkZ
     * @param Random $random
     * @return void
     */
    public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) {
        if (mt_rand(0, 100) < 10) {
            $this->level = $level;
            $amount = $random->nextRange(0, $this->randomAmount + 1) + $this->baseAmount;
            for ($i = 0; $i < $amount; ++$i) {
                $x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
                $z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
                $y = $this->getHighestWorkableBlock($x, $z);
                if ($this->level->getBlockIdAt($x, $y, $z) == Block::END_STONE) {
                    $height = mt_rand(28, 50);
                    for ($ny = $y; $ny < $y + $height; $ny++) {
                        for ($r = 0.5; $r < 5; $r += 0.5) {
                            $nd = 360 / (2 * pi() * $r);
                            for ($d = 0; $d < 360; $d += $nd) {
                                $level->setBlockIdAt($x + (cos(deg2rad($d)) * $r), $ny, $z + (sin(deg2rad($d)) * $r), Block::OBSIDIAN);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $x
     * @param $z
     * @return int
     */
    private function getHighestWorkableBlock($x, $z) {
        for ($y = 127; $y >= 0; --$y) {
            $b = $this->level->getBlockIdAt($x, $y, $z);
            if ($b == Block::END_STONE) {
                break;
            }
        }
        return $y === 0 ? -1 : $y;
    }
}