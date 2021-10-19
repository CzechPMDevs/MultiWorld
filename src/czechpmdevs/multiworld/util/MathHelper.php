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

namespace czechpmdevs\multiworld\util;

use function sin;

class MathHelper {

	/** @var MathHelper */
	private static MathHelper $instance;

	/** @var float[] */
	private array $sinTable = [];

	private function __construct() {
		for($i = 0; $i < 65536; ++$i) {
			$this->sinTable[$i] = sin((float)$i * M_PI * 2.0 / 65536.0);
		}
	}

	public function sin(float $num): float {
		return $this->sinTable[(int)($num * 10430.378) & 0xffff];
	}

	public function cos(float $num): float {
		return $this->sinTable[(int)($num * 10430.378 + 16384.0) & 0xffff];
	}

	public static function getInstance(): MathHelper {
		return MathHelper::$instance ?? MathHelper::$instance = new MathHelper();
	}
}