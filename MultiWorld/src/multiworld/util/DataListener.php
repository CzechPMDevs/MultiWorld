<?php

declare(strict_types=1);

namespace multiworld\util;

use multiworld\MultiWorld;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\Server;

/**
 * Class DataListener
 * @package multiworld\util
 */
class DataListener implements Listener {

    /** @var  Data $data */
    public $data;

    /**
     * DataListener constructor.
     * @param Data $data
     */
    public function __construct(Data $data) {
        $this->data = $data;
        Server::getInstance()->getPluginManager()->registerEvents($this, $this->getPlugin());
        $this->getPlugin()->getLogger()->info("Data listener loaded (level ".$this->getData()->getLevelName()." )");
    }

    /**
     * @param PlayerCommandPreprocessEvent $event
     */
    public function onCommandPreprocess(PlayerCommandPreprocessEvent $event) {
        if(!in_array($this->getData()->getLevelName(), [$event->getPlayer()->getLevel()->getName(), $event->getPlayer()->getLevel()->getFolderName()])) return;
        if($this->getData()->getAllowCommands()) return;
        if($event->getPlayer()->hasPermission("mw.world.allowcommands")) return;
        if(strpos($event->getMessage(), "/") === 0) $event->setCancelled(true);
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event) {
        if(!in_array($this->getData()->getLevelName(), [$event->getPlayer()->getLevel()->getName(), $event->getPlayer()->getLevel()->getFolderName()])) return;
        if($this->getData()->getAllowEditWorld()) return;
        if(!$event->getPlayer()->hasPermission("mw.world.edit")) $event->setCancelled(true);
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event) {
        if(!in_array($this->getData()->getLevelName(), [$event->getPlayer()->getLevel()->getName(), $event->getPlayer()->getLevel()->getFolderName()])) return;
        if($this->data->getAllowEditWorld()) return;
        if(!$event->getPlayer()->hasPermission("mw.world.edit")) $event->setCancelled(true);
    }


    /**
     * @return MultiWorld
     */
    public function getPlugin():MultiWorld {
        return MultiWorld::getInstance();
    }

    /**
     * @return Data
     */
    public function getData():Data {
        return $this->data;
    }
}