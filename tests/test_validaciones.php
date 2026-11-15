<?php

require_once __DIR__ . '/../src/validaciones.php';

// PRUEBAS PARA EL NOMBRE DEL PRODUCTO
$casosNombre = [
    ["nombre válido: Teclado", "Teclado", true],
    ["nombre vacío", "", false],
    ["nombre con espacios", "  ", false],
    ["nombre válido: Mouse", "Mouse", true],
    ["nombre válido: Monitor", "Monitor", true]
];

// PRUEBAS PARA LA CANTIDAD DEL PRODUCTO
$casosCantidad = [
    ["cantidad válida: 10", "10", true],
    ["cantidad válida: 1", "1", true],
    ["cantidad vacía", "", false],
    ["cantidad con espacios", "  ", false],
    ["cantidad cero", "0", false],
    ["cantidad negativa", "-5", false],
    ["cantidad con letras", "abc", false],
    ["cantidad decimal", "2.5", false]
];

// CINCO PRUEBAS UNITARIAS PARA LA MARCA
$casosMarca = [
    ["marca válida: Samsung", "Samsung", true],
    ["marca de un carácter", "A", false],
    ["marca vacía", "", false],
    ["marca con 25 caracteres", "1234567890123456789012345", true],
    ["marca con espacios al inicio y final", "  Sony  ", true]
];

$correctas = 0;
$fallidas = 0;

function ejecutarPruebas(array $casos, callable $validar): void
{
    global $correctas, $fallidas;

    foreach ($casos as [$descripcion, $entrada, $esperado]) {
        $obtenido = $validar($entrada);

        if ($obtenido === $esperado) {
            echo "PASS - $descripcion\n";
            $correctas++;
        } else {
            echo "FAIL - $descripcion\n";
            echo "Esperado: " . var_export($esperado, true) . "\n";
            echo "Obtenido: " . var_export($obtenido, true) . "\n";
            $fallidas++;
        }
    }
}

echo "\n----- PRUEBAS DE NOMBRE -----\n";
ejecutarPruebas($casosNombre, 'validarNombreProducto');

echo "\n----- PRUEBAS DE CANTIDAD -----\n";
ejecutarPruebas($casosCantidad, 'validarCantidadProducto');

echo "\n----- PRUEBAS DE MARCA -----\n";
ejecutarPruebas($casosMarca, 'validarMarcaProducto');

$total = count($casosNombre) + count($casosCantidad) + count($casosMarca);

echo "\n------- RESUMEN -------\n";
echo "Total: $total\n";
echo "Correctas: $correctas\n";
echo "Fallidas: $fallidas\n";

exit($fallidas > 0 ? 1 : 0);