<?php
/**
 * This file is used to build the list of deleted files and folders between two reference points.
 *
 * @package    Joomla.Build
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// TODO: Make these directories dynamic or clone from git
$previousReleaseDir = __DIR__ . '/joomla390';
$newReleaseDir = __DIR__ . '/joomla400';

$previousReleaseDirIterator = new RecursiveDirectoryIterator($previousReleaseDir, RecursiveDirectoryIterator::SKIP_DOTS);
$previousReleaseIterator = new RecursiveIteratorIterator($previousReleaseDirIterator, RecursiveIteratorIterator::SELF_FIRST);
$previousReleaseFiles = [];
$previousReleaseFolders = [];

foreach ($previousReleaseIterator as $info)
{
	if ($info->isDir())
	{
		$previousReleaseFolders[] = "'" . str_replace($previousReleaseDir, '', $info->getPathname()) . "',";
		continue;
	}

	$previousReleaseFiles[] = "'" . str_replace($previousReleaseDir, '', $info->getPathname()) . "',";
}

$newReleaseDirIterator = new RecursiveDirectoryIterator($newReleaseDir, RecursiveDirectoryIterator::SKIP_DOTS);
$newReleaseIterator = new RecursiveIteratorIterator($newReleaseDirIterator, RecursiveIteratorIterator::SELF_FIRST);
$newReleaseFiles = [];
$newReleaseFolders = [];

foreach ($newReleaseIterator as $info)
{
	if ($info->isDir())
	{
		$newReleaseFolders[] = "'" . str_replace($newReleaseDir, '', $info->getPathname()) . "',";
		continue;
	}

	$newReleaseFiles[] = "'" . str_replace($newReleaseDir, '', $info->getPathname()) . "',";
}

$filesDifference = array_diff($previousReleaseFiles, $newReleaseFiles);

$foldersDifference = array_diff($previousReleaseFolders, $newReleaseFolders);

asort($filesDifference);
asort($foldersDifference);

// Write the deleted files list to a file for later reference
file_put_contents(__DIR__ . '/deleted_files.txt', implode("\n", $filesDifference));
file_put_contents(__DIR__ . '/deleted_folders.txt', implode("\n", $foldersDifference));
