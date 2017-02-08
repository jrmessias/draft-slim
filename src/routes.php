<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

// Routes
$app->get('/hello', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

// Listagem de usuários
$app->get('/users', function (Request $request, Response $response, $args) {


//    $this->db->schema()->create('users', function ($table) {
//        $table->increments('id');
//        $table->string('email')->unique();
//        $table->string('name');
//    });
//    $this->db->table('users')->insert(['email' => 'email@email.com', 'name' => 'user']);

    $users = $this->db->table('users')->get();

    $nome = $_SESSION['user']['name'];

    return $this->renderer->render($response, 'users/index.phtml', ['users' => $users, 'nome' => $nome]);
})->add($auth);

// Cadastro
$app->post('/users', function (Request $request, Response $response, $args) {

    $data = [
        'name' => filter_input(INPUT_POST, 'name'),
        'email' => filter_input(INPUT_POST, 'email'),
    ];

    $this->db->table('users')->insert($data);

    return $response->withStatus(302)->withHeader('Location', '/users');
})->add($auth);

// Delete
$app->get('/users/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];

    $this->db->table('users')->where('id', $id)->delete();

    return $response->withStatus(302)->withHeader('Location', '/users');
})->add($auth);

// Login
$app->map(['GET', 'POST'], '/login', function (Request $request, Response $response, $args) {

    if($request->getMethod() == 'POST'){
        $data = [
            'email' => filter_input(INPUT_POST, 'email'),
            'password' => filter_input(INPUT_POST, 'password'),
        ];

        $users = $this->db->table('users')->where($data)->get();

        if($users->count()){
            $_SESSION['user'] = (array)$users->first();
            return $response->withStatus(302)->withHeader('Location', '/users');
        }
    }

    return $this->renderer->render($response, 'users/login.phtml', $args);
});

// Logout
$app->get('/logout', function (Request $request, Response $response, $args) {
    unset($_SESSION['user']);

    return $response->withStatus(302)->withHeader('Location', '/login');
});