#!/bin/bash

PHP_BINARY="php"

while getopts "p:" OPTION 2> /dev/null; do
	case ${OPTION} in
		p)
			PHP_BINARY="$OPTARG"
			;;
	esac
done

"$PHP_BINARY" ./tests/ConsoleScript.php --make . --relative . --out ./Slapper.phar
