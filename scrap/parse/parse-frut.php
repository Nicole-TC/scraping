<?php

require_once "../funciones.php";

$html = "../html/scrap-one-piece-frutas.html";

$dom = new DOMDocument();
// El @ oculta advertencias si el HTML del sitio objetivo no es perfecto
@$dom->loadHTMLFile($html); 
#var_dump($dom);exit;
$xpath = new DOMXPath($dom);
#var_dump($xpath);exit;


// 1. Buscamos la tabla que tiene el encabezado de "Fruta del diablo"
$tabla = $xpath->query("//table[.//th[contains(text(), 'Fruta del diablo')]]")->item(0);
$frutas_extraidas = [];
$consumidores_extraidos = [];
if ($tabla) {
    // 2. Recorremos todas las filas de la tabla
    $filas = $xpath->query(".//tr", $tabla);
    foreach ($filas as $fila) {
        $fruta_nodo = $xpath->query(".//td[1]", $fila)->item(0);
        $anterior_nodo = $xpath->query(".//td[2]", $fila)->item(0);
        $actual_nodo = $xpath->query(".//td[4]//a[last()]", $fila)->item(0);
        // Si la fila tiene fruta y consumidor anterior, es una fila válida
        if ($fruta_nodo && $anterior_nodo) {
            $nombre_fruta = trim($fruta_nodo->nodeValue);
            
            // Limpiamos el consumidor anterior (a veces viene con un punto al final como "Kurozumi Higurashi.")
            $consumidor_anterior = rtrim(trim($anterior_nodo->nodeValue), ".");
            // Si hay un enlace en estado tomamos el nombre del personaje, si no ponemos "Desconocido"
            $consumidor_actual = $actual_nodo ? trim($actual_nodo->nodeValue) : "Desconocido";
            // Guardamos para la tabla 1 (frutas)
            $frutas_extraidas[] = [
                "nombre" => $nombre_fruta
            ];
            // Guardamos para la tabla 2 (consumidores)
            $consumidores_extraidos[] = [
                "consumidor_anterior" => $consumidor_anterior,
                "consumidor_actual" => $consumidor_actual,
                "fruta" => $nombre_fruta
            ];
        }
    }
}
// 3. Archivo SQL de destino
$sql_file = __DIR__ . "/../sql/frutas_one_piece.sql";
if (file_exists($sql_file)) {
    unlink($sql_file);
}
// Guardar primero todas las frutas
foreach ($frutas_extraidas as $fruta) {
    generar_sql_frutas($fruta, $sql_file);
}
// Guardar después todos los consumidores
foreach ($consumidores_extraidos as $consumidor) {
    generar_sql_consumidores($consumidor, $sql_file);
}
// Mensaje informativo final
if (file_exists($sql_file)) {
    echo "\n¡Archivo SQL generado con éxito =) ! Tamaño: " . filesize($sql_file) . " bytes.\n";
    echo "Total de frutas: " . count($frutas_extraidas) . " | Total de registros: " . count($consumidores_extraidos) . "\n";
} else {
    echo "\nError al generar el archivo SQL.\n";
}