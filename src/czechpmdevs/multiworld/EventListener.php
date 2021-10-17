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

use czechpmdevs\multiworld\session\PlayerInventorySession;
use czechpmdevs\multiworld\world\dimension\Dimension;
use czechpmdevs\multiworld\world\gamerules\GameRule;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Living;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\event\world\WorldUnloadEvent;
use pocketmine\network\mcpe\protocol\SettingsCommandPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use function array_key_exists;
use function substr;

class EventListener implements Listener {

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
		MultiWorld::getGameRules($event->getPlayer()->getWorld())->applyToPlayer($event->getPlayer());
	}

	/** @noinspection PhpUnused */
	public function onQuit(PlayerQuitEvent $event): void {
		unset($this->deathSessions[$event->getPlayer()->getName()]);
		unset($this->dimensionData[$event->getPlayer()->getName()]);
	}

	/** @noinspection PhpUnused */
	public function onWorldLoad(WorldLoadEvent $event): void {
		MultiWorld::getGameRules($event->getWorld());
	}

	/** @noinspection PhpUnused */
	public function onWorldUnload(WorldUnloadEvent $event): void {
		MultiWorld::unloadWorld($event->getWorld());
	}

	/** @noinspection PhpUnused */
	public function onWorldChange(EntityTeleportEvent $event): void {
		$player = $event->getEntity();
		if (!$player instanceof Player) {
			return;
		}
		if ($event->getFrom()->getWorld()->getId() == $event->getTo()->getWorld()->getId()) {
			return;
		}

		MultiWorld::getGameRules($event->getTo()->getWorld())->applyToPlayer($player);

		if (Dimension::getDimensionByWorld($event->getFrom()->getWorld()) != ($targetDimension = Dimension::getDimensionByWorld($event->getTo()->getWorld())) && $this->plugin->getConfig()->get("handle-dimensions")) {
			Dimension::sendDimensionToPlayer($player, $targetDimension);
		}
	}

	/** @noinspection PhpUnused */
	public function onPlayerDeath(PlayerDeathEvent $event): void {
		$player = $event->getPlayer();

		if (MultiWorld::getGameRules($player->getWorld())->getRule(GameRule::KEEP_INVENTORY())->getValue()) {
			$this->deathSessions[$player->getId()] = new PlayerInventorySession($player);
			$event->setDrops([]);
		}

		if ($this->plugin->getConfig()->get("handle-dimensions")) {
			$this->dimensionData[$player->getId()] = Dimension::getDimensionByWorld($player->getWorld());
		}
	}

	/** @noinspection PhpUnused */
	public function onPlayerRespawn(PlayerRespawnEvent $event): void {
		$player = $event->getPlayer();

		if (array_key_exists($player->getId(), $this->dimensionData) && $this->dimensionData[$player->getId()] != ($currentDimension = Dimension::getDimensionByWorld($player->getWorld()))) {
			Dimension::sendDimensionToPlayer($player, $currentDimension, true);
		}
		unset($this->dimensionData[$player->getId()]);

		if (array_key_exists($player->getId(), $this->deathSessions)) {
			$this->deathSessions[$player->getId()]->close();
			unset($this->deathSessions[$player->getId()]);
		}
	}

	/** @noinspection PhpUnused */
	public function onBreak(BlockBreakEvent $event): void {
		if (!MultiWorld::getGameRules($event->getPlayer()->getWorld())->getRule(GameRule::DO_TILE_DROPS())->getValue()) {
			$event->setDrops([]);
		}
	}

	/** @noinspection PhpUnused */
	public function onRegenerate(EntityRegainHealthEvent $event): void {
		$entity = $event->getEntity();
		if (!$entity instanceof Living) return;
		if ($entity->getEffects()->has(VanillaEffects::REGENERATION())) return;

		if (!MultiWorld::getGameRules($entity->getWorld())->getRule(GameRule::NATURAL_REGENERATION())->getValue()) {
			$event->cancel();
		}
	}

	/** @noinspection PhpUnused */
	public function onDamage(EntityDamageEvent $event): void {
		$entity = $event->getEntity();

		if (
			$entity instanceof Player &&
			$event instanceof EntityDamageByEntityEvent &&
			$event->getDamager() instanceof Player &&
			!MultiWorld::getGameRules($event->getEntity()->getWorld())->getRule(GameRule::PVP())->getValue()
		) {
			$event->cancel();
		}
	}

	/** @noinspection PhpUnused */
	public function onExplode(EntityExplodeEvent $event): void {
		if (!MultiWorld::getGameRules($event->getEntity()->getWorld())->getRule(GameRule::TNT_EXPLODES())->getValue()) {
			$event->cancel();
		}
	}

	/** @noinspection PhpUnused */
	public function onDataPacketReceive(DataPacketReceiveEvent $event): void {
		$packet = $event->getPacket();

		// Changing game rules from the menu
		if ($packet instanceof SettingsCommandPacket) {
			$player = $event->getOrigin()->getPlayer();
			if ($player === null) {
				return;
			}

			Server::getInstance()->dispatchCommand($player, substr($packet->getCommand(), 1));
		}
	}
}