<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2019  CzechPMDevs
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace czechpmdevs\multiworld\generator\nether;

use czechpmdevs\multiworld\generator\nether\populator\GlowstoneSphere;
use czechpmdevs\multiworld\generator\nether\populator\Ore;
use czechpmdevs\multiworld\generator\nether\populator\SoulSand;
use pocketmine\block\Block;
use pocketmine\block\NetherQuartzOre;
use pocketmine\level\biome\Biome;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\noise\Simplex;
use pocketmine\level\generator\object\OreType;
use pocketmine\level\generator\populator\Populator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;


/**
 * Edited PocketMine-MP generator (https://github.com/pmmp/PocketMine-MP/blob/master/src/pocketmine/level/generator/hell/Nether.php) for MultiWorld
 *
 * Features:
 *  - Glowstone populator
 *  - Soulsand with netherwarts generator
 *  - Quartz ore generator
 *  - Nether base - TODO
 *
 * Class NetherGenerator
 * @package czechpmdevs\multiworld\generator\nether
 */
class NetherGenerator extends Generator {

    /** @var Populator[] */
    private $populators = [];
    /** @var int */
    private $waterHeight = 32;
    /** @var int */
    private $emptyHeight = 64;
    /** @var int */
    private $emptyAmplitude = 1;
    /** @var float */
    private $density = 0.5;

    /** @var Populator[] */
    private $generationPopulators = [];
    /** @var Simplex $noiseBase */
    private $noiseBase;

    public function __construct(array $options = []){

    }

    /**
     * @return string
     */
    public function getName(): string {
        return "nether";
    }

    /**
     * @return array
     */
    public function getSettings(): array {
        return [];
    }

    /**
     * @param ChunkManager $level
     * @param Random $random
     */
    public function init(ChunkManager $level, Random $random) : void{
        parent::init($level, $random);
        $this->random->setSeed($this->level->getSeed());
        $this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 64);
        $this->random->setSeed($this->level->getSeed());

        $ores = new Ore();
        $ores->setOreTypes([
            new OreType(new NetherQuartzOre(), 50, 14, 0, 128)
        ]);
        $this->populators[] = $ores;
        $this->populators[] = new GlowstoneSphere();
        $this->populators[] = new SoulSand();
    }

    /**
     * @param int $chunkX
     * @param int $chunkZ
     */
    public function generateChunk(int $chunkX, int $chunkZ) : void{
        $this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());

        $noise = $this->noiseBase->getFastNoise3D(16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);
        $chunk = $this->level->getChunk($chunkX, $chunkZ);

        for($x = 0; $x < 16; ++$x){
            for($z = 0; $z < 16; ++$z){

                $biome = Biome::getBiome(Biome::HELL);
                $chunk->setBiomeId($x, $z, $biome->getId());

                for($y = 0; $y < 128; ++$y){
                    if($y === 0 or $y === 127){
                        $chunk->setBlockId($x, $y, $z, Block::BEDROCK);
                        continue;
                    }
                    if($y === 126) {
                        $chunk->setBlockId($x, $y, $z, Block::NETHERRACK);
                        continue;
                    }
                    $noiseValue = (\abs($this->emptyHeight - $y) / $this->emptyHeight) * $this->emptyAmplitude - $noise[$x][$z][$y];
                    $noiseValue -= 1 - $this->density;

                    if($noiseValue > 0){
                        $chunk->setBlockId($x, $y, $z, Block::NETHERRACK);
                    }elseif($y <= $this->waterHeight){
                        $chunk->setBlockId($x, $y, $z, Block::STILL_LAVA);
                    }
                }
            }
        }

        foreach($this->generationPopulators as $populator){
            $populator->populate($this->level, $chunkX, $chunkZ, $this->random);
        }
    }

    /**
     * @param int $chunkX
     * @param int $chunkZ
     */
    public function populateChunk(int $chunkX, int $chunkZ) : void{
        $this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());
        foreach($this->populators as $populator){
            $populator->populate($this->level, $chunkX, $chunkZ, $this->random);
        }

        $chunk = $this->level->getChunk($chunkX, $chunkZ);
        $biome = Biome::getBiome($chunk->getBiomeId(7, 7));
        $biome->populateChunk($this->level, $chunkX, $chunkZ, $this->random);
    }

    /**
     * @return Vector3
     */
    public function getSpawn() : Vector3 {
        return new Vector3(127.5, 128, 127.5);
    }
}