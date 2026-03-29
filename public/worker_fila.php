<?php

require_once __DIR__ . '/../app/services/FilaImpressao.php';

while (true) {

    FilaImpressao::processar();

    usleep(500000); // meio segundo
}