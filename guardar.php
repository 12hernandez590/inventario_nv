<?php

require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/src/validaciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Obtener los datos del formulario
$nombre = $_POST['nombre'] ?? '';
$marca = $_POST['marca'] ?? '';
$cantidad = $_POST['cantidad'] ?? '';

// Comprobar que los datos tengan el tipo esperado
if (
    !is_string($nombre) ||
    !is_string($marca) ||
    !is_string($cantidad)
) {
    header('Location: index.php?estado=incompleto');
    exit;
}

// Eliminar espacios innecesarios
$nombre = trim($nombre);
$marca = trim($marca);
$cantidad = trim($cantidad);

// Verificar campos vacíos
if ($nombre === '' || $marca === '' || $cantidad === '') {
    header('Location: index.php?estado=incompleto');
    exit;
}

// Validar el nombre del producto
if (!validarNombreProducto($nombre)) {
    header('Location: index.php?estado=nombre_invalido');
    exit;
}

// Validar la marca del producto
if (!validarMarcaProducto($marca)) {
    header('Location: index.php?estado=marca_invalida');
    exit;
}

// Validar la cantidad
if (!validarCantidadProducto($cantidad)) {
    header('Location: index.php?estado=cantidad_invalida');
    exit;
}

// Guardar en la base de datos
$sentencia = $conexion->prepare(
    'INSERT INTO productos (nombre, marca, cantidad)
     VALUES (:nombre, :marca, :cantidad)'
);

$sentencia->execute([
    'nombre' => $nombre,
    'marca' => $marca,
    'cantidad' => (int) $cantidad
]);

// Regresar al formulario
header('Location: index.php?estado=guardado');
exit;