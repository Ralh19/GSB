<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

use Modeles\PdoGsb;

require '../vendor/autoload.php';
require '../config/define.php';
require '../src/Modeles/PdoGsb.php';

$pdo = PdoGsb::getPdoGsb();
$pdo-> hashPassword('visiteur');
$pdo-> hashPassword('comptable');

echo 'Execution effectuer';

