<?php
require('../vendor/autoload.php');

use App\Factory\VehicleFactory;

$factory = new VehicleFactory();

$bicycle = $factory->create('bicycle');
$car = $factory->create('car');
$truck = $factory->create('truck');

echo '<pre>';
echo 'Bicycle: ' . $bicycle->getFuelType() . ' - ' . $bicycle->getCostPerKm() . ' €/km' . PHP_EOL;
echo 'Car: ' . $car->getFuelType() . ' - ' . $car->getCostPerKm() . ' €/km' . PHP_EOL;
echo 'Truck: ' . $truck->getFuelType() . ' - ' . $truck->getCostPerKm() . ' €/km' . PHP_EOL;
echo '</pre>';