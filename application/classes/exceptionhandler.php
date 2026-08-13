<?php

defined('SYSPATH') or die('No direct script access.');

class Exceptionhandler {

    public static function handle(Exception $e) {
        switch (get_class($e)) {
            case 'Http_Exception_404':
                $response = new Response;
                $response->status(404);
                $view = new View('/pages/404');
                $view->message = $e->getMessage();
                echo $response->body($view)->send_headers()->body();
                return TRUE;
                break;
            default:
                return Kohana_Exception::handler($e);
                break;
        }
    }

}
