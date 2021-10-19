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

namespace czechpmdevs\multiworld\session;

use pocketmine\item\Item;
use pocketmine\Player;

class PlayerInventorySession {

	/** @var Player */
	public Player $player;

	/** @var Item[] */
	public array $inventory;
	/** @var Item[] */
	public array $armorInventory;
	/** @var Item[] */
	public array $cursorInventory;

	/** @var int */
	public int $experienceLevel;
	/** @var float */
	public float $experience;

	public function __construct(Player $player) {
		$this->player = $player;

		$this->inventory = $player->getInventory()->getContents();
		$this->armorInventory = $player->getArmorInventory()->getContents();
		$this->cursorInventory = $player->getCursorInventory()->getContents();

		$this->experienceLevel = $player->getXpLevel();
		$this->experience = $player->getXpProgress();
	}

	public function close(): void {
		if(!$this->player->isOnline()) {
			return;
		}

		$this->player->getInventory()->setContents($this->inventory);
		$this->player->getArmorInventory()->setContents($this->armorInventory);
		$this->player->getCursorInventory()->setContents($this->cursorInventory);

		$this->player->setXpLevel($this->experienceLevel);
		$this->player->setXpProgress($this->experience);
	}
}