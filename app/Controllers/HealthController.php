<?php

namespace App\Controllers;

use App\Core\Response;

class HealthController
{
    /**
     * Verifica saúde da API
     */
    public function check(): void
    {
        Response::success([
            'status' => 'ok',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0.0',
        ], 'API is running');
    }
}
