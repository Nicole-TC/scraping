<?php
/**
 * DOMDocument :Transforma el texto plano (HTML) en un 
 * árbol de Nodos legibles por objetos.
 * 
 * DOMXPath :Se conecta a ese árbol para poder buscar.
 * 
 * query() :Usa la combinación de // (dónde buscar), [] (filtrar)
 * y @ (mirar atributos html) para aislar el dato exacto antes de limpiarlo.
 * 
 * Una etiqueta entera es un Elemento Nodo (<div>, <p>, <a>).
 * El texto dentro de una etiqueta es un Texto Nodo.
 * Un atributo dentro de una etiqueta es un Atributo Nodo (class="precio").
 * 
 * // = Buscar en todo el documento (Global).
 * .// = Buscar solo dentro de este nodo (Relativa al contexto actual).
 * 
 */
require_once "../funciones.php";

$html = "../html/scrap-one-piece.html";

$dom = new DOMDocument();
// El @ oculta advertencias si el HTML del sitio objetivo no es perfecto
@$dom->loadHTMLFile($html); 
#var_dump($dom);exit;
$xpath = new DOMXPath($dom);
#var_dump($xpath);exit;
// Buscamos todos los contenedores de informacion sobre los personajes
//estructura de html que quiero sacar la informacion: 

$tablas = $xpath->query("//table[@class='wikitable']");


$tripulaciones_extraidas = [];
$personajes_extraidos = [];


foreach ($tablas as $tabla) {
    /**
     * Busca el título ÚNICAMENTE dentro de este libro
     * .// hace referencia a los mismo al igual que item(0)
     * se refiere al $libro del ciclo actual de foreach 
    */

    //nombre de las tripulaciones es decir el titulo de la tabla
    $titulo_nodo = $xpath->query("preceding::span[@class='mw-headline'][1]", $tabla)->item(0);
    $nombre_tripulacion = $titulo_nodo ? trim($titulo_nodo->nodeValue) : "Desconocido";

    $tripulaciones_extraidas[] = [
        "titulo" => $nombre_tripulacion
    ];

    //nombre de los persnajes y su recompensa
    $filas = $xpath->query(".//tr", $tabla);
    foreach ($filas as $fila) {
        $nombre_nodo = $xpath->query(".//td[1]//a", $fila)->item(0);
        $recompensa_nodo = $xpath->query(".//td[3]/b", $fila)->item(0);

        if ($nombre_nodo && $recompensa_nodo) {
            $texto_recompensa = trim($recompensa_nodo->nodeValue);

             // Extraemos solo el número limpio, ignorando corchetes como [90] o textos
            if (preg_match('/[0-9]+(\.[0-9]+)*/', $texto_recompensa, $coincidencias)) {
                $recompensa = str_replace(".", "", $coincidencias[0]);
            } else {
                // Si no tiene número (ej: "?"), le ponemos 0
                $recompensa = 0;
            }

            $personajes_extraidos[] = [
                "nombre_personaje" => trim($nombre_nodo->getAttribute('title')),
                "recompensa" =>$recompensa, //se quitan los puntos porque en la bd puede dar error
                "titulo" => $nombre_tripulacion
            ];
        }
    }

    // Si acabamos de procesar la tabla de los Marines, terminamos el scraping
    if ($nombre_tripulacion === "Marines") {
        break; // Sale del bucle y no lee nada más
    }
}

$sql_file = __DIR__ . "/../sql/one_piece.sql";

// 1. Guardar tripulaciones
foreach ($tripulaciones_extraidas as $tripulacion) {
    generar_sql_tripulaciones($tripulacion, $sql_file);
}
// 2. Guardar personajes
foreach ($personajes_extraidos as $personaje) {
    generar_sql_personajes($personaje, $sql_file);
}

if(file_exists($sql_file)) {

    echo "\n¡Archivo SQL generado con éxito =) ! Tamaño: " . filesize($sql_file) . " bytes.\n";
    echo "Total de tripulaciones: " . count($tripulaciones_extraidas) . " | Total de personajes: " . count($personajes_extraidos) . "\n";
} else {
    echo "\nError al generar el archivo SQL.\n";
}
