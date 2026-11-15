<?php

require_once __DIR__ . '/config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = filter_var(
    $_POST['id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

$cantidad = filter_var(
    $_POST['cantidad'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 0]]
);

if ($id === false || $id === null || $cantidad === false) {
    header('Location: index.php?estado=cantidad_invalida');
    exit;
}

$sentencia = $conexion->prepare(
    'UPDATE productos
     SET cantidad = :cantidad
     WHERE id = :id'
);

$sentencia->execute([
    'cantidad' => $cantidad,
    'id' => $id
]);

header('Location: index.php?estado=cantidad_actualizada');
exit;