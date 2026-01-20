<?php

declare(strict_types=1);

namespace HerzScheisse\Webtrees\Module\GlobalMessage;

use Composer\Autoload\ClassLoader;

$loader = new ClassLoader();
$loader->addPsr4('HerzScheisse\\Webtrees\\Module\\GlobalMessage\\', __DIR__);
$loader->register();

return new GlobalMessage();
