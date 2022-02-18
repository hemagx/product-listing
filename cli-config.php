<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\DependencyFactory;

$app = require_once "bootstrap/bootstrap.php";
$app->run();

$config = new PhpFile('bootstrap/migrations.php');

return DependencyFactory::fromEntityManager($config, new ExistingEntityManager($app->entityManager));
