<?php

require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('./templates/');
$twig = new \Twig\Environment($loader);

$uri = $_SERVER['REQUEST_URI'];
$template = 'index.twig';

$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$twig->addGlobal('base_url', $base_url);

$ringsData = json_decode(file_get_contents('./data/rings.json'), true);

if ($uri === $base_url.'/') {
    echo $twig->render('index.html.twig', ['ringsData' => $ringsData]);
} else if ($uri === $base_url.'/rings') {
    echo $twig->render('rings.html.twig', ['ringsData' => $ringsData]);
}