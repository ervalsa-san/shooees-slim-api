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
    
    // Store Product
    $app->post('/api/v1/product', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $parsedBody = $request->getParsedBody();
        
        $name = $parsedBody['name'];
        $name = $parsedBody['price'];
        $name = $parsedBody['description'];
        $name = $parsedBody['stock'];
        $name = $parsedBody[''];
        $name = $parsedBody['name'];

    });

    // Store Payment
    // $app->post('/payment', function(Request $request, Response $response) {
    //     $db = $this->get(PDO::class);
    //     $parsedBody = $request->getParsedBody();

    //     $transaction_id = $parsedBody['transaction_id'];
    //     $pay_date = $parsedBody['pay_date'];
    //     $total_price = $parsedBody['total_price'];
    //     $method = $parsedBody['method'];
    //     $status = $parsedBody['status'];

    //     $query = CALL InsertPayment()
    //     $query->execute([$name, $username, $email, $password, $profile_photo_path, $alamat]);

    //     $response->getBody()->write(json_encode(
    //         [
    //             "status" => "Registrasi berhasil"
    //         ]
    //     ));

    //     return $response->withHeader("Content-Type", "application/json");
    // });

    // All Product with Gallery
    $app->get('/api/v1/products', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('SELECT * FROM getallproduct');
        
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

        $id = $parsedBody["id"];
        $name = $parsedBody["name"];
        $price = $parsedBody["price"];
        $description = $parsedBody["description"];
        $stock = $parsedBody["stock"];
        $category_id = $parsedBody["category_id"];

        $query = $db->prepare('CALL InsertProduct(?, ?, ?, ?, ?, ?)');
        $query->execute([$id, $name, $price, $description, $stock, $category_id]);

        $response->getBody()->write(json_encode(
            [
                "message" => "Produk baru telah ditambahkan",
                "data" => $request->getParsedBody()
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });

    // Insert Product Category
    $app->post('/api/v1/products/category', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $parsedBody = $request->getParsedBody();

        $id = $parsedBody["id"];
        $name = $parsedBody["name"];

        $query = $db->prepare('CALL InsertProductCategory(?, ?)');
        $query->execute([$id, $name]);

        $response->getBody()->write(json_encode(
            [
                "message" => "Produk Kategori baru telah ditambahkan",
                "data" => $request->getParsedBody()
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });

    // Insert Product Gallery
    $app->post('/api/v1/products/gallery', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $parsedBody = $request->getParsedBody();

        $id = $parsedBody["id"];
        $product_id = $parsedBody["product_id"];
        $name = $parsedBody["name"];

        $query = $db->prepare('CALL InsertProductGallery(?, ?, ?)');
        $query->execute([$id, $product_id, $name]);

        $response->getBody()->write(json_encode(
            [
                "message" => "Produk Galeri baru telah ditambahkan",
                "data" => $request->getParsedBody()
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });

    // Insert Product Size
    $app->post('/api/v1/products/size', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $parsedBody = $request->getParsedBody();

        $id = $parsedBody["id"];
        $product_id = $parsedBody["product_id"];
        $name = $parsedBody["name"];

        $query = $db->prepare('CALL InsertProductSize(?, ?, ?)');
        $query->execute([$id, $product_id, $name]);

        $response->getBody()->write(json_encode(
            [
                "message" => "Produk Size baru telah ditambahkan",
                "data" => $request->getParsedBody()
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });

    // Insert Transaction
    $app->post('/api/v1/transactions', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $parsedBody = $request->getParsedBody();

        $id = $parsedBody["id"];
        $user_id = $parsedBody["user_id"];
        $total_price = $parsedBody["total_price"];

        $query = $db->prepare('CALL InsertTransaction(?, ?, ?)');
        $query->execute([$id, $user_id, $total_price]);

        $response->getBody()->write(json_encode(
            [
                "message" => "Transaksi baru telah ditambahkan",
                "data" => $request->getParsedBody()
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });

    // Insert Transaction item
    $app->post('/api/v1/transactions/item', function(Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $parsedBody = $request->getParsedBody();

        $id = $parsedBody["id"];
        $transaction_id = $parsedBody["transaction_id"];
        $product_id = $parsedBody["product_id"];
        $quantity = $parsedBody["quantity"];
        $total_price = $parsedBody["total_price"];

        $query = $db->prepare('CALL InsertTransactionItem(?, ?, ?, ?, ?)');
        $query->execute([$id, $transaction_id, $product_id, $quantity, $total_price]);

        $response->getBody()->write(json_encode(
            [
                "message" => "Transaksi Item baru telah ditambahkan",
                "data" => $request->getParsedBody()
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });
};
