<?php
// Establecer la codificación de salida
header('Content-Type: text/html; charset=UTF-8');

// Mostrar información sobre la configuración
echo "<h1>Prueba de codificación UTF-8</h1>";

// Probar caracteres especiales
$texto = "Esta es una prueba con caracteres especiales: áéíóúñÁÉÍÓÚÑ";
echo "<p>Texto original: $texto</p>";
echo "<p>Codificación actual: " . mb_detect_encoding($texto) . "</p>";

// Mostrar caracteres en diferentes codificaciones
echo "<p>UTF-8: " . mb_convert_encoding($texto, 'UTF-8') . "</p>";
echo "<p>ISO-8859-1: " . mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8') . "</p>";
echo "<p>UTF-8 desde ISO-8859-1: " . mb_convert_encoding(mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8'), 'UTF-8', 'ISO-8859-1') . "</p>";

// Mostrar texto como JSON
echo "<h2>Prueba JSON</h2>";
$array = ['texto' => $texto];
echo "<p>JSON_UNESCAPED_UNICODE: " . json_encode($array, JSON_UNESCAPED_UNICODE) . "</p>";
echo "<p>Sin JSON_UNESCAPED_UNICODE: " . json_encode($array) . "</p>";

// Información de PHP
echo "<h2>Información del servidor</h2>";
echo "<p>Versión de PHP: " . phpversion() . "</p>";
echo "<p>default_charset: " . ini_get('default_charset') . "</p>";
echo "<p>mb_internal_encoding: " . mb_internal_encoding() . "</p>";
?> 