<?php
declare(strict_types = 1);

namespace App\Controllers;

use App\Attributes\{
    Route,
    Get,
    Post,
    Put,
    Delete
};

class User
{

    #[Get('/RestfullAPI/user-get')]
    public function userGet($properties)
    {
        echo "<pre>".print_r($properties, true)."</pre>";
    }

    #[Post('/user-post')]
    public function userPost()
    {

    }

    #[Put('/user-put')]
    public function userPut()
    {

    }

    #[Delete("/user-delete")]
    public function userDelete()
    {

    }
}