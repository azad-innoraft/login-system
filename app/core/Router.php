<?php

use App\controllers\AuthController;
use App\controllers\FormController;

// parsing the path form the uri 
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$auth = new AuthController();
$form = new FormController();


/**
 * Routing logic
 */
switch ($uri) {
    case '/':
        $form->index();
        break;
    case '/register':
        $auth->register();
        break;
    case '/login':
        $auth->login();
        break;
    case '/forgot':
        $auth->forgot();
        break;
    case '/reset':
        $auth->reset();
        break;
    case '/logout':
        $auth->logout();
        break;
    case '/form':
        $form->index();
        break;
    case '/pdf':
        $form->generatePDF();
        break;
    default:
        http_response_code(404);
        echo "
            <h1>404 Page Not Found</h1>
            <p><a href='/'>Go to Home</a></p>
        ";
}
