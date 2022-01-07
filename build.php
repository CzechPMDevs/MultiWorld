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

// For example C:/pmmp-server/plugins/MultiWorld.phar
const CUSTOM_OUTPUT_PATH = "../../Plocha/pmmp/plugins/MultiWorld.phar";
const COMPRESS_FILES = true;
const COMPRESSION = Phar::GZ;

$startTime = microtime(true);

// Input & Output directory...
$from = getcwd() . DIRECTORY_SEPARATOR;
$to = getcwd() . DIRECTORY_SEPARATOR . "out" . DIRECTORY_SEPARATOR . "MultiWorld" . DIRECTORY_SEPARATOR;

@mkdir($to, 0777, true);

// Clean output directory...
cleanDirectory($to);

// Copying new files...
copyDirectory($from . "src", $to . "src");
copyDirectory($from . "resources", $to . "resources");
copyDirectory($from . "vendor/czechpmdevs/libpmform/src", $to . "src/");

$description = (array)yaml_parse_file($from . "plugin.yml");
yaml_emit_file($to . "plugin.yml", $description);

$buildVersion = strval($description["version"] ?? "unknown");

// Defining output path...
$outputPath = CUSTOM_OUTPUT_PATH === "" ?
	getcwd() . DIRECTORY_SEPARATOR . "out" . DIRECTORY_SEPARATOR . "MultiWorld_{$buildVersion}_.phar" :
	CUSTOM_OUTPUT_PATH;

@unlink($outputPath);

// Generate phar
$phar = new Phar($outputPath);
$phar->buildFromDirectory($to);

if(COMPRESS_FILES) {
    $phar->compressFiles(COMPRESSION);
}

printf("Plugin built in %s seconds! Output path: %s\n", round(microtime(true) - $startTime, 3), $outputPath);

function copyDirectory(string $from, string $to): void {
    @mkdir($to, 0777, true);

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
    /** @var SplFileInfo $fileInfo */
    foreach ($files as $fileInfo) {
        $target = str_replace($from, $to, $fileInfo->getPathname());
        if($fileInfo->isDir()) {
            @mkdir($target, 0777, true);
        } else {
            $contents = file_get_contents($fileInfo->getPathname());
            file_put_contents($target, $contents);
        }
    }
}

function cleanDirectory(string $directory): void {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    /** @var SplFileInfo $fileInfo */
    foreach ($files as $fileInfo) {
        if($fileInfo->isDir()) {
            rmdir($fileInfo->getPathname());
        } else {
            unlink($fileInfo->getPathname());
        }
    }
}