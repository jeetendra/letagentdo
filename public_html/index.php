<?php
require __DIR__ . '/../vendor/autoload.php';

use App\App;

$app = new App();
echo $app->greet("Jeetendra");

?>