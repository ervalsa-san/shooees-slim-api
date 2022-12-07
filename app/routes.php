<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    // Register User
    $app->post('/register', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $parsedBody = $request->getParsedBody();

        $name = $parsedBody['name'];
        $username = $parsedBody['username'];
        $email = $parsedBody['email'];
        $password = $parsedBody['password'];
        $profile_photo_path = $parsedBody['profile_photo_path'];
        $alamat = $parsedBody['alamat'];

        $encryptPassword = password_hash($password, PASSWORD_DEFAULT);
        $password = $encryptPassword;
        $query = $db->prepare('INSERT INTO user (name, username, email, password, profile_photo_path, alamat) VALUES (?, ?, ?, ?, ?, ?)');
        $query->execute([$name, $username, $email, $password, $profile_photo_path, $alamat]);

        $response->getBody()->write(json_encode(
            [
                "status" => "Registrasi berhasil"
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });

    // All Product with Gallery
    $app->get('/api/v1/products', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('SELECT * FROM getallproductwithgallery');
        
        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode(
            [
                "message" => "Berhasil memuat data",
                "data" => $results
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });

    // Get one Product with ID
    $app->get('/api/v1/products/{id}', function(Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('SELECT * FROM product WHERE id = ?');
        $query->execute([$args['id']]);

        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode(
            [
                "message" => "Berhasil memuat data",
                "data" => $result[0]
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });  

    // Insert Product
    $app->post('/api/v1/products', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $parsedBody = $request->getParsedBody();

        $name = $parsedBody["name"];
        $price = $parsedBody["price"];
        $description = $parsedBody["description"];
        $stock = $parsedBody["stock"];
        $category_id = $parsedBody["category_id"];

        $query = $db->prepare('INSERT INTO product (name, price, description, stock, category_id) VALUES (?, ?, ?, ?, ?)');
        $query->execute([$name, $price, $description, $stock, $category_id]);

        
        $response->getBody()->write(json_encode(
            [
                "message" => "Produk baru telah ditambahkan",
                "data" => $request->getParsedBody()
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });

    // // Login User
    // $app->post('/login', function(Request $request, Response $response) {
    //     $db = $this->get(PDO::class);
    //     $parsedBody = $request->getParsedBody();

    //     $email = $parsedBody['email'];
    //     $password = $parsedBody['password'];
    //     $encryptPassword = password_hash($password, PASSWORD_DEFAULT);
    //     $password = $encryptPassword;

    //     $query = $db->prepare('SELECT email, password FROM user WHERE email = ? AND password = ?');
    //     $query->execute([$email, $password]);

    //     $response->getBody()->write(json_encode(
    //         [
    //             "message" => "Berhasil login"
    //         ]
    //     ));

    //     return $response->withHeader("Content-Type", "application/json");
    // });

    // // Get All Product
    // $app->get('/products', function (Request $request, Response $response) {
    //     $db = $this->get(PDO::class);

    //     $query = $db->query('SELECT id, name, price, description, category, product_photo, stock FROM product');

    //     $results = $query->fetchAll(PDO::FETCH_ASSOC);

    //     $response->getBody()->write(json_encode(
    //         [
    //             "message" => "Berhasil memuat produk", 
    //             "data" => $results
    //         ])
    //     );

    //     return $response->withHeader("Content-Type", "application/json");
    // });

    // // Insert Product
    // $app->post('/products', function (Request $request, Response $response) {
    //     $db = $this->get(PDO::class);
    //     $parsedBody = $request->getParsedBody();
        
    //     $name = $parsedBody['name'];
    //     $price = $parsedBody['price'];
    //     $description = $parsedBody['description'];
    //     $category = $parsedBody['category'];
    //     $product_photo = $parsedBody['product_photo'];
    //     $stock = $parsedBody['stock'];

    //     $query = $db->prepare('INSERT INTO product (name, price, description, category, product_photo, stock) VALUES (?, ?, ?, ?, ?, ?)');
    //     $query->execute([$name, $price, $description, $category, $product_photo, $stock]);

    //     $lastId = $db->lastinsertId();

    //     $response->getBody()->write(json_encode(
    //         [
    //             "status" => "Produk berhasil disimpan",
    //             "message" => "Product disimpan dengan id " . $lastId
    //         ]
    //     ));

    //     return $response->withHeader("Content-Type", "application/json");
    // });
};
