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

namespace czechpmdevs\multiworld\generator\normal\object;

use pocketmine\level\ChunkManager;
use pocketmine\level\generator\object\BirchTree;
use pocketmine\level\generator\object\JungleTree;
use pocketmine\level\generator\object\OakTree;
use pocketmine\level\generator\object\SpruceTree;
use pocketmine\level\generator\object\Tree as TreeObject;
use pocketmine\utils\Random;

abstract class Tree extends TreeObject {

	public const OAK = 0;
	public const SPRUCE = 1;
	public const BIRCH = 2;
	public const JUNGLE = 3;
	public const ACACIA = 4;
	public const DARK_OAK = 5;
	public const BIG_BIRCH = 6;

	public const SMALL_OAK = 10;
	public const BIG_OAK = 11;

	public const MUSHROOM = 20;

	public static function growTree(ChunkManager $level, int $x, int $y, int $z, Random $random, int $type = 0, bool $vines = false) {
		switch($type) {
			case self::SPRUCE:
				$tree = new SpruceTree();
				break;
			case self::BIRCH:
				if($random->nextBoundedInt(39) === 0) {
					$tree = new BirchTree(true);
				} else {
					$tree = new BirchTree();
				}
				break;
			case self::BIG_BIRCH:
				$tree = new BirchTree(true);
				break;
			case self::JUNGLE:
				$tree = new JungleTree();
				break;
			case self::ACACIA:
				$tree = new AcaciaTree();
				break;
			case self::DARK_OAK:
				$tree = new DarkOakTree();
				break;
			case self::MUSHROOM:
				$tree = new HugeMushroom();
				break;
			default:
				if($vines) {
					$tree = new SwampTree();
					goto placeObject;
				}

				if($type !== self::SMALL_OAK && $random->nextRange(0, 9) === 0) {
					$tree = new BigOakTree($random);
				} else {
					$tree = new OakTree();
				}
				break;
		}

		placeObject:
		if($tree->canPlaceObject($level, $x, $y, $z, $random)) {
			$tree->placeObject($level, $x, $y, $z, $random);
		}
	}
}