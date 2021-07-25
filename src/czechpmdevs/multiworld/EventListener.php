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
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\event\world\WorldUnloadEvent;
use pocketmine\network\mcpe\protocol\SettingsCommandPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\SpawnSettings;
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

	public function onJoin(PlayerJoinEvent $event) : void {
		$player = $event->getPlayer();
		MultiWorld::getGameRules($player->getWorld())->applyToPlayer($player);
		LanguageManager::$players[spl_object_hash($player)] = $player->getLocale();
	}

	public function onQuit(PlayerQuitEvent $event) : void {
		$name = $event->getPlayer()->getName();
		unset($this->deathSessions[$name], $this->dimensionData[$name], LanguageManager::$players[spl_object_hash($event->getPlayer())]);
	}

	public function onWorldLoad(WorldLoadEvent $event) : void {
		MultiWorld::getGameRules($event->getWorld());
	}

	public function onWorldUnload(WorldUnloadEvent $event) : void {
		MultiWorld::unloadWorld($event->getWorld());
	}

	public function onWorldChange(EntityTeleportEvent $event) : void {
		if ($event->getFrom()->getWorld() !== $event->getTo()->getWorld()) {
			$player = $event->getEntity();
			if (!$player instanceof Player) {
				return;
			}

			MultiWorld::getGameRules($event->getTo()->getWorld())->applyToPlayer($player);

			if ($this->plugin->getConfig()->get("handle-dimensions") && Dimension::getDimensionByWorld($event->getFrom()->getWorld()) !== ($targetDimension = Dimension::getDimensionByWorld($event->getTo()->getWorld()))) {
				Dimension::sendDimensionToPlayer($player, $targetDimension);
			}
		}
	}

	public function onPlayerDeath(PlayerDeathEvent $event) : void {
		$player = $event->getPlayer();

		if (MultiWorld::getGameRules($player->getWorld())->getBool(GameRules::GAMERULE_KEEP_INVENTORY)) {
			$this->deathSessions[$player->getId()] = new PlayerInventorySession($player);
			$event->setDrops([]);
		}

		if ($this->plugin->getConfig()->get("handle-dimensions")) {
			$this->dimensionData[$player->getId()] = Dimension::getDimensionByWorld($player->getWorld());
		}
	}

	public function onPlayerRespawn(PlayerRespawnEvent $event) : void {
		$player = $event->getPlayer();

		if (array_key_exists($player->getId(), $this->dimensionData) && $this->dimensionData[$player->getId()] !== ($currentDimension = Dimension::getDimensionByWorld($player->getWorld()))) {
			Dimension::sendDimensionToPlayer($player, $currentDimension, true);
		}
		unset($this->dimensionData[$player->getId()]);

		if (array_key_exists($player->getId(), $this->deathSessions)) {
			$this->deathSessions[$player->getId()]->close();
			unset($this->deathSessions[$player->getId()]);
		}
	}

	public function onBreak(BlockBreakEvent $event) : void {
		if (!MultiWorld::getGameRules($event->getPlayer()->getWorld())->getBool(GameRules::GAMERULE_DO_TILE_DROPS)) {
			$event->setDrops([]);
		}
	}

	public function onRegenerate(EntityRegainHealthEvent $event) : void {
		$entity = $event->getEntity();
		if (!$entity instanceof Living) {
			return;
		}

		if ($entity->getEffects()->has(VanillaEffects::REGENERATION())) {
			return;
		}

		if (!MultiWorld::getGameRules($entity->getWorld())->getBool(GameRules::GAMERULE_NATURAL_REGENERATION)) {
			$event->cancel();
		}
	}

	public function onDamage(EntityDamageEvent $event) : void {
		$entity = $event->getEntity();

		if (
			$entity instanceof Player &&
			$event instanceof EntityDamageByEntityEvent &&
			$event->getDamager() instanceof Player &&
			!MultiWorld::getGameRules($event->getEntity()->getWorld())->getBool(GameRules::GAMERULE_PVP)
		) {
			$event->cancel();
		}
	}

	public function onExplode(EntityExplodeEvent $event) : void {
		if (!MultiWorld::getGameRules($event->getEntity()->getWorld())->getBool(GameRules::GAMERULE_TNT_EXPLODES)) {
			$event->cancel();
		}
	}

	public function onDataPacketSend(DataPacketSendEvent $event) : void {
		$packets = $event->getPackets();
		// assume targets are in the same world or the StartGamePacket will never be the same, but i think that this packet is unique
		$world = $event->getTargets()[array_key_first($event->getTargets())]->getPlayer()->getWorld();
		foreach ($packets as $packet) {
			if ($packet instanceof StartGamePacket && $this->plugin->getConfig()->get("handle-dimensions")) {
				$packet->spawnSettings = new SpawnSettings($packet->spawnSettings->getBiomeType(), $packet->spawnSettings->getBiomeName(), Dimension::getDimensionByWorld($world));
			}
		}
	}

	public function onDataPacketReceive(DataPacketReceiveEvent $event) : void {
		$packet = $event->getPacket();
		// Changing game rules from the menu
		if ($packet instanceof SettingsCommandPacket) {
			$player = $event->getOrigin()->getPlayer();
			if ($player !== null) {
				Server::getInstance()->dispatchCommand($player, substr($packet->getCommand(), 1));
			}
		}
	}
}