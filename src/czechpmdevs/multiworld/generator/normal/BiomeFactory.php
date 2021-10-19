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
use czechpmdevs\multiworld\level\data\BiomeIds;
use InvalidStateException;
use pocketmine\level\biome\Biome;
use function array_key_exists;

class BiomeFactory implements BiomeIds {

	/** @var BiomeFactory */
	private static BiomeFactory $instance;

	/** @var Biome[] */
	private array $biomes = [];

	public function registerBiome(int $id, Biome $biome): void {
		$biome->setId($id);

		$this->biomes[$id] = $biome;
	}

	public function getBiome(int $id): Biome {
		if(!array_key_exists($id, $this->biomes)) {
			throw new InvalidStateException("Biome with id $id is not registered.");
		}

		return $this->biomes[$id];
	}

	private static function init(): void {
		BiomeFactory::$instance = new self;

		BiomeFactory::$instance->registerBiome(BiomeIds::OCEAN, new Ocean());
		BiomeFactory::$instance->registerBiome(BiomeIds::PLAINS, new Plains());
		BiomeFactory::$instance->registerBiome(BiomeIds::DESERT, new Desert());
		BiomeFactory::$instance->registerBiome(BiomeIds::EXTREME_HILLS, new ExtremeHills());
		BiomeFactory::$instance->registerBiome(BiomeIds::FOREST, new Forest());
		BiomeFactory::$instance->registerBiome(BiomeIds::TAIGA, new Taiga());
		BiomeFactory::$instance->registerBiome(BiomeIds::SWAMP, new Swampland());
		BiomeFactory::$instance->registerBiome(BiomeIds::RIVER, new River());
		BiomeFactory::$instance->registerBiome(BiomeIds::FROZEN_OCEAN, new FrozenOcean());
		BiomeFactory::$instance->registerBiome(BiomeIds::FROZEN_RIVER, new FrozenRiver());
		BiomeFactory::$instance->registerBiome(BiomeIds::ICE_PLAINS, new IcePlains());
		BiomeFactory::$instance->registerBiome(BiomeIds::ICE_MOUNTAINS, new IceMountains());
		BiomeFactory::$instance->registerBiome(BiomeIds::MUSHROOM_ISLAND, new MushroomIsland());
		BiomeFactory::$instance->registerBiome(BiomeIds::MUSHROOM_ISLAND_SHORE, new MushroomIslandShore());
		BiomeFactory::$instance->registerBiome(BiomeIds::BEACH, new Beach());
		BiomeFactory::$instance->registerBiome(BiomeIds::DESERT_HILLS, new DesertHills());
		BiomeFactory::$instance->registerBiome(BiomeIds::FOREST_HILLS, new ForestHills());
		BiomeFactory::$instance->registerBiome(BiomeIds::TAIGA_HILLS, new TaigaHills());
		BiomeFactory::$instance->registerBiome(BiomeIds::EXTREME_HILLS_EDGE, new ExtremeHillsEdge());
		BiomeFactory::$instance->registerBiome(BiomeIds::JUNGLE, new Jungle());
		// TODO: Ids 21 - 23
		BiomeFactory::$instance->registerBiome(BiomeIds::DEEP_OCEAN, new DeepOcean());
		// TODO: Ids 25 - 26
		BiomeFactory::$instance->registerBiome(BiomeIds::BIRCH_FOREST, new BirchForest());
		// TODO: Id 28
		BiomeFactory::$instance->registerBiome(BiomeIds::ROOFED_FOREST, new RoofedForest());
		// TODO Ids 30 - 34
		BiomeFactory::$instance->registerBiome(BiomeIds::SAVANNA, new Savanna());
		BiomeFactory::$instance->registerBiome(BiomeIds::SAVANNA_PLATEAU, new SavannaPlateau());
		BiomeFactory::$instance->registerBiome(BiomeIds::BADLANDS, new Badlands());
		BiomeFactory::$instance->registerBiome(BiomeIds::BADLANDS_PLATEAU, new BadlandsPlateau());
		// TODO Ids 39 - 128
		BiomeFactory::$instance->registerBiome(BiomeIds::SUNFLOWER_PLAINS, new SunflowerPlains());
		// TODO Id 130
		BiomeFactory::$instance->registerBiome(BiomeIds::EXTREME_HILLS_MUTATED, new ExtremeHillsMutated());
		// TODO Ids 132 - 154
		BiomeFactory::$instance->registerBiome(BiomeIds::TALL_BIRCH_FOREST, new TallBirchForest());
		// TODO Id 156
		BiomeFactory::$instance->registerBiome(BiomeIds::ROOFED_FOREST_HILLS, new RoffedForestHills());
	}

	public static function getInstance(): BiomeFactory {
		if(!isset(BiomeFactory::$instance)) {
			BiomeFactory::init();
		}

		return BiomeFactory::$instance;
	}
}