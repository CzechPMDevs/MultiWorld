<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2023  CzechPMDevs
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

namespace czechpmdevs\multiworld\tools {
	use RuntimeException;
	use function array_splice;
	use function explode;
	use function file_get_contents;
	use function file_put_contents;
	use function glob;
	use function implode;

	const TARGET_LINE = 76; // int, required
	const CONTENT = "info: '§a--- {%0} ---'"; // string, required

	const RESOURCES_DIRECTORY = "../resources/languages";

	$iterator = glob(RESOURCES_DIRECTORY . "/*.yml");
	if(!$iterator) {
		throw new RuntimeException("Could not iterate over " . RESOURCES_DIRECTORY . ".");
	}

	$cnt = 0;
	foreach($iterator as $file) {
		$contents = file_get_contents($file);
		if(!$contents) {
			throw new RuntimeException("Could not fetch file $file");
		}

		$lines = explode("\n", $contents);
		array_splice($lines, TARGET_LINE - 1, 0, CONTENT);

		file_put_contents($file, implode("\n", $lines));
		++$cnt;
	}

	echo "Line successfully added to all ($cnt) of the language files!\n";
}