<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

\App\Models\Book::query()->delete();
echo 'Base de données nettoyée.' . PHP_EOL;
echo 'Livres restants: ' . \App\Models\Book::count() . PHP_EOL;
