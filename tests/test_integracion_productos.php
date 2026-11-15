<?php

$conexion = null;
$codigoSalida = 0;

try {
    require __DIR__ . '/conexion_pruebas.php';
    require_once __DIR__ . '/../src/validaciones.php';

    echo "Conectado correctamente." . PHP_EOL;
    echo "Base utilizada: "
        . $conexion->query('SELECT DATABASE()')->fetchColumn()
        . PHP_EOL;

    $conexion->beginTransaction();

    // =====================================
    // I1: REGISTRAR Y RECUPERAR DATO VÁLIDO
    // =====================================

    echo PHP_EOL . "=== PRUEBA I1: DATO VALIDO ===" . PHP_EOL;

    $nombre = 'Prueba';
    $marca = 'Logitech';
    $cantidad = 5;

    if (!validarMarcaProducto($marca)) {
        throw new Exception('La marca válida fue rechazada.');
    }

    $consulta = $conexion->prepare(
        'INSERT INTO productos (nombre, marca, cantidad)
         VALUES (:nombre, :marca, :cantidad)'
    );

    $consulta->execute([
        'nombre' => $nombre,
        'marca' => $marca,
        'cantidad' => $cantidad
    ]);

    $id = $conexion->lastInsertId();

    echo "Correcto: inserción realizada. ID: $id" . PHP_EOL;

    $consulta = $conexion->prepare(
        'SELECT nombre, marca, cantidad
         FROM productos
         WHERE id = :id'
    );

    $consulta->execute(['id' => $id]);

    $producto = $consulta->fetch();

    if (
        $producto !== false
        && $producto['nombre'] === $nombre
        && $producto['marca'] === $marca
        && (int) $producto['cantidad'] === $cantidad
    ) {
        echo "CORRECTO: producto recuperado con los datos esperados."
            . PHP_EOL;
    } else {
        echo "ERROR: los datos recuperados no coinciden." . PHP_EOL;
        $codigoSalida = 1;
    }

    // =====================================
    // I2: RECHAZAR DATO INVÁLIDO
    // =====================================

    echo PHP_EOL . "=== PRUEBA I2: DATO INVALIDO ===" . PHP_EOL;

    $marcaInvalida = 'A';

    $cantidadAntes = (int) $conexion
        ->query('SELECT COUNT(*) FROM productos')
        ->fetchColumn();

    if (validarMarcaProducto($marcaInvalida)) {
        $consulta = $conexion->prepare(
            'INSERT INTO productos (nombre, marca, cantidad)
             VALUES (:nombre, :marca, :cantidad)'
        );

        $consulta->execute([
            'nombre' => 'Producto inválido',
            'marca' => $marcaInvalida,
            'cantidad' => 5
        ]);

        echo "ERROR: la marca inválida fue aceptada." . PHP_EOL;
        $codigoSalida = 1;

    } else {
        echo "CORRECTO: la marca '$marcaInvalida' fue rechazada."
            . PHP_EOL;
    }

    $cantidadDespues = (int) $conexion
        ->query('SELECT COUNT(*) FROM productos')
        ->fetchColumn();

    if ($cantidadAntes === $cantidadDespues) {
        echo "CORRECTO: no se creó ningún registro adicional."
            . PHP_EOL;
    } else {
        echo "ERROR: se encontró un registro adicional." . PHP_EOL;
        $codigoSalida = 1;
    }

} catch (Throwable $error) {

    echo "ERROR: " . $error->getMessage() . PHP_EOL;
    $codigoSalida = 1;

} finally {

    if ($conexion !== null && $conexion->inTransaction()) {
        $conexion->rollBack();
        echo PHP_EOL . "Limpieza: registros de prueba eliminados."
            . PHP_EOL;
    }
}

exit($codigoSalida);