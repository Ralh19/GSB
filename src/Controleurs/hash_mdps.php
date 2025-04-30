<?php

use Modeles\PdoGsb;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

switch ($action) {
    case 'executer':
        $pdo = PdoGsb::getPdoGsb();
        $pdo->setHashMdpComptables();
        // $pdo->setHashMdp();
        echo '<div class="alert alert-success" style="margin:2rem;">✔ Mots de passe hachés avec succès !</div>';
        break;

    default:
        include PATH_VIEWS . 'v_hashMdp.php';
        break;
}
