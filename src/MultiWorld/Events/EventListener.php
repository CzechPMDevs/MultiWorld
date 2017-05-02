<?php

namespace MultiWorld\Events;

use MultiWorld\MultiWorld;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class EventListener implements Listener  {

    /** @var MultiWorld */
    public $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function onBreak(BlockBreakEvent $e) {
        if(in_array($e->getPlayer()->getLevel(), $this->plugin->getConfig()->get("guarded-levels"))) {
            $e->getPlayer()->sendMessage(MultiWorld::$prefix."§cYou are not allowed for this!");
            $e->setCancelled(true);
        }
    }

    public function onPlace(BlockPlaceEvent $e) {
        if(in_array($e->getPlayer()->getLevel(), $this->plugin->getConfig()->get("guarded-levels"))) {
            $e->getPlayer()->sendMessage(MultiWorld::$prefix."§cYou are not allowed for this!");
            $e->setCancelled(true);
        }
    }

    public function onTouch(PlayerInteractEvent $e) {
        if(in_array($e->getPlayer()->getLevel(), $this->plugin->getConfig()->get("guarded-levels"))) {
            $e->getPlayer()->sendMessage(MultiWorld::$prefix."§cYou are not allowed for this!");
            $e->setCancelled(true);
        }
    }
}
