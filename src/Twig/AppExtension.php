<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private string $host
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('santa_url', [$this, 'getSantaUrl']),
        ];
    }

    public function getSantaUrl(string $path): string
    {
      return $this->host . $path;
    }
}