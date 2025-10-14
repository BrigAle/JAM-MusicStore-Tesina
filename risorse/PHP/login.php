<?php
session_start();
require_once 'connection.php';
$connection = new mysqli($host, $user, $password, $db);

$username = $connection->real_escape_string($_POST['username']);
$password = $connection->real_escape_string($_POST['password']);
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $queryL = "SELECT * FROM utente u WHERE u.username = '$username'";
    $result = $connection->query($queryL);
    if ($result) {
        if (mysqli_num_rows(($result)) == 1) {
            $record = $result->fetch_array((MYSQLI_ASSOC));
            if (password_verify($password, $record['password'])) {
                $_SESSION['username'] = $username;
                $_SESSION['logged'] = 'true';
                // setto la variabile di sessione id con l'id dell'utente loggato
                $_SESSION['ruolo'] = $record['ruolo'];
                $_SESSION['id_utente'] = $record['id'];
                $_SESSION['stato'] = $record['stato'];
                // se l'utente è bloccato (stato = 0) non faccio il login e mostro un messaggio di errore
                if ($_SESSION['stato'] == 0) {
                    $_SESSION['error_banned'] = true;
                    header('Location: ../../Login.php');
                    exit(1);
                }
                // se l'utente ha ruolo 'amministratore' allora setto la variabile di sessione ruolo a 'amministratore'
                if ($record['ruolo'] == 'amministratore') {
                    $_SESSION['ruolo'] = 'amministratore';
                    header('Location: ../../Homepage.php');
                    exit(1);
                }
                // se l'utente ha ruolo 'gestore' allora setto la variabile di sessione ruolo a 'gestore'
                if ($record['ruolo'] == 'gestore') {
                    $_SESSION['ruolo'] = 'gestore';
                    header('Location: ../../Homepage.php');
                    exit(1);
                }
                // se l'utente ha ruolo 'cliente' allora setto la variabile di sessione ruolo a 'cliente'
                if ($record['ruolo'] == 'cliente') {
                    $_SESSION['ruolo'] = 'cliente';
                    header('Location: ../../Homepage.php');
                    exit(1);
                }
            } else {
                $_SESSION['error_password'] = true;
                header('Location: ../../Login.php');
                exit(1);
            }
        } else {
            $_SESSION['error_username'] = true;
            header('Location: ../../Login.php');
            exit(1);
        }
    } else {
        $_SESSION['error_connection'] = true;
        header('Location: ../../Login.php');
        exit(1);
    }
    $connection->close();
}
