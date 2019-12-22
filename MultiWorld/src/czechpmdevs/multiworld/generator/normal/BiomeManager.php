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

namespace czechpmdevs\multiworld\generator\normal;

use czechpmdevs\multiworld\generator\normal\biome\Beach;
use czechpmdevs\multiworld\generator\normal\biome\BirchForest;
use czechpmdevs\multiworld\generator\normal\biome\DeepOcean;
use czechpmdevs\multiworld\generator\normal\biome\Desert;
use czechpmdevs\multiworld\generator\normal\biome\DesertHills;
use czechpmdevs\multiworld\generator\normal\biome\Forest;
use czechpmdevs\multiworld\generator\normal\biome\ForestHills;
use czechpmdevs\multiworld\generator\normal\biome\GravellyMountains;
use czechpmdevs\multiworld\generator\normal\biome\Mountains;
use czechpmdevs\multiworld\generator\normal\biome\Ocean;
use czechpmdevs\multiworld\generator\normal\biome\Plains;
use czechpmdevs\multiworld\generator\normal\biome\River;
use czechpmdevs\multiworld\generator\normal\biome\Savanna;
use czechpmdevs\multiworld\generator\normal\biome\SunflowerPlains;
use czechpmdevs\multiworld\generator\normal\biome\Swamp;
use czechpmdevs\multiworld\generator\normal\biome\Taiga;
use czechpmdevs\multiworld\generator\normal\biome\TallBirchForest;
use pocketmine\level\biome\Biome;

/**
 * Class BiomeManager
 */
class BiomeManager {

    /** @var array $hillBiomes */
    protected static $hillBiomes = [];
    /** @var array $biomes */
    protected static $biomes = [];

    const OCEAN = 0;
    const PLAINS = 1;
    const DESERT = 2;
    const MOUNTAINS = 3;
    const FOREST = 4;
    const TAIGA = 5;
    const SWAMP = 6;
    const RIVER = 7;
    //const FROZEN_OCEAN = 10;
    //const FROZEN_RIVER = 11;
    //const ICE_PLAINS = 12;
    //const ICE_MOUNTAINS = 13;
    //const MUSHROOM_ISLAND = 14;
    //const MUSHROOM_ISLAND_SHORE = 15;
    const BEACH = 16;
    const DESERT_HILLS = 17;
    const FOREST_HILLS = 18;
    //const TAIGA_HILLS = 19;
    //const SMALL_MOUNTAINS = 20;
    //const JUNGLE = 21;
    //const JUNGLE_HILLS = 22;
    //const JUNGLE_EDGE = 23;
    const DEEP_OCEAN = 24;
    //const STONE_BEACH = 25;
    //const COLD_BEACH = 26;
    const BIRCH_FOREST = 27;
    //const BIRCH_FOREST_HILLS = 28;
    //const ROOFED_FOREST = 29;
    //const COLD_TAIGA = 30;
    //const COLD_TAIGA_HILLS = 31;
    //const MEGA_TAIGA = 32;
    //const MEGA_TAIGA_HILLS = 33;
    //const EXTREME_HILLS_PLUS = 34;
    const SAVANNA = 35;
    //const SAVANNA_PLATEAU = 36;
    //const MESA = 37;
    //const MESA_PLATEAU_F = 38;
    //const MESA_PLATEAU = 39;
    const SUNFLOWER_PLAINS = 129;
    const GRAVELLY_MOUNTAINS = 131;
    const TALL_BIRCH_FOREST = 155;

    /**
     * @throws \ReflectionException
     *
     * Implement this just to threads with MultiWorld generator
     */
    public static function registerBiomes() {
        $biomeClass = new \ReflectionClass(Biome::class);

        $biomes = $biomeClass->getProperty("biomes");
        $biomes->setAccessible(true);
        $biomes->setValue(new \SplFixedArray(Biome::MAX_BIOMES)); // TODO: add all the MultiWorld biomes :D

        $register = $biomeClass->getMethod("register");
        $register->setAccessible(true);
        foreach (static::getBiomes() as $id => $biome) {
            $register->invokeArgs(null, [$id, $biome]);
        }
    }

    /**
     * @param int $id
     * @param bool $hills
     * @return mixed|null
     */
    public static function getBiome(int $id) {
        return Biome::getBiome($id);
    }

    /**
     * @param bool $hills
     * @return array
     */
    private static function getBiomes(): array {
        return [
            0 => new Ocean(),
            1 => new Plains(),
            2 => new Desert(),
            3 => new Mountains(),
            4 => new Forest(),
            5 => new Taiga(),
            6 => new Swamp(),
            7 => new River(),
            16 => new Beach(),
            17 => new DesertHills(),
            18 => new ForestHills(),
            24 => new DeepOcean(),
            27 => new BirchForest(),
            35 => new Savanna(),
            129 => new SunflowerPlains(),
            131 => new GravellyMountains(),
            155 => new TallBirchForest()
        ];
    }
}