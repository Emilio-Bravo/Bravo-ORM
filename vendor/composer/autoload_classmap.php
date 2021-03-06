<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Bravo\\ORM\\BravoORM' => $baseDir . '/src/ORM/BravoORM.php',
    'Bravo\\ORM\\DB' => $baseDir . '/src/Database/DB.php',
    'Bravo\\ORM\\DataHandler' => $baseDir . '/src/helpers/DataHandler.php',
    'Bravo\\ORM\\DatabseEnv' => $baseDir . '/src/env/DatabaseEnv.php',
    'Bravo\\ORM\\Model' => $baseDir . '/src/demo/model.php',
    'Bravo\\ORM\\ORM' => $baseDir . '/src/ORM/ORM.php',
    'Bravo\\ORM\\ParameterBag' => $baseDir . '/src/helpers/ParameterBag.php',
    'Bravo\\ORM\\Query' => $baseDir . '/src/Database/Query.php',
    'Bravo\\ORM\\QueryFormatter' => $baseDir . '/src/helpers/QueryFormatter.php',
    'Bravo\\ORM\\countsResults' => $baseDir . '/src/traits/countsResultsTrait.php',
    'Bravo\\ORM\\handlesExceptions' => $baseDir . '/src/traits/handlesExceptionsTrait.php',
    'Bravo\\ORM\\logicQuerys' => $baseDir . '/src/contracts/logicQuerysInterface.php',
    'Bravo\\ORM\\supportsCRUD' => $baseDir . '/src/contracts/supportsCRUDInterface.php',
    'Bravo\\ORM\\verifyiesData' => $baseDir . '/src/traits/veryfiesDataTrait.php',
    'Composer\\InstalledVersions' => $vendorDir . '/composer/InstalledVersions.php',
);
