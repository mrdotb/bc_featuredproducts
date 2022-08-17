#!/usr/bin/env bash
set -x
composer install
composer dump-autoload
mkdir bc_featuredproducts
cp -r override src tools translations vendor views bc_featuredproducts.php composer.json composer.lock config.xml index.php -t bc_featuredproducts
zip -r bc_featuredproducts.zip bc_featuredproducts
rm -rf bc_featuredproducts
