<?php
require_once('risorse/PHP/connection.php');

$connessione = new mysqli($host, $user, $password);
if ($connessione->connect_error) {
    exit("Connessione al server fallita: " . $connessione->connect_error);
}
// creo database
$sql_db = "CREATE DATABASE IF NOT EXISTS $db";
if ($connessione->query($sql_db) === FALSE) {
    echo "Errore nella creazione del database " . $connessione->error;
}
// connessione al database
$connessione = new mysqli($host, $user, $password, $db);
if ($connessione->connect_error) {
    exit("Connessione al database fallita: " . $connessione->connect_error);
}

// creo tabella utente
$sql_table_utente = "CREATE TABLE IF NOT EXISTS utente(
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(30) NOT NULL,
        nome VARCHAR(50) NOT NULL,
        cognome VARCHAR(50) NOT NULL,
        indirizzo VARCHAR(100) NOT NULL,
        telefono VARCHAR(20) NOT NULL,
        email VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL,
        ruolo ENUM ('cliente', 'gestore', 'amministratore') NOT NULL DEFAULT 'cliente',
        reputazione FLOAT DEFAULT 0.00,
        stato BOOLEAN DEFAULT 1,
        portafoglio FLOAT DEFAULT 0.00,
        crediti FLOAT DEFAULT 0,
        data_iscrizione DATE NOT NULL DEFAULT CURRENT_DATE,
        UNIQUE (username, email)
    );";

if ($connessione->query($sql_table_utente) === FALSE) {
    echo "Errore nella creazione della tabella utente: " . $connessione->error;
}
// controllo se gia' esiste un utente registrato con il ruolo di admin,
// se non esiste inserisco nel database l'utente admin con hashing della password
$check_admin = "SELECT * FROM utente WHERE ruolo = 'admin';";
$result_admin = mysqli_query($connessione, $check_admin);
if (!mysqli_num_rows($result_admin) > 0) {
    $pwd_admin = password_hash('admin', PASSWORD_DEFAULT);
    $sql_admin = "INSERT INTO utente (username, nome, cognome, indirizzo, telefono, email, password, ruolo) VALUES ('admin','Admin','Admin','Via Roma 1','3333333333','admin@gmail.com','$pwd_admin','admin');";
    if ($connessione->query($sql_admin) === FALSE) {
        echo "Errore nell'inserimento dell'utente admin: " . $connessione->error;
    }
}
// inserisco utente brigale con hashing della password
$check_brigale = "SELECT * FROM utente WHERE username = 'brigale';";
$result_utente = mysqli_query($connessione, $check_brigale);
if (!mysqli_num_rows($result_utente) > 0) {
    $pwd_brigale = password_hash('ale', PASSWORD_DEFAULT);
    $sql_brigale = "INSERT INTO utente (username, nome, cognome, indirizzo, telefono, email, password) VALUES ('brigale','Alessandro','Brighenti','Via Milano 2','3222222222','brigale@gmail.com','$pwd_brigale');";
    if ($connessione->query($sql_brigale) === FALSE) {
        echo "Errore nell'inserimento dell'utente brigale: " . $connessione->error;
    }
}

// chiudo la connessione e reindirizzo alla homepage
$connessione->close();
header("Location: homepage.php");
exit(1);
?>