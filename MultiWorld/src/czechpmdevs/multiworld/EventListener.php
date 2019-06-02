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

namespace czechpmdevs\multiworld;

use czechpmdevs\multiworld\api\WorldGameRulesAPI;
use czechpmdevs\multiworld\api\WorldManagementAPI;
use czechpmdevs\multiworld\command\MultiWorldCommand;
use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\entity\Effect;
use pocketmine\entity\Living;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Bread;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\Player;
use pocketmine\scheduler\Task;

/**
 * Class EventListener
 * @package multiworld
 */
class EventListener implements Listener {

    /** @var MultiWorld $plugin */
    public $plugin;

    /** @var MultiWorldCommand $cmd */
    private $mwCommand;

    /** @var Item[][][] $inventories */
    private $inventories = [];

    /** @var array $deathLevels */
    private $deathLevels = [];

    /**
     * EventListener constructor.
     *
     * @param MultiWorld $plugin
     * @param MultiWorldCommand $mwCommand
     */
    public function __construct(MultiWorld $plugin, MultiWorldCommand $mwCommand) {
        $this->plugin = $plugin;
        $this->mwCommand = $mwCommand;
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event) {
        WorldGameRulesAPI::updateGameRules($event->getPlayer());
    }

    /**
     * @param LevelLoadEvent $event
     */
    public function onLevelLoad(LevelLoadEvent $event) {
        WorldGameRulesAPI::handleGameRuleChange($event->getLevel(), WorldGameRulesAPI::getLevelGameRules($event->getLevel()));
    }

    /**
     * @param EntityLevelChangeEvent $event
     */
    public function onLevelChange(EntityLevelChangeEvent $event) {
        $entity = $event->getEntity();
        if($entity instanceof Player) {
            WorldGameRulesAPI::updateGameRules($entity, $event->getTarget());

            $originGenerator = $event->getOrigin()->getProvider()->getGenerator();
            $targetGenerator = $event->getTarget()->getProvider()->getGenerator();

            $getDimension = function ($generator): int {
                switch ($generator) {
                    case "normal":
                    case "skyblock":
                    case "void":
                        return 0;
                    case "nether":
                        return 1;
                    case "ender":
                        return 2;
                    default:
                        return 0;
                }
            };

            if($getDimension($originGenerator) == $getDimension($targetGenerator)) return;

            $pk = new ChangeDimensionPacket();
            $pk->dimension = $getDimension($targetGenerator);
            $pk->position = $event->getTarget()->getSpawnLocation();

            $entity->dataPacket($pk);
        }
    }

    /**
     * @param EntityDeathEvent $event
     */
    public function onEntityDeath(EntityDeathEvent $event) {
        $entity = $event->getEntity();
        $levelGameRules = WorldGameRulesAPI::getLevelGameRules($entity->getLevel());
        if(isset($levelGameRules["doMobLoot"]) && !$levelGameRules["doMobLoot"][1] && !$entity instanceof Player) {
            $event->setDrops([]);
        }
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function onPlayerDeath(PlayerDeathEvent $event) {
        $player = $event->getPlayer();

        $levelGameRules = WorldGameRulesAPI::getLevelGameRules($player->getLevel());
        if(isset($levelGameRules["keepInventory"]) && $levelGameRules["keepInventory"][1]) {
            $this->inventories[$player->getName()] = [$player->getInventory()->getContents(), $player->getArmorInventory()->getContents(), $player->getCursorInventory()->getContents()];
            $event->setDrops([]);
        }

        $getDimension = function ($generator): int {
            switch ($generator) {
                case "normal":
                case "skyblock":
                case "void":
                    return 0;
                case "nether":
                    return 1;
                case "ender":
                    return 2;
                default:
                    return 0;
            }
        };

        if($getDimension($player->getLevel()->getProvider()->getGenerator()) !== 0) {
            $player->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn());
            return;
        }
    }

    /**
     * @param PlayerRespawnEvent $event
     */
    public function onPlayerRespawn(PlayerRespawnEvent $event) {
        $player = $event->getPlayer();
        $levelGameRules = WorldGameRulesAPI::getLevelGameRules($player->getLevel());
        if(isset($levelGameRules["keepInventory"]) && $levelGameRules["keepInventory"][1] && isset($this->inventories[$player->getName()])) {
            $player->getInventory()->setContents(array_shift($this->inventories[$player->getName()]));
            $player->getArmorInventory()->setContents(array_shift($this->inventories[$player->getName()]));
            $player->getCursorInventory()->setContents(array_shift($this->inventories[$player->getName()]));
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        $levelGameRules = WorldGameRulesAPI::getLevelGameRules($player->getLevel());
        if(isset($levelGameRules["doTileDrops"]) && !$levelGameRules["doTileDrops"][1]) {
            $event->setDrops([]);
        }
    }

    /**
     * @param EntityRegainHealthEvent $event
     */
    public function onRegenerate(EntityRegainHealthEvent $event) {
        $entity = $event->getEntity();
        if(!$entity instanceof Living) return;
        if($entity->hasEffect(Effect::REGENERATION)) return;

        $levelGameRules = WorldGameRulesAPI::getLevelGameRules($entity->getLevel());
        if(isset($levelGameRules["naturalRegeneration"]) && !$levelGameRules["naturalRegeneration"][1]) {
            $event->setCancelled(true);
        }
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event) {
        $entity = $event->getEntity();

        if(!$event instanceof EntityDamageByEntityEvent) return;

        if($event->getEntity()->getLevel() instanceof Level) {
            $levelGameRules = WorldGameRulesAPI::getLevelGameRules($entity->getLevel());
            if(isset($levelGameRules["pvp"]) && !$levelGameRules["pvp"][1]) {
                $event->setCancelled(true);
            }
        }
    }

    /**
     * @param EntityExplodeEvent $event
     */
    public function onExplode(EntityExplodeEvent $event) {
        $entity = $event->getEntity();

        $levelGameRules = WorldGameRulesAPI::getLevelGameRules($entity->getLevel());
        if(isset($levelGameRules["tntexplodes"]) && !$levelGameRules["tntexplodes"][1]) {
            $event->setCancelled(true);
        }
    }



    /**
     * @param DataPacketReceiveEvent $event
     */
    public function onDataPacketReceive(DataPacketReceiveEvent $event) {
        $packet = $event->getPacket();
        if($packet instanceof LoginPacket) {
            LanguageManager::$players[$packet->username] = $packet->locale;
        }
    }
}