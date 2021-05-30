<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2020  CzechPMDevs
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

use czechpmdevs\multiworld\gamerules\GameRules;
use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\entity\Effect;
use pocketmine\entity\Living;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\level\LevelUnloadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\Player;

class EventListener implements Listener {

    /** @var MultiWorld */
    public MultiWorld $plugin;
    /** @var Item[][][] */
    private array $inventories = [];

    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event): void {
        MultiWorld::getGameRules($event->getPlayer()->getLevelNonNull())->applyToPlayer($event->getPlayer());
    }

    public function onLevelLoad(LevelLoadEvent $event): void {
        MultiWorld::getGameRules($event->getLevel());
    }

    public function onLevelUnload(LevelUnloadEvent $event): void {
        MultiWorld::unloadLevel($event->getLevel());
    }

    public function onLevelChange(EntityLevelChangeEvent $event): void {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            MultiWorld::getGameRules($event->getTarget())->applyToPlayer($entity);

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

            if ($getDimension($originGenerator) == $getDimension($targetGenerator)) return;

            $pk = new ChangeDimensionPacket();
            $pk->dimension = $getDimension($targetGenerator);
            $pk->position = $event->getTarget()->getSpawnLocation();

            $entity->dataPacket($pk);
        }
    }

    public function onPlayerDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();

        if (MultiWorld::getGameRules($player->getLevelNonNull())->getBool(GameRules::GAMERULE_KEEP_INVENTORY)) {
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

        if ($getDimension($player->getLevel()->getProvider()->getGenerator()) !== 0) {
            $player->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn());
        }
    }

    public function onPlayerRespawn(PlayerRespawnEvent $event): void {
        $player = $event->getPlayer();
        if (MultiWorld::getGameRules($event->getPlayer()->getLevelNonNull())->getBool(GameRules::GAMERULE_KEEP_INVENTORY) && isset($this->inventories[$player->getName()])) {
            $player->getInventory()->setContents(array_shift($this->inventories[$player->getName()]));
            $player->getArmorInventory()->setContents(array_shift($this->inventories[$player->getName()]));
            $player->getCursorInventory()->setContents(array_shift($this->inventories[$player->getName()]));
        }
    }

    public function onBreak(BlockBreakEvent $event): void {
        if (!MultiWorld::getGameRules($event->getPlayer()->getLevelNonNull())->getBool(GameRules::GAMERULE_DO_TILE_DROPS)) {
            $event->setDrops([]);
        }
    }

    public function onRegenerate(EntityRegainHealthEvent $event): void {
        $entity = $event->getEntity();
        if (!$entity instanceof Living) return;
        if ($entity->hasEffect(Effect::REGENERATION)) return;

        if (!MultiWorld::getGameRules($entity->getLevelNonNull())->getBool(GameRules::GAMERULE_NATURAL_REGENERATION)) {
            $event->setCancelled(true);
        }
    }

    public function onDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();

        if($entity instanceof Player && $event instanceof EntityDamageByEntityEvent && $event->getDamager() instanceof Player && !MultiWorld::getGameRules($event->getEntity()->getLevelNonNull())->getBool(GameRules::GAMERULE_PVP)) {
            $event->setCancelled();
        }
    }

    public function onExplode(EntityExplodeEvent $event): void {
        if(!MultiWorld::getGameRules($event->getEntity()->getLevelNonNull())->getBool(GameRules::GAMERULE_TNT_EXPLODES)) {
            $event->setCancelled();
        }
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event): void {
        $packet = $event->getPacket();
        if ($packet instanceof LoginPacket) {
            LanguageManager::$players[$packet->username] = $packet->locale;
        }
    }
}