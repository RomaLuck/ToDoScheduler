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

    public function hourFilter($time): int
    {
        $result = preg_match('!(\d+):!u', $time, $timeMatch);
        return (int)$timeMatch[0];
    }
}
