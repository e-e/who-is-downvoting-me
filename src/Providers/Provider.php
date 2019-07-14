<?php

namespace App\Providers;

use App\App;

abstract class Provider
{
    abstract public function provide(App $app);
}