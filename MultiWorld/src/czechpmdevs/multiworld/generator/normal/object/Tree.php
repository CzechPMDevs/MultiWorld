<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\generator\normal\object;

use pocketmine\block\Sapling;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\object\BirchTree;
use pocketmine\level\generator\object\JungleTree;
use pocketmine\level\generator\object\OakTree;
use pocketmine\level\generator\object\SpruceTree;
use pocketmine\level\generator\object\Tree as TreeObject;
use pocketmine\utils\Random;

/**
 * Class Tree
 * @package czechpmdevs\multiworld\generator\normal\object
 */
abstract class Tree extends TreeObject {

    public static function growTree(ChunkManager $level, int $x, int $y, int $z, Random $random, int $type = 0, bool $vines = false) {
        switch($type){
            case Sapling::SPRUCE:
                $tree = new SpruceTree();
                break;
            case Sapling::BIRCH:
                if($random->nextBoundedInt(39) === 0){
                    $tree = new BirchTree(true);
                }else{
                    $tree = new BirchTree();
                }
                break;
            case Sapling::JUNGLE:
                $tree = new JungleTree();
                break;
            case Sapling::ACACIA:
                $tree = new AcaciaTree();
                break;
            case Sapling::DARK_OAK:
                return; //TODO
            default:
                if($vines) {
                    $tree = new SwampTree();
                    goto place;
                }
                $tree = new OakTree();
                if($random->nextRange(0, 9) === 0){
                    $tree = new BigOakTree($random, $level);
                }
                break;
        }
        place:
        if($tree->canPlaceObject($level, $x, $y, $z, $random)){
            $tree->placeObject($level, $x, $y, $z, $random);
        }
    }
}