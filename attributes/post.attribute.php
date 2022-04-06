<?php
declare(strict_types = 1);

namespace App\Attributes
{
    use Attribute;

    #[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
    class Post extends Route
    {
        public function __construct(string $path)
        {
            parent::__construct($path, 'post');
        }
    }
}