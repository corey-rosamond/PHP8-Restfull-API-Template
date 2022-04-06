<?php
declare(strict_types = 1);

namespace App\Enums
{
    enum HTTPType: string
    {
        case Get    = 'get';
        case Post   = 'post';
        case Put    = 'put';
        case Head   = 'head';
        case Delete = 'delete';
    }
}