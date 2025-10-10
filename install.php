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
    email VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    stato BOOLEAN DEFAULT TRUE NOT NULL,
    ruolo ENUM('amministratore','cliente','gestore') DEFAULT 'cliente' NOT NULL
);";

if ($connessione->query($sql_table_utente) === FALSE) {
    echo "Errore nella creazione della tabella utente: " . $connessione->error;
}
// controllo se gia' esiste un utente registrato con il ruolo di admin,
// se non esiste inserisco nel database l'utente admin con hashing della password
$check_admin = "SELECT * FROM utente WHERE ruolo = 'amministratore';";
$result_admin = $connessione->query($check_admin);
if ($result_admin->num_rows === 0) {
    $pwd_admin = password_hash('admin', PASSWORD_DEFAULT);
    $sql_admin = "  INSERT INTO utente 
                            (username, email, password, ruolo) 
                    VALUES  ('admin','admin@gmail.com','$pwd_admin','amministratore');";
    if ($connessione->query($sql_admin) === FALSE) {
        echo "Errore nell'inserimento dell'utente admin: " . $connessione->error;
    }
}

// voglio prendere l'id dell'admin appena creato
$idAdmin = $connessione->insert_id;

// inserisco utente brigale con hashing della password
$check_brigale = "SELECT * FROM utente WHERE username = 'brigale';";
$result_utente = $connessione->query($check_brigale);
if ($result_utente->num_rows === 0) {
    $pwd_brigale = password_hash('ale', PASSWORD_DEFAULT);
    $sql_brigale = "INSERT INTO utente (username, email, password, ruolo) 
                    VALUES ('brigale','brigale@gmail.com','$pwd_brigale','gestore');";
    if ($connessione->query($sql_brigale) === FALSE) {
        echo "Errore nell'inserimento dell'utente brigale: " . $connessione->error;
    }
}
//prendo l'id generato
$idBrigale = $connessione->insert_id;

$check_giovyears = "SELECT * FROM utente WHERE username = 'giovyears';";
$result_utente = $connessione->query($check_giovyears);
if ($result_utente->num_rows === 0) {
    $pwd_giovyears = password_hash('giovyears', PASSWORD_DEFAULT);
    $sql_giovyears = "INSERT INTO utente (username, email, password, ruolo) 
    VALUES ('giovyears','giovyears@gmail.com','$pwd_giovyears','gestore');";
    if ($connessione->query($sql_giovyears) === FALSE) {
        echo "Errore nell'inserimento dell'utente giovyears: " . $connessione->error;
    }
}
//prendo l'id generato
$idGiovanni = $connessione->insert_id;

$check_orione = "SELECT * FROM utente WHERE username = 'orione';";
$result_utente = $connessione->query($check_orione);
if ($result_utente->num_rows === 0) {
    $pwd_orione = password_hash('orione', PASSWORD_DEFAULT);
    $sql_orione = "INSERT INTO utente (username, email, password, ruolo) VALUES ('orione','orione@gmail.com','$pwd_orione','cliente');";
    if ($connessione->query($sql_orione) === FALSE) {
        echo "Errore nell'inserimento dell'utente orione: " . $connessione->error;
    }
}
//prendo l'id generato
$idOrione = $connessione->insert_id;

// voglio inserire nel file xml i dati degli utenti appena creati
$xmlFile = "risorse/XML/utenti.xml";

// Creo il documento XML da zero
$doc = new DOMDocument('1.0', 'UTF-8');
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

// Creo il root <utenti> con attributo XSD
$utenti = $doc->createElement('utenti');
$utenti->setAttributeNS(
    "http://www.w3.org/2001/XMLSchema-instance",
    "xsi:noNamespaceSchemaLocation",
    "utenti.xsd"
);
$doc->appendChild($utenti);

// 1) Admin
$utenteAdmin = $doc->createElement('utente');
$utenteAdmin->appendChild($doc->createElement('nome', 'Admin'));
$utenteAdmin->appendChild($doc->createElement('cognome', 'Admin'));
$utenteAdmin->appendChild($doc->createElement('telefono', '0000000000'));
$utenteAdmin->appendChild($doc->createElement('indirizzo', 'Via Roma, 1'));
$utenteAdmin->appendChild($doc->createElement('reputazione', 0));
$utenteAdmin->appendChild($doc->createElement('portafoglio', 0));
$utenteAdmin->appendChild($doc->createElement('crediti', 0));
$utenteAdmin->appendChild($doc->createElement('data_iscrizione', date('Y-m-d')));
$utenteAdmin->setAttribute('id', $idAdmin);
$utenti->appendChild($utenteAdmin);

// 2) Alessandro Brighenti
$utenteBrigale = $doc->createElement('utente');
$utenteBrigale->appendChild($doc->createElement('nome', 'Alessandro'));
$utenteBrigale->appendChild($doc->createElement('cognome', 'Brighenti'));
$utenteBrigale->appendChild($doc->createElement('telefono', '1234567890'));
$utenteBrigale->appendChild($doc->createElement('indirizzo', 'Via Milano, 10'));
$utenteBrigale->appendChild($doc->createElement('reputazione', 0));
$utenteBrigale->appendChild($doc->createElement('portafoglio', 0));
$utenteBrigale->appendChild($doc->createElement('crediti', 0));
$utenteBrigale->appendChild($doc->createElement('data_iscrizione', date('Y-m-d')));
$utenteBrigale->setAttribute('id', $idBrigale);
$utenti->appendChild($utenteBrigale);

// 3) Giovanni Tagliaferri
$utenteGiovanni = $doc->createElement('utente');
$utenteGiovanni->appendChild($doc->createElement('nome', 'Giovanni'));
$utenteGiovanni->appendChild($doc->createElement('cognome', 'Tagliaferri'));
$utenteGiovanni->appendChild($doc->createElement('telefono', '0987654321'));
$utenteGiovanni->appendChild($doc->createElement('indirizzo', 'Via Napoli, 5'));
$utenteGiovanni->appendChild($doc->createElement('reputazione', 0));
$utenteGiovanni->appendChild($doc->createElement('portafoglio', 0));
$utenteGiovanni->appendChild($doc->createElement('crediti', 0));
$utenteGiovanni->appendChild($doc->createElement('data_iscrizione', date('Y-m-d')));
$utenteGiovanni->setAttribute('id', $idGiovanni);
$utenti->appendChild($utenteGiovanni);

// 4) Mattia Aquilina
$utenteOrione = $doc->createElement('utente');
$utenteOrione->appendChild($doc->createElement('nome', 'Mattia'));
$utenteOrione->appendChild($doc->createElement('cognome', 'Aquilina'));
$utenteOrione->appendChild($doc->createElement('telefono', '1122334455'));
$utenteOrione->appendChild($doc->createElement('indirizzo', 'Via Roma, 2'));
$utenteOrione->appendChild($doc->createElement('reputazione', 0));
$utenteOrione->appendChild($doc->createElement('portafoglio', 0));
$utenteOrione->appendChild($doc->createElement('crediti', 0));
$utenteOrione->appendChild($doc->createElement('data_iscrizione', date('Y-m-d')));
$utenteOrione->setAttribute('id', $idOrione);
$utenti->appendChild($utenteOrione);

// Salvo nel file XML
$doc->save($xmlFile);


// chiudo la connessione e reindirizzo alla homepage
$connessione->close();
header("Location: homepage.php");
exit(1);
