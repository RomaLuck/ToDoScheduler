<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class IntToTimeRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function formatIntToTime($value): string
    {
        return sprintf("%02d", $value) . ':00';
    }
}
