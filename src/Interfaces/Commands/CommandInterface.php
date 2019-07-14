<?php

namespace App\Interfaces\Commands;

use App\App;

interface CommandInterface
{
    public function run(App $app);
}