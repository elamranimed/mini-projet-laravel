<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CONFIGURATION BASE DE DONNÉES ===" . PHP_EOL . PHP_EOL;
echo "Connexion active : " . config('database.default') . PHP_EOL;
echo "Driver           : " . config('database.connections.mysql.driver') . PHP_EOL;
echo "Host             : " . config('database.connections.mysql.host') . PHP_EOL;
echo "Port             : " . config('database.connections.mysql.port') . PHP_EOL;
echo "Database         : " . config('database.connections.mysql.database') . PHP_EOL;
echo "Username         : " . config('database.connections.mysql.username') . PHP_EOL;

echo PHP_EOL . "Livres dans la base : " . \App\Models\Book::count() . PHP_EOL;
