<?php

/**
 * Copyright (C) 2018-2023  CzechPMDevs
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

const CRASH_ARCHIVE_URL = "https://crash.pmmp.io";
const PLUGIN_NAME = "MultiWorld"; // required
const MIN_PLUGIN_VERSION = "2.1.0-beta2"; // required
const QUERY_URL = CRASH_ARCHIVE_URL . "/list?cause=plugin&cause=plugin_indirect&page=%page&plugin=" . PLUGIN_NAME;

$currentPage = 1;
$totalPages = null;

$crashesDuplicatesCount = [];
$crashesMessagesWithLinks = [];
do {
	file_put_contents("temp.html", $html = file_get_contents(str_replace("%page", (string)($currentPage++), QUERY_URL)));
	if(!is_string($html)) {
		out("Unable to fetch data from crash archive.");
		return;
	}

	if($totalPages === null) {
		$matchCount = preg_match_all("/Showing \d*-\d* \((\d*) reports\) of (\d*) reports/", $html, $matches);
		$totalPages = ceil(((float)$matches[2][0]) / ((float)$matches[1][0]));
	}

	$matchCount = preg_match_all("/<tr class=\"\w* lighten-4\">\n\s*<td class=\"link-table-cell\" style=\"white-space: nowrap\"><a href=\"([\/\\w]*)\">/", $html, $matches);
	$crashUrls = $matches[1];
	for($i = 0; $i < $matchCount; ++$i) {
		$crashUrl = $crashUrls[$i];
		if(!($crashHtml = @file_get_contents($fullCrashUrl = CRASH_ARCHIVE_URL . $crashUrl))) {
			out("Unknown address $fullCrashUrl, skipping...\n");
			continue;
		}

		preg_match_all("/<title>([#\w\s()\\\:\$\-._\/,=&;~!|{}\[\]]*)<\/title>/u", $crashHtml, $matches);
		$message = (string)($matches[1][0] ?? "");
		if($message === "") {
			file_put_contents("invalid.html", $crashHtml);
			out("Crash message contains invalid characters");
			return;
		}

		$message = preg_replace("/#?\d+:?/", "%num", $message); // Replace numbers so we can remove duplicates
		if($message === null) {
			out("Invalid pattern specified for replacing numbers in string");
			continue;
		}

		preg_match_all("/<td>" . PLUGIN_NAME . "<\/td>\s*<td>([\d.\-\w]*)<\/td>/", $crashHtml, $matches);
		$version = (string)$matches[1][0];
		if(version_compare($version, MIN_PLUGIN_VERSION) < 0) {
			out("Skipping error caused by outdated version: $version");
			continue;
		}

		$messageHash = md5($message);
		$crashesDuplicatesCount[$messageHash] = ($crashesDuplicatesCount[$messageHash] ?? 0) + 1;
		$crashesMessagesWithLinks[$messageHash] = "$fullCrashUrl ($message)";

		out("Found \"$message\" crash!");
	}

	out("---------------------");
	out("Read page " . ($currentPage - 1) . " of $totalPages pages.");
	out("---------------------");
} while($currentPage <= $totalPages);

out("Found " . count($crashesMessagesWithLinks) . " crashes!");

arsort($crashesDuplicatesCount);

$output = "";

$i = 0;
foreach($crashesDuplicatesCount as $id => $duplicateCount) {
	$output .= (++$i) . ". [$duplicateCount] $crashesMessagesWithLinks[$id]\n";
}

file_put_contents("crashes.txt", $output);
out($output);

function out(string $message): void {
	echo "[" . gmdate("H:i:s") . "] " . $message . "\n";
}