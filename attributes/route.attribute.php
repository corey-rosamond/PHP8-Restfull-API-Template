<?php
declare(strict_types = 1);

namespace App\Attributes
{
    use App\Enums\HTTPType;

    require_once("./interfaces/route.interface.php");

    #[\Attribute]
    class Route implements \App\Interfaces\Route
    {
        public function __construct(public string $path, public HTTPType $method = HTTPType::Get)
        {}
    }
}