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

namespace czechpmdevs\multiworld;

use czechpmdevs\multiworld\level\dimension\Dimension;
use czechpmdevs\multiworld\level\gamerules\GameRules;
use czechpmdevs\multiworld\session\PlayerInventorySession;
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
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\Player;

class EventListener implements Listener {

    /** @var MultiWorld */
    public MultiWorld $plugin;

    /** @var PlayerInventorySession[] $deathSessions */
    protected array $deathSessions = [];
    /** @var int[] */
    protected array $dimensionData = [];

    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
    }

    /** @noinspection PhpUnused */
    public function onJoin(PlayerJoinEvent $event): void {
        MultiWorld::getGameRules($event->getPlayer()->getLevelNonNull())->applyToPlayer($event->getPlayer());
    }

    /** @noinspection PhpUnused */
    public function onQuit(PlayerQuitEvent $event): void {
        unset($this->deathSessions[$event->getPlayer()->getName()]);
        unset($this->dimensionData[$event->getPlayer()->getName()]);
    }

    /** @noinspection PhpUnused */
    public function onLevelLoad(LevelLoadEvent $event): void {
        MultiWorld::getGameRules($event->getLevel());
    }

    /** @noinspection PhpUnused */
    public function onLevelUnload(LevelUnloadEvent $event): void {
        MultiWorld::unloadLevel($event->getLevel());
    }

    /** @noinspection PhpUnused */
    public function onLevelChange(EntityLevelChangeEvent $event): void {
        $player = $event->getEntity();
        if (!$player instanceof Player) {
            return;
        }

        MultiWorld::getGameRules($event->getTarget())->applyToPlayer($player);

        if(Dimension::getDimensionByLevel($event->getOrigin()) != ($targetDimension = Dimension::getDimensionByLevel($event->getTarget()))) {
            Dimension::sendDimensionToPlayer($player, $targetDimension);
        }
    }

    /** @noinspection PhpUnused */
    public function onPlayerDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();

        if (MultiWorld::getGameRules($player->getLevelNonNull())->getBool(GameRules::GAMERULE_KEEP_INVENTORY)) {
            $this->deathSessions[$player->getId()] = new PlayerInventorySession($player);
            $event->setDrops([]);
        }

        $this->dimensionData[$player->getId()] = Dimension::getDimensionByLevel($player->getLevelNonNull());
    }

    /** @noinspection PhpUnused */
    public function onPlayerRespawn(PlayerRespawnEvent $event): void {
        $player = $event->getPlayer();

        if($this->dimensionData[$player->getId()] != ($currentDimension = Dimension::getDimensionByLevel($player->getLevelNonNull()))) {
            Dimension::sendDimensionToPlayer($player, $currentDimension, true);
        }
        unset($this->dimensionData[$player->getId()]);

        if(isset($this->deathSessions[$player->getId()])) {
            $this->deathSessions[$player->getId()]->close();
            unset($this->deathSessions[$player->getId()]);
        }
    }

    /** @noinspection PhpUnused */
    public function onBreak(BlockBreakEvent $event): void {
        if (!MultiWorld::getGameRules($event->getPlayer()->getLevelNonNull())->getBool(GameRules::GAMERULE_DO_TILE_DROPS)) {
            $event->setDrops([]);
        }
    }

    /** @noinspection PhpUnused */
    public function onRegenerate(EntityRegainHealthEvent $event): void {
        $entity = $event->getEntity();
        if (!$entity instanceof Living) return;
        if ($entity->hasEffect(Effect::REGENERATION)) return;

        if (!MultiWorld::getGameRules($entity->getLevelNonNull())->getBool(GameRules::GAMERULE_NATURAL_REGENERATION)) {
            $event->setCancelled(true);
        }
    }

    /** @noinspection PhpUnused */
    public function onDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();

        if($entity instanceof Player && $event instanceof EntityDamageByEntityEvent && $event->getDamager() instanceof Player && !MultiWorld::getGameRules($event->getEntity()->getLevelNonNull())->getBool(GameRules::GAMERULE_PVP)) {
            $event->setCancelled();
        }
    }

    /** @noinspection PhpUnused */
    public function onExplode(EntityExplodeEvent $event): void {
        if(!MultiWorld::getGameRules($event->getEntity()->getLevelNonNull())->getBool(GameRules::GAMERULE_TNT_EXPLODES)) {
            $event->setCancelled();
        }
    }

    /** @noinspection PhpUnused */
    public function onDataPacketReceive(DataPacketReceiveEvent $event): void {
        $packet = $event->getPacket();
        if ($packet instanceof LoginPacket) {
            LanguageManager::$players[$packet->username] = $packet->locale;
        }
    }
}