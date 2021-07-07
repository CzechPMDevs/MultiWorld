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
use czechpmdevs\multiworld\generator\normal\biome\Mesa;
use czechpmdevs\multiworld\generator\normal\biome\MesaPlateau;
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
use InvalidStateException;
use pocketmine\level\biome\Biome;
use function array_key_exists;

class BiomeFactory {

    public const OCEAN = 0;
    public const PLAINS = 1;
    public const DESERT = 2;
    public const EXTREME_HILLS = 3;
    public const FOREST = 4;
    public const TAIGA = 5;
    public const SWAMP = 6;
    public const RIVER = 7;
    public const NETHER = 8;
    public const THE_END = 9;
    public const FROZEN_OCEAN = 10;
    public const FROZEN_RIVER = 11; // new
    public const ICE_PLAINS = 12; // new
    public const ICE_MOUNTAINS = 13; // new
    public const MUSHROOM_ISLAND = 14;
    public const MUSHROOM_ISLAND_SHORE = 15; // new
    public const BEACH = 16; // new
    public const DESERT_HILLS = 17;
    public const FOREST_HILLS = 18;
    public const TAIGA_HILLS = 19;
    public const EXTREME_HILLS_EDGE = 20; // new
    public const JUNGLE = 21; // new
    public const DEEP_OCEAN = 24; // new
    //public const JUNGLE_HILLS = 22;
    //public const JUNGLE_EDGE = 23;
    public const BIRCH_FOREST = 27;
    //public const STONE_BEACH = 25;
    //public const COLD_BEACH = 26;
    public const ROOFED_FOREST = 29;
    //public const BIRCH_FOREST_HILLS = 28;
    public const SAVANNA = 35;
    //public const COLD_TAIGA = 30;
    //public const COLD_TAIGA_HILLS = 31;
    //public const MEGA_TAIGA = 32;
    //public const MEGA_TAIGA_HILLS = 33;
    //public const EXTREME_HILLS_PLUS = 34;
    public const SAVANNA_PLATEAU = 36;
    public const MESA = 37; // new
    public const MESA_PLATEAU = 39;
    public const SUNFLOWER_PLAINS = 129;
    public const EXTREME_HILLS_MUTATED = 131;
    public const TALL_BIRCH_FOREST = 155;
    public const ROOFED_FOREST_HILLS = 157;

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

        BiomeFactory::$instance->registerBiome(0, new Ocean());
        BiomeFactory::$instance->registerBiome(1, new Plains());
        BiomeFactory::$instance->registerBiome(2, new Desert());
        BiomeFactory::$instance->registerBiome(3, new ExtremeHills());
        BiomeFactory::$instance->registerBiome(4, new Forest());
        BiomeFactory::$instance->registerBiome(5, new Taiga());
        BiomeFactory::$instance->registerBiome(6, new Swampland());
        BiomeFactory::$instance->registerBiome(7, new River());
        BiomeFactory::$instance->registerBiome(10, new FrozenOcean());
        BiomeFactory::$instance->registerBiome(11, new FrozenRiver());
        BiomeFactory::$instance->registerBiome(12, new IcePlains());
        BiomeFactory::$instance->registerBiome(13, new IceMountains());
        BiomeFactory::$instance->registerBiome(14, new MushroomIsland());
        BiomeFactory::$instance->registerBiome(15, new MushroomIslandShore());
        BiomeFactory::$instance->registerBiome(16, new Beach());
        BiomeFactory::$instance->registerBiome(17, new DesertHills());
        BiomeFactory::$instance->registerBiome(18, new ForestHills());
        BiomeFactory::$instance->registerBiome(19, new TaigaHills());
        BiomeFactory::$instance->registerBiome(20, new ExtremeHillsEdge());
        BiomeFactory::$instance->registerBiome(21, new Jungle());
        BiomeFactory::$instance->registerBiome(24, new DeepOcean());
        BiomeFactory::$instance->registerBiome(27, new BirchForest());
        BiomeFactory::$instance->registerBiome(29, new RoofedForest());
        BiomeFactory::$instance->registerBiome(35, new Savanna());
        BiomeFactory::$instance->registerBiome(36, new SavannaPlateau());
        BiomeFactory::$instance->registerBiome(37, new Mesa());
        BiomeFactory::$instance->registerBiome(39, new MesaPlateau());
        BiomeFactory::$instance->registerBiome(129, new SunflowerPlains());
        BiomeFactory::$instance->registerBiome(131, new ExtremeHillsMutated());
        BiomeFactory::$instance->registerBiome(155, new TallBirchForest());
        BiomeFactory::$instance->registerBiome(157, new RoffedForestHills());
    }

    public static function getInstance(): BiomeFactory {
        if(!isset(BiomeFactory::$instance)) {
            BiomeFactory::init();
        }
        
        return BiomeFactory::$instance;
    }
}