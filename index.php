<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

$dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'] . ";charset=utf8mb4";
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

try {
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$loader = new \Twig\Loader\FilesystemLoader('./templates/');
$twig = new \Twig\Environment($loader);

$uri = $_SERVER['REQUEST_URI'];

$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$twig->addGlobal('base_url', $base_url);

//$ringsData = json_decode(file_get_contents('./data/rings.json'), true);
//$menJewelryData = json_decode(file_get_contents('./data/men.json'), true);

$query = "SELECT * FROM rings";
$statement = $pdo->prepare($query);
$statement->execute();
$ringsData = $statement->fetchAll(PDO::FETCH_ASSOC);

$query = "SELECT * FROM ring_images";
$statement = $pdo->prepare($query);
$statement->execute();
$ringImages = $statement->fetchAll(PDO::FETCH_ASSOC);

$ringImagesByRingId = [];
foreach ($ringImages as $image) {
    $ringId = $image['ring_id'];
    if (!isset($ringImagesByRingId[$ringId])) {
        $ringImagesByRingId[$ringId] = [];
    }
    $ringImagesByRingId[$ringId][] = $image;
}

foreach ($ringsData as &$ring) {
    $ringId = $ring['id'];
    $ring['images'] = isset($ringImagesByRingId[$ringId]) ? $ringImagesByRingId[$ringId] : [];
}

$query = "SELECT * FROM men_jewelry";
$statement = $pdo->prepare($query);
$statement->execute();
$menJewelryData = $statement->fetchAll(PDO::FETCH_ASSOC);

$query = "SELECT * FROM men_jewelry_images";
$statement = $pdo->prepare($query);
$statement->execute();
$menJewelryImages = $statement->fetchAll(PDO::FETCH_ASSOC);

$menJewelryImagesByItemId = [];
foreach ($menJewelryImages as $image) {
    $jewelryId = $image['jewelry_id'];
    if (!isset($menJewelryImagesByItemId[$jewelryId])) {
        $menJewelryImagesByItemId[$jewelryId] = [];
    }
    $menJewelryImagesByItemId[$jewelryId][] = $image;
}

foreach ($menJewelryData as &$item) {
    $jewelryId = $item['id'];
    $item['images'] = isset($menJewelryImagesByItemId[$jewelryId]) ? $menJewelryImagesByItemId[$jewelryId] : [];
}

if ($uri === $base_url.'/') {
    echo $twig->render('index.html.twig', [
        'ringsData' => $ringsData,
        'ringImages' => $ringImages,
        'menJewelryData' => $menJewelryData,
        'menJewelryImages' => $menJewelryImages
    ]);
} else if ($uri === $base_url.'/rings') {
    echo $twig->render('rings.html.twig', [
        'ringsData' => $ringsData,
        'ringImages' => $ringImages,
    ]);
} else if ($uri === $base_url.'/men-jewelry') {
    echo $twig->render('men-jewelry.html.twig', [
        'menJewelryData' => $menJewelryData,
        'menJewelryImages' => $menJewelryImages,
    ]);
} elseif (preg_match('#^'.$base_url.'/category/rings/(\d+)$#', $uri, $matches)) {
    $itemId = $matches[1];
    $query = "SELECT * FROM rings WHERE id = :id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':id', $itemId, PDO::PARAM_INT);
    $statement->execute();
    $itemData = $statement->fetch(PDO::FETCH_ASSOC);
    
    $query = "SELECT * FROM ring_images WHERE ring_id = $itemId";
    $statement = $pdo->prepare($query);
    $statement->execute();
    $ringImages = $statement->fetchAll(PDO::FETCH_ASSOC);

    $ringImagesByRingId = [];
    foreach ($ringImages as $image) {
        $ringId = $image['ring_id'];
        if (!isset($ringImagesByRingId[$ringId])) {
            $ringImagesByRingId[$ringId] = [];
        }
        $ringImagesByRingId[$ringId][] = $image;
    }

    echo $twig->render('single/item.html.twig', [
        'category' => 'ring',
        'itemData' => $itemData,
        'images' => $ringImages,
    ]);
} elseif (preg_match('#^'.$base_url.'/category/men-jewelry/(\d+)$#', $uri, $matches)) {
    $itemId = $matches[1];
    $query = "SELECT * FROM men_jewelry WHERE id = :id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':id', $itemId, PDO::PARAM_INT);
    $statement->execute();
    $itemData = $statement->fetch(PDO::FETCH_ASSOC);
    
    $query = "SELECT * FROM men_jewelry_images WHERE jewelry_id = $itemId";
    $statement = $pdo->prepare($query);
    $statement->execute();
    $menJewelryImages = $statement->fetchAll(PDO::FETCH_ASSOC);

    $menJewelryImagesByItemId = [];
    foreach ($menJewelryImages as $image) {
        $jewelryId = $image['jewelry_id'];
        if (!isset($menJewelryImagesByItemId[$jewelryId])) {
            $menJewelryImagesByItemId[$jewelryId] = [];
        }
        $menJewelryImagesByItemId[$jewelryId][] = $image;
    }

    echo $twig->render('single/item.html.twig', [
        'category' => 'men-jewelry',
        'itemData' => $itemData,
        'images' => $menJewelryImages,
    ]);
} else {
    echo $twig->render('404.html.twig');
}