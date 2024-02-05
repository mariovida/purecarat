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
$twig->addGlobal('isCategoryPage', false);

$uri = $_SERVER['REQUEST_URI'];

$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$twig->addGlobal('base_url', $base_url);

// RINGS
$query = "SELECT * FROM rings";
$statement = $pdo->prepare($query);
$statement->execute();
$ringsData = $statement->fetchAll(PDO::FETCH_ASSOC);
shuffle($ringsData);

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

// MEN JEWELRY
$query = "SELECT * FROM men_jewelry";
$statement = $pdo->prepare($query);
$statement->execute();
$menJewelryData = $statement->fetchAll(PDO::FETCH_ASSOC);
shuffle($menJewelryData);

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

// EARRINGS
$query = "SELECT * FROM earrings";
$statement = $pdo->prepare($query);
$statement->execute();
$earringsData = $statement->fetchAll(PDO::FETCH_ASSOC);
shuffle($earringsData);

$query = "SELECT * FROM earrings_images";
$statement = $pdo->prepare($query);
$statement->execute();
$earringsImages = $statement->fetchAll(PDO::FETCH_ASSOC);

$earringsImagesByItemId = [];
foreach ($earringsImages as $image) {
    $earringId = $image['earring_id'];
    if (!isset($earringsImagesByItemId[$earringId])) {
        $earringsImagesByItemId[$earringId] = [];
    }
    $earringsImagesByItemId[$earringId][] = $image;
}

foreach ($earringsData as &$item) {
    $earringId = $item['id'];
    $item['images'] = isset($earringsImagesByItemId[$earringId]) ? $earringsImagesByItemId[$earringId] : [];
}

if ($uri === $base_url.'/') {
    echo $twig->render('index.html.twig', [
        'ringsData' => $ringsData,
        'ringImages' => $ringImages,
        'menJewelryData' => $menJewelryData,
        'menJewelryImages' => $menJewelryImages,
        'earringsData' => $earringsData,
        'earringsImages' => $earringsImages
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
} else if ($uri === $base_url.'/earrings') {
    echo $twig->render('earrings.html.twig', [
        'earringsData' => $earringsData,
        'earringsImages' => $earringsImages,
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
    $twig->addGlobal('isCategoryPage', true);

    echo $twig->render('single/item.html.twig', [
        'category' => 'ring',
        'itemData' => $itemData,
        'images' => $ringImages,
        'relatedData' => $ringsData,
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
    $twig->addGlobal('isCategoryPage', true);

    echo $twig->render('single/item.html.twig', [
        'category' => 'men-jewelry',
        'itemData' => $itemData,
        'images' => $menJewelryImages,
        'relatedData' => $menJewelryData,
    ]);
} elseif (preg_match('#^'.$base_url.'/category/earrings/(\d+)$#', $uri, $matches)) {
    $itemId = $matches[1];
    $query = "SELECT * FROM earrings WHERE id = :id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':id', $itemId, PDO::PARAM_INT);
    $statement->execute();
    $itemData = $statement->fetch(PDO::FETCH_ASSOC);
    
    $query = "SELECT * FROM earrings_images WHERE earring_id = $itemId";
    $statement = $pdo->prepare($query);
    $statement->execute();
    $earringsImages = $statement->fetchAll(PDO::FETCH_ASSOC);

    $earringsImagesByItemId = [];
    foreach ($earringsImages as $image) {
        $jewelryId = $image['earring_id'];
        if (!isset($earringsImagesByItemId[$jewelryId])) {
            $earringsImagesByItemId[$jewelryId] = [];
        }
        $earringsImagesByItemId[$jewelryId][] = $image;
    }
    $twig->addGlobal('isCategoryPage', true);
    
    echo $twig->render('single/item.html.twig', [
        'category' => 'earrings',
        'itemData' => $itemData,
        'images' => $earringsImages,
        'relatedData' => $earringsData,
    ]);
} else {
    echo $twig->render('404.html.twig');
}