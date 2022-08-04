<?php

/**
 * PocketMine Tools - Tools to simplify PocketMine plugin development
 * Copyright (C) 2022 CzechPMDevs
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

const OUTPUT_FILE = "out\MultiWorld.phar";
const WORKSPACE_DIRECTORY = "out";

const COMPOSER_DIR = "vendor";
const SOURCES_FILE = "src";
const RESOURCES_FILE = "resources";
const INCLUDED_VIRIONS = [
	"czechpmdevs/libpmform" => "czechpmdevs\\libpmform",
	"cortexpe/commando" => "CortexPE\\Commando",
	"muqsit/vanillagenerator" => "muqsit\\vanillagenerator\\generator",
	"muqsit/simplepackethandler" => "muqsit\\simplepackethandler"
];

const PLUGIN_DESCRIPTION_FILE = "plugin.yml";

chdir("..");
if(file_exists("out")) {
	out("Cleaning workspace...");
	cleanDirectory(WORKSPACE_DIRECTORY);
}

out("Building phar from sources...");

$startTime = microtime(true);

@mkdir(WORKSPACE_DIRECTORY);
@mkdir(WORKSPACE_DIRECTORY . "/" . SOURCES_FILE);;
if(RESOURCES_FILE !== "") {
	@mkdir(WORKSPACE_DIRECTORY . "/" . RESOURCES_FILE);
}

if(!is_file(PLUGIN_DESCRIPTION_FILE)) {
	out("Plugin description file not found. Cancelling the process..");
	return;
}

$pluginDescription = yaml_parse_file(PLUGIN_DESCRIPTION_FILE);
$mainClass = $pluginDescription["main"];
$splitNamespace = explode("\\", $mainClass);
$mainClass = array_pop($splitNamespace);
$pluginNamespace = implode("\\", $splitNamespace);

$sourceReplacements = [];
$virionReplacements = [];
foreach(INCLUDED_VIRIONS as $composerPath => $namespace) {
	$sourceReplacements[0][] = "use $namespace";
	$sourceReplacements[1][] = "use $pluginNamespace\\libs\\$namespace";

	$virionReplacements[$composerPath][0][] = "namespace $namespace";
	$virionReplacements[$composerPath][0][] = "use $namespace";
	$virionReplacements[$composerPath][1][] = "namespace $pluginNamespace\\libs\\$namespace";
	$virionReplacements[$composerPath][1][] = "use $pluginNamespace\\libs\\$namespace";
	foreach(INCLUDED_VIRIONS as $virionComposerPath => $virionNamespace) {
		if($composerPath === $virionComposerPath) {
			continue;
		}

		$virionReplacements[$composerPath][0][] = "use $virionNamespace";
		$virionReplacements[$composerPath][1][] = "use $pluginNamespace\\libs\\$virionNamespace";
	}
}

// Copying plugin.yml
copy(PLUGIN_DESCRIPTION_FILE, WORKSPACE_DIRECTORY . "/" . PLUGIN_DESCRIPTION_FILE);

// Copying plugin /src/...
copyDirectory(
	SOURCES_FILE,
	WORKSPACE_DIRECTORY,
	fn(string $file) => str_replace($sourceReplacements[0], $sourceReplacements[1], $file),
	fn(string $path) => $path
);

if(RESOURCES_FILE !== "") {
	copyDirectory(RESOURCES_FILE, WORKSPACE_DIRECTORY, fn(string $content) => $content, fn(string $path) => $path);
}

// Copying libraries
if(count(INCLUDED_VIRIONS) > 0) {
	out("Including libraries used...");
	foreach(INCLUDED_VIRIONS as $composerPath => $namespace) {
		out("Adding $composerPath");
		copyDirectory(
			COMPOSER_DIR . "/" . $composerPath . "/" . SOURCES_FILE,
			WORKSPACE_DIRECTORY . "/" . SOURCES_FILE . "/" . $pluginNamespace . "/libs",
			fn(string $file) => str_replace($virionReplacements[$composerPath][0], $virionReplacements[$composerPath][1], $file),
			fn(string $path) => str_replace(COMPOSER_DIR . "/" . $composerPath . "/" . SOURCES_FILE, "", $path)
		);
	}
}

out("Packing phar file...");
$phar = new Phar(OUTPUT_FILE ?? "output.phar");
$phar->buildFromDirectory(WORKSPACE_DIRECTORY);
$phar->compressFiles(Phar::GZ);

out("Done (took " . round(microtime(true) - $startTime, 3) . " seconds)");

function copyDirectory(string $directory, string $targetFolder, Closure $modifyFileClosure, Closure $modifyPathClosure): void {
	@mkdir($targetFolder, 0777, true);
	/** @var SplFileInfo $file */
	foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $file) {
		$targetPath = $modifyPathClosure($targetFolder . "/" . $file->getPath() . "/" . $file->getFilename());
		if($file->isFile()) {
			file_put_contents($targetPath, $modifyFileClosure(file_get_contents($file->getPath() . "/" . $file->getFilename())));
		} else {
			@mkdir($targetPath);
		}
	}
}

function cleanDirectory(string $directory): void {
	/** @var SplFileInfo $file */
	foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $file) {
		if($file->isFile()) {
			unlink($file->getPath() . "/" . $file->getFilename());
		} else {
			rmdir($file->getPath() . "/" . $file->getFilename());
		}
	}
}

function out(string $message): void {
	echo "[" . gmdate("H:i:s") . "] " . $message . "\n";
}