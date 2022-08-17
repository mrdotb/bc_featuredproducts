#!/usr/bin/env bash
set -x
composer install
composer dump-autoload
zip -r bc_featuredproducts.zip override vendor bc_featuredproducts.php composer.lock index.php src translations views composer.json config.xml
