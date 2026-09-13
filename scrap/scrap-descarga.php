<?php
/**
 * La funcion scraper descargara el contenido html de una pagina web
 * y lo almacenara en un archivo .html localmente
 * 
 * En la variable $output_file colocar el nombre del archivo .html que se desea crear,
 * para almacenar el contenido html descargado para scrapear y poder analizarlo posteriormente.
 * 
 * En la variable $url colocar la url correspondiente de la pagina que se desea scrapear.
 * 
 * Ejecutar en Docker:
 * docker exec scraping-alg php /var/www/html/scrap-descarga.php
 */

require_once __DIR__ . "/funciones.php";

$dir = __DIR__ . "/html"; // Carpeta donde se guardara el contenido html
// para los personajes y tripulacion:
//$output_file = $dir . "/scrap-one-piece.html";

// URL de one piece fandom sobre las recompensas
//$url = "https://onepiece.fandom.com/es/wiki/Recompensas";

// para las frutas del diablo y sus portadores:
$output_file = $dir . "/scrap-one-piece-frutas.html";
$url= "https://onepiece.fandom.com/es/wiki/Fruta_del_diablo";

scraper($url, $output_file);