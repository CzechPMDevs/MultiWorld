<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2022  CzechPMDevs
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

namespace czechpmdevs\multiworld\generator\normal;

use czechpmdevs\multiworld\generator\normal\biome\Badlands;
use czechpmdevs\multiworld\generator\normal\biome\BadlandsPlateau;
use czechpmdevs\multiworld\generator\normal\biome\Beach;
use czechpmdevs\multiworld\generator\normal\biome\BirchForest;
use czechpmdevs\multiworld\generator\normal\biome\DeepOcean;
use czechpmdevs\multiworld\generator\normal\biome\Desert;
use czechpmdevs\multiworld\generator\normal\biome\DesertHills;
use czechpmdevs\multiworld\generator\normal\biome\ExtremeHills;
use czechpmdevs\multiworld\generator\normal\biome\ExtremeHillsEdge;
use czechpmdevs\multiworld\generator\normal\biome\ExtremeHillsMutated;
use czechpmdevs\multiworld\generator\normal\biome\Forest;
use czechpmdevs\multiworld\generator\normal\biome\ForestHills;
use czechpmdevs\multiworld\generator\normal\biome\FrozenOcean;
use czechpmdevs\multiworld\generator\normal\biome\FrozenRiver;
use czechpmdevs\multiworld\generator\normal\biome\IceMountains;
use czechpmdevs\multiworld\generator\normal\biome\IcePlains;
use czechpmdevs\multiworld\generator\normal\biome\Jungle;
use czechpmdevs\multiworld\generator\normal\biome\MushroomIsland;
use czechpmdevs\multiworld\generator\normal\biome\MushroomIslandShore;
use czechpmdevs\multiworld\generator\normal\biome\Ocean;
use czechpmdevs\multiworld\generator\normal\biome\Plains;
use czechpmdevs\multiworld\generator\normal\biome\River;
use czechpmdevs\multiworld\generator\normal\biome\RoffedForestHills;
use czechpmdevs\multiworld\generator\normal\biome\RoofedForest;
use czechpmdevs\multiworld\generator\normal\biome\Savanna;
use czechpmdevs\multiworld\generator\normal\biome\SavannaPlateau;
use czechpmdevs\multiworld\generator\normal\biome\SunflowerPlains;
use czechpmdevs\multiworld\generator\normal\biome\Swampland;
use czechpmdevs\multiworld\generator\normal\biome\Taiga;
use czechpmdevs\multiworld\generator\normal\biome\TaigaHills;
use czechpmdevs\multiworld\generator\normal\biome\TallBirchForest;
use czechpmdevs\multiworld\generator\normal\biome\types\Biome;
use czechpmdevs\multiworld\world\data\BiomeIds;
use InvalidArgumentException;
use pocketmine\utils\SingletonTrait;
use function array_key_exists;

class BiomeFactory implements BiomeIds {
	use SingletonTrait;

	/** @var Biome[] */
	private array $biomes = [];

	final protected function __construct() {
		$this->registerBiome(BiomeIds::OCEAN, new Ocean());
		$this->registerBiome(BiomeIds::PLAINS, new Plains());
		$this->registerBiome(BiomeIds::DESERT, new Desert());
		$this->registerBiome(BiomeIds::EXTREME_HILLS, new ExtremeHills());
		$this->registerBiome(BiomeIds::FOREST, new Forest());
		$this->registerBiome(BiomeIds::TAIGA, new Taiga());
		$this->registerBiome(BiomeIds::SWAMP, new Swampland());
		$this->registerBiome(BiomeIds::RIVER, new River());
		$this->registerBiome(BiomeIds::FROZEN_OCEAN, new FrozenOcean());
		$this->registerBiome(BiomeIds::FROZEN_RIVER, new FrozenRiver());
		$this->registerBiome(BiomeIds::ICE_PLAINS, new IcePlains());
		$this->registerBiome(BiomeIds::ICE_MOUNTAINS, new IceMountains());
		$this->registerBiome(BiomeIds::MUSHROOM_ISLAND, new MushroomIsland());
		$this->registerBiome(BiomeIds::MUSHROOM_ISLAND_SHORE, new MushroomIslandShore());
		$this->registerBiome(BiomeIds::BEACH, new Beach());
		$this->registerBiome(BiomeIds::DESERT_HILLS, new DesertHills());
		$this->registerBiome(BiomeIds::FOREST_HILLS, new ForestHills());
		$this->registerBiome(BiomeIds::TAIGA_HILLS, new TaigaHills());
		$this->registerBiome(BiomeIds::EXTREME_HILLS_EDGE, new ExtremeHillsEdge());
		$this->registerBiome(BiomeIds::JUNGLE, new Jungle());
		// TODO: Ids 21 - 23
		$this->registerBiome(BiomeIds::DEEP_OCEAN, new DeepOcean());
		// TODO: Ids 25 - 26
		$this->registerBiome(BiomeIds::BIRCH_FOREST, new BirchForest());
		// TODO: Id 28
		$this->registerBiome(BiomeIds::ROOFED_FOREST, new RoofedForest());
		// TODO Ids 30 - 34
		$this->registerBiome(BiomeIds::SAVANNA, new Savanna());
		$this->registerBiome(BiomeIds::SAVANNA_PLATEAU, new SavannaPlateau());
		$this->registerBiome(BiomeIds::BADLANDS, new Badlands());
		$this->registerBiome(BiomeIds::BADLANDS_PLATEAU, new BadlandsPlateau());
		// TODO Ids 39 - 128
		$this->registerBiome(BiomeIds::SUNFLOWER_PLAINS, new SunflowerPlains());
		// TODO Id 130
		$this->registerBiome(BiomeIds::EXTREME_HILLS_MUTATED, new ExtremeHillsMutated());
		// TODO Ids 132 - 154
		$this->registerBiome(BiomeIds::TALL_BIRCH_FOREST, new TallBirchForest());
		// TODO Id 156
		$this->registerBiome(BiomeIds::ROOFED_FOREST_HILLS, new RoffedForestHills());
	}

	public function registerBiome(int $id, Biome $biome): void {
		$biome->setId($id);

		$this->biomes[$id] = $biome;
	}

	public function getBiome(int $id): Biome {
		if(!array_key_exists($id, $this->biomes)) {
			throw new InvalidArgumentException("Biome with id $id is not registered.");
		}

		return $this->biomes[$id];
	}
}