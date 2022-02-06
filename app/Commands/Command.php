<?php

declare(strict_types=1);

namespace App\Commands;

abstract class Command {
    abstract public function execute();
}
