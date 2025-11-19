<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "TMS API Documentation",
    description: "API documentation for TMS (Tenant Management System) - Customer and Contact Management"
)]
#[OA\Server(
    url: "http://localhost:8081/api",
    description: "Local development server"
)]
class Info
{
}

