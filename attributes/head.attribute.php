<?php
declare(strict_types = 1);

namespace App\Attributes
{
    use Attribute;
    use App\Enums\HTTPType;

    #[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
    class Head extends Route
    {
        public function __construct(string $path)
        {
            parent::__construct($path, HTTPType::Head);
        }
    }
}