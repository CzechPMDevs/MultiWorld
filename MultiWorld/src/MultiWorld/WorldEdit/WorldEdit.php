<?php

namespace MultiWorld\WorldEdit;

use MultiWorld\MultiWorld;
use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

/**
 * Class WorldEdit
 * @package MultiWorld\WorldEdit
 */
class WorldEdit {

    /** @var  MultiWorld $plugin */
    public $plugin;

    /** @var  array $pos */
    public $pos;

    /**
     * WorldEdit constructor.
     * @param MultiWorld $plugin
     */
    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param Player $player
     * @param $block
     */
    public function fill(Player $player, $block) {
        $pos1 = $this->getPos($player, 1);
        $pos2 = $this->getPos($player, 2);
        if($pos1 instanceof Position && $pos2 instanceof Position) {
            if($pos1->getLevel()->getName() == $pos2->getLevel()->getName()) {
                $pos1 = new Position(min($pos1->getX(), $pos2->getX()), min($pos1->getY(), $pos2->getY()), min($pos1->getZ(), $pos2->getZ()), $pos1->getLevel());
                $pos2 = new Position(max($pos1->getX(), $pos2->getX()), max($pos1->getY(), $pos2->getY()), max($pos1->getZ(), $pos2->getZ()), $pos2->getLevel());
                $x1 = $pos1->getX(); $y1 = $pos1->getY(); $z1 = $pos1->getZ();
                $x2 = $pos2->getX(); $y2 = $pos2->getY(); $z2 = $pos2->getZ();
                for($x = $x1; $x < $x2; $x++) {
                    for($z = $z1; $z < $z2; $z++) {
                        for($y = $y1; $y < $y2; $y++) {
                            $this->getBlock($block);
                            $pos1->getLevel()->setBlock(new Vector3($x, $y, $z), Block::get(Block::STONE));
                        }
                    }
                }
                $player->sendMessage("§aFillded.");
            }
            else {
                $player->sendMessage("§cPositions must be in same level.");
            }
        }
        else {
            $player->sendMessage("§cSelect position first");
        }
    }

    /**
     * @param $block
     * @return Block
     */
    public function getBlock($block):Block {
        if (is_numeric($block)) {
            return Block::get(intval($block));
        } else {
            if (is_string($block)) {
                $id = explode(",", $block);
                $index = array_rand($id, 1);
                $arg = explode(":", $id[$index]);
                try {
                    return Block::get($arg[0],$arg[1]);
                }
                catch (\Exception $exception) {
                    $this->plugin->getLogger()->debug("§cSome bug");
                    return Block::get(Block::AIR);
                }
            }
        }
    }

    /**
     * @param Player $player
     * @param Position $position
     * @param int $pos
     */
    public function selectPos(Player $player, Position $position, int $pos) {
        $this->pos["{$pos}"][strtolower($player->getName())] = $position;
        var_dump($this->pos["{$pos}"][strtolower($player->getName())]);
        $player->sendMessage("§aSelected");
    }

    /**
     * @param Player $player
     * @param int $pos
     * @return Position|null
     */
    public function getPos(Player $player, int $pos) {
        var_dump($this->pos["{$pos}"][strtolower($player->getName())]);
        return $this->pos["{$pos}"][strtolower($player->getName())];
    }
}