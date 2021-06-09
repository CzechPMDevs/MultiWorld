<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\generator\normal\populator;

use czechpmdevs\multiworld\generator\normal\populator\object\Plant;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

class PlantPopulator extends Populator {
    use PopulatorTrait;

    /** @var ChunkManager */
    private $level;

    /** @var int $randomAmount */
    private $randomAmount;

    /** @var int $baseAmount */
    private $baseAmount;

    /** @var int $spawnPercentage */
    private $spawnPercentage = 100;

    /** @var Plant[] $plants */
    private $plants = [];

    /** @var array $allowedBlocks */
    private $allowedBlocks = [];

    /**
     * @param $amount
     */
    public function setRandomAmount($amount){
        $this->randomAmount = $amount;
    }

    /**
     * @param $amount
     */
    public function setBaseAmount($amount){
        $this->baseAmount = $amount;
    }

    /**
     * @param Plant $plant
     */
    public function addPlant(Plant $plant) {
        $this->plants[] = $plant;
    }

    /**
     * @param int $blockId
     */
    public function allowBlockToStayAt(int $blockId) {
        $this->allowedBlocks[] = $blockId;
    }

    /**
     * @param int $spawnPercentage
     */
    public function setSpawnPercentage(int $spawnPercentage) {
        $this->spawnPercentage = $spawnPercentage;
    }

    /**
     * @param ChunkManager $level
     * @param int $chunkX
     * @param int $chunkZ
     * @param Random $random
     * @return mixed|void
     */
    public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random){
        if(count($this->plants) === 0) {
            return;
        }
        if($random->nextRange(100, $this->spawnPercentage) != 100) {
            return;
        }
        $this->level = $level;
        $amount = $random->nextRange(0, $this->randomAmount + 1) + $this->baseAmount;
        for($i = 0; $i < $amount; ++$i){
            $x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
            $z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
            $y = $this->getHighestWorkableBlock($level, $x, $z);

            if($y !== -1 and $this->canPlantStay($x, $y, $z)){
                $plant = $random->nextRange(0, (int)(count($this->plants)-1));
                $pY = $y;
                foreach ($this->plants[$plant]->blocks as $block) {
                    $level->setBlockIdAt($x, $pY, $z, $block->getId());
                    $level->setBlockDataAt($x, $pY, $z, $block->getDamage());
                    $pY++;
                }
            }
        }
    }

    private function canPlantStay(int $x, int $y, int $z) : bool{
        $b = $this->level->getBlockIdAt($x, $y, $z);
        return ($b === Block::AIR or $b === Block::SNOW_LAYER or $b === Block::WATER) and in_array($this->level->getBlockIdAt($x, $y - 1, $z), array_merge([Block::GRASS], $this->allowedBlocks)) ;
    }
}