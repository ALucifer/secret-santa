<?php

namespace App\Services;

use Random\Randomizer;

class RandomizeCollection
{
    public function randomizeCollection(array $collection)
    {
        $randomizer = new Randomizer();

        do {
            $shuffled = $randomizer->shuffleArray($collection);
            $valid = true;
            for ($i = 0; $i < count($shuffled); $i++) {
                if ($shuffled[$i] === $collection[$i]) {
                    $valid = false;
                    break;
                }
            }

        } while (!$valid);

        return $shuffled;
    }
}