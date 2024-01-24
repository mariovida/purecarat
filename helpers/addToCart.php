<?php
    $ringId = $_POST['ringId'];
    session_start();
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][] = [
        'id' => $ringId,
        'name' => 'Ring ' . $ringId,
        'price' => 1000,
    ];

    $total = array_sum(array_column($_SESSION['cart'], 'price'));
    echo json_encode([
        'cartHtml' => $_SESSION['cart'],
        'total' => $total,
    ]);
