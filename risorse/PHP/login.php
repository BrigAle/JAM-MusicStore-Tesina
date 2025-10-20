<?php
session_start();
require_once 'connection.php';

$connection = new mysqli($host, $user, $password, $db);
if ($connection->connect_error) {
    $_SESSION['error_connection'] = true;
    header('Location: ../../login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $connection->real_escape_string($_POST['username']);
    $password = $connection->real_escape_string($_POST['password']);

    // salvo l'username per reinserirlo in caso di errore
    $_SESSION['old_data'] = ['username' => $username];

    $query = "SELECT * FROM utente WHERE username = '$username'";
    $result = $connection->query($query);

    if ($result && $result->num_rows === 1) {
        $record = $result->fetch_assoc();

        if (password_verify($password, $record['password'])) {

            // ✅ controllo subito se l'utente è bannato
            if ($record['stato'] == 0) {
                $_SESSION['error_banned'] = true;
                header('Location: ../../login.php');
                exit();
            }

            // login consentito
            $_SESSION['username'] = $username;
            $_SESSION['logged'] = 'true';
            $_SESSION['ruolo'] = $record['ruolo'];
            $_SESSION['id_utente'] = $record['id'];
            $_SESSION['stato'] = $record['stato'];

            // redirect in base al ruolo
            header('Location: ../../homepage.php');
            exit();
        } else {
            $_SESSION['error_password'] = true;
            header('Location: ../../login.php');
            exit();
        }
    } else {
        $_SESSION['error_username'] = true;
        header('Location: ../../login.php');
        exit();
    }

    $connection->close();
}
?>
