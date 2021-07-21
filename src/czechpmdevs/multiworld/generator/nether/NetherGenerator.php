<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2021  CzechPMDevs
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
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\NetherQuartzOre;
use pocketmine\world\biome\Biome;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\noise\Simplex;
use pocketmine\world\generator\object\OreType;
use pocketmine\world\generator\populator\Populator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use function abs;

class NetherGenerator extends Generator {

    /** @var Populator[] */
    private array $populators = [];
    /** @var int */
    private int $waterHeight = 32;
    /** @var int */
    private int $emptyHeight = 64;
    /** @var int */
    private int $emptyAmplitude = 1;
    /** @var float */
    private float $density = 0.5;

    /** @var Populator[] */
    private array $generationPopulators = [];
    /** @var Simplex $noiseBase */
    private Simplex $noiseBase;

    /** @phpstan-ignore-next-line */
    public function __construct(array $options = []) {
    }

    public function init(ChunkManager $world, Random $random): void {
        parent::init($world, $random);
        $this->random->setSeed($this->world->getSeed());
        $this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 64);
        $this->random->setSeed($this->world->getSeed());

        $ores = new Ore();
        $ores->setOreTypes([
            new OreType(new NetherQuartzOre(), 50, 14, 0, 128)
        ]);
        $this->populators[] = $ores;
        $this->populators[] = new GlowstoneSphere();
        $this->populators[] = new SoulSand();
    }

    public function generateChunk(int $chunkX, int $chunkZ): void {
        $this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->world->getSeed());

        /** @phpstan-var Chunk $chunk */
        $chunk = $this->world->getChunk($chunkX, $chunkZ);
        $noise = $this->noiseBase->getFastNoise3D(16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);

        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {

                $biome = Biome::getBiome(Biome::HELL);
                $chunk->setBiomeId($x, $z, $biome->getId());

                for ($y = 0; $y < 128; ++$y) {
                    if ($y === 0 or $y === 127) {
                        $chunk->setBlockId($x, $y, $z, BlockLegacyIds::BEDROCK);
                        continue;
                    }
                    if ($y === 126) {
                        $chunk->setBlockId($x, $y, $z, BlockLegacyIds::NETHERRACK);
                        continue;
                    }
                    $noiseValue = (abs($this->emptyHeight - $y) / $this->emptyHeight) * $this->emptyAmplitude - $noise[$x][$z][$y];
                    $noiseValue -= 1 - $this->density;

                    if ($noiseValue > 0) {
                        $chunk->setBlockId($x, $y, $z, BlockLegacyIds::NETHERRACK);
                    } elseif ($y <= $this->waterHeight) {
                        $chunk->setBlockId($x, $y, $z, BlockLegacyIds::STILL_LAVA);
                    }
                }
            }
        }

        foreach ($this->generationPopulators as $populator) {
            $populator->populate($this->world, $chunkX, $chunkZ, $this->random);
        }
    }

    public function populateChunk(int $chunkX, int $chunkZ): void {
        $this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->world->getSeed());
        foreach ($this->populators as $populator) {
            $populator->populate($this->world, $chunkX, $chunkZ, $this->random);
        }

        /** @phpstan-var Chunk $chunk */
        $chunk = $this->world->getChunk($chunkX, $chunkZ);
        $biome = Biome::getBiome($chunk->getBiomeId(7, 7));
        $biome->populateChunk($this->world, $chunkX, $chunkZ, $this->random);
    }

    public function getName(): string {
        return "nether";
    }

    public function getSpawn(): Vector3 {
        return new Vector3(127.5, 128, 127.5);
    }

    public function getSettings(): array {
        return [];
    }
}