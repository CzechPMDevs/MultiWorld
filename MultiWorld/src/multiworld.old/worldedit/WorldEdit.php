<?php

declare(strict_types=1);

namespace multiworld\worldedit;

use multiworld\MultiWorld;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

/**
 * Class WorldEdit
 * @package multiworld\WorldEdit
 */
class WorldEdit {

    /** @var  MultiWorld $plugin */
    public $plugin;

    /** @var  array $pos */
    public $pos1 = [];

    /** @var array $pos2 */
    public $pos2 = [];

    /** @var array $level */
    public $level = [];

    /**
     * WorldEdit constructor.
     * @param MultiWorld $plugin
     */
    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param Vector3 $pos1
     * @param Vector3 $pos2
     * @param Level $level
     * @param string $blocks
     * @return int
     */
    public function fill(Vector3 $pos1, Vector3 $pos2, Level $level, string $blocks) {
        $count = 0;
        $minPos = new Vector3(min($pos1->getX(), $pos2->getX()), min($pos1->getY(), $pos2->getY()), min($pos1->getZ(), $pos2->getZ()));
        $maxPos = new Vector3(max($pos1->getX(), $pos2->getX()), max($pos1->getY(), $pos2->getY()), max($pos1->getZ(), $pos2->getZ()));
        for($x = $minPos->x; $x <= $maxPos->x; $x++) {
            for($y = $minPos->y; $y <= $maxPos->y; $y++) {
                for($z = $minPos->z; $z <= $maxPos->z; $z++) {
                    $blockArray = explode(",", $blocks);
                    $item = Item::fromString($blockArray[array_rand($blockArray, 1)]);
                    $vec = new Vector3($x, $y, $z);
                    $level->setBlock($vec, $item->getBlock());
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * @param Player $player
     * @param $blocks
     */
    public function startFill(Player $player, $blocks) {
        $pos1 = $this->pos1[$player->getName()];
        $pos2 = $this->pos2[$player->getName()];
        $level = $this->level[$player->getName()];
        if(!($pos1 instanceof Vector3) && !($pos2 instanceof Vector3) && !($level instanceof Level)) {
            $player->sendMessage("§cNO VECTOR");
            return;
        }
        $count = $this->fill($pos1, $pos2, $level, $blocks);
        $player->sendMessage("§aFilled ($count block changed).");
    }

    /**
     * @param $block
     * @return Block
     */
    public function getBlock($block):Block {
        $blocks = explode(",", $block);
        $item = Item::fromString(strval($blocks[array_rand($blocks, 1)]));
        return Block::get($item->getId(), $item->getDamage());
    }

    /**
     * @param Player $player
     * @param Position $position
     * @param int $pos
     */
    public function selectPos(Player $player, Position $position, int $pos) {
        if(strval($pos) == "1") {
            $this->pos1[$player->getName()] = $vec = new Vector3(intval($position->getX()), intval($position->getY()), intval($position->getZ()));
            $player->sendMessage("§aSelected first position at §b{$vec->getX()}, {$vec->getY()}, {$vec->getZ()}");
        }
        elseif(strval($pos) == "2") {
            $this->pos2[$player->getName()] = $vec = new Vector3(intval($position->getX()), intval($position->getY()), intval($position->getZ()));
            $player->sendMessage("§aSelected second position at §b{$vec->getX()}, {$vec->getY()}, {$vec->getZ()}");
        }
        $this->level[$player->getName()] = $position->getLevel();
    }
}