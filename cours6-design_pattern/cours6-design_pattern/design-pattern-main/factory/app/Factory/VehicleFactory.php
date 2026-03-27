<?php

namespace App\Factory;

use App\Entity\Bicycle;
use App\Entity\Car;
use App\Entity\Truck;
use App\Entity\VehicleInterface;
use InvalidArgumentException;

class VehicleFactory
{
    public function create(string $type): VehicleInterface
    {
        return match ($type) {
            'bicycle' => new Bicycle(0, 'human'),
            'car' => new Car(0.5, 'gasoline'),
            'truck' => new Truck(1.2, 'diesel'),
            default => throw new InvalidArgumentException('Véhicule inconnu'),
        };
    }

    public function createByDistanceAndWeight(float $distanceKm, float $weightKg): VehicleInterface
    {
        if ($weightKg > 200) {
            return $this->create('truck');
        }

        if ($weightKg > 20) {
            return $this->create('car');
        }

        if ($distanceKm < 20) {
            return $this->create('bicycle');
        }

        return $this->create('car');
    }
}