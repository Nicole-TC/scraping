<?php 
/**
 * Funciones para el scraping
 */

/**
 * Descarga el HTML de una URL y lo guarda en un archivo local
 */

function scraper($url, $output_file) {

  if (!file_exists($output_file)) { // se crea el archivo por si no existiera
    touch($output_file);
    chmod($output_file, 0777);
    echo "se creo el archivo ".$output_file."\n";
  } else {
    echo "el archivo ".$output_file." ya existe"."\n";
  }

  //se agrega el siguiente bloque de codigo para evitar que se bloquee el acesso a la web
  //los diferentes header simulan ser un navegador web y que no es un bot
  //esto es necesario ya que si no se incluyen los headers la web no enviara el contenido
  
  $opciones = [
    "http" => [
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
    ]
  ];
  $contexto = stream_context_create($opciones);
  $html = file_get_contents($url, false, $contexto);



  if ($html === false) {
    die("Error al obtener el contenido de la web.\n");
  }
  
  file_put_contents($output_file, $html);
  
  echo "¡HTML obtenido con éxito! Longitud: " . strlen($html) . " caracteres.\n";

}

function generar_sql_tripulaciones($data, $sql_file) {

  if (!file_exists($sql_file)) { // se crea el archivo por si no existiera
    touch($sql_file);
    chmod($sql_file, 0777);
    echo "se creo el archivo ".$sql_file."\n";
    $file = fopen($sql_file, "w");
    fwrite($file, "INSERT INTO tripulaciones (nombre) VALUES "."\n");
    fwrite($file, '("'.$data["titulo"].'"),'."\n");
    fclose($file);
  } else {
    echo "se agrego contenido en ".$sql_file."\n";
    $file = fopen($sql_file, "a");
    fwrite($file, '("'.$data["titulo"].'"),'."\n");
    fclose($file);
  }
}

function generar_sql_personajes($data, $sql_file) {

  $contenido = file_exists($sql_file) ? file_get_contents($sql_file) : "";

  // Si todavía no se escribió el encabezado de personajes en este archivo:
  if (strpos($contenido, "INSERT INTO personajes") === false) {
    echo "se inicio la tabla personajes en ".$sql_file."\n";
    $file = fopen($sql_file, "a");
    fwrite($file, "\nINSERT INTO personajes (nombre, recompensa, id_tripulacion) VALUES "."\n");
    fwrite($file, '("'.$data["nombre_personaje"].'",'.$data["recompensa"].',"'.$data["titulo"].'"),'."\n");
    fclose($file);
  } else {
    echo "se agrego contenido en ".$sql_file."\n";
    $file = fopen($sql_file, "a");
    fwrite($file, '("'.$data["nombre_personaje"].'",'.$data["recompensa"].',"'.$data["titulo"].'"),'."\n");
    fclose($file);
  }

}


function generar_sql_frutas($data, $sql_file) {
  $dir = dirname($sql_file);
  if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
  }

  if (!file_exists($sql_file)) {
    touch($sql_file);
    chmod($sql_file, 0777);
    $file = fopen($sql_file, "w");
    fwrite($file, "INSERT INTO frutas (nombre) VALUES "."\n");
    fwrite($file, '("'.$data["nombre"].'"),'."\n");
    fclose($file);
  } else {
    $file = fopen($sql_file, "a");
    fwrite($file, '("'.$data["nombre"].'"),'."\n");
    fclose($file);
  }
}

function generar_sql_consumidores($data, $sql_file) {
  $contenido = file_exists($sql_file) ? file_get_contents($sql_file) : "";

  // Si todavía no se escribió el encabezado de consumidores:
  if (strpos($contenido, "INSERT INTO consumidores") === false) {
    $file = fopen($sql_file, "a");
    fwrite($file, "\nINSERT INTO consumidores (consumidor_anterior, consumidor_actual, fruta) VALUES "."\n");
    fwrite($file, '("'.$data["consumidor_anterior"].'","'.$data["consumidor_actual"].'","'.$data["fruta"].'"),'."\n");
    fclose($file);
  } else {
    $file = fopen($sql_file, "a");
    fwrite($file, '("'.$data["consumidor_anterior"].'","'.$data["consumidor_actual"].'","'.$data["fruta"].'"),'."\n");
    fclose($file);
  }
}
