<?php

include_once 'Request.php';
include_once 'Response.php';
include_once 'Router.php';

interface IPexpress{
    public function Router();
    public function use();
}

class Pexpress{  
     static $route = '';

    function Router() {
        return $this->use();
    }

    function use(){
        return new Router(new Request, new Response);
    }
}
?>