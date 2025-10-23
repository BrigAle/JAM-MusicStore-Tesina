<?php
require_once('risorse/PHP/connection.php');

/* Connessione al server */
$connessione = new mysqli($host, $user, $password);
if ($connessione->connect_error) {
    exit("Connessione al server fallita: " . $connessione->connect_error);
}

/* Creo database se non esiste */
$sql_db = "CREATE DATABASE IF NOT EXISTS $db";
if ($connessione->query($sql_db) === FALSE) {
    exit("Errore nella creazione del database: " . $connessione->error);
}

/* Connessione al database */
$connessione = new mysqli($host, $user, $password, $db);
if ($connessione->connect_error) {
    exit("Connessione al database fallita: " . $connessione->connect_error);
}

/* Creo tabella utente (come la tua) */
$sql_table_utente = "CREATE TABLE IF NOT EXISTS utente(
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL UNIQUE,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    stato BOOLEAN DEFAULT TRUE NOT NULL,
    ruolo ENUM('amministratore','cliente','gestore') DEFAULT 'cliente' NOT NULL
);";
if ($connessione->query($sql_table_utente) === FALSE) {
    exit("Errore nella creazione della tabella utente: " . $connessione->error);
}

/* Funzione: inserisce se non esiste e restituisce SEMPRE l'id */
function getOrCreateUserId(mysqli $conn, string $username, string $email, string $ruolo): int {
    // c'è già?
    $q = $conn->prepare("SELECT id FROM utente WHERE username = ? LIMIT 1");
    $q->bind_param("s", $username);
    $q->execute();
    $res = $q->get_result();
    if ($row = $res->fetch_assoc()) {
        $q->close();
        return (int)$row['id'];
    }
    $q->close();

    // crea (password = username, hash)
    $pwd_hash = password_hash($username, PASSWORD_DEFAULT);
    $ins = $conn->prepare("INSERT INTO utente (username, email, password, ruolo) VALUES (?, ?, ?, ?)");
    $ins->bind_param("ssss", $username, $email, $pwd_hash, $ruolo);
    if (!$ins->execute()) {
        $err = $ins->error;
        $ins->close();
        exit("Errore nell'inserimento dell'utente $username: " . $err);
    }
    $id = (int)$conn->insert_id;
    $ins->close();
    return $id;
}

/* --- Inserimenti (password = username) --- */
$idAdmin   = getOrCreateUserId($connessione, 'admin',   'admin@gmail.com',         'amministratore');
$idBrigale = getOrCreateUserId($connessione, 'brigale', 'brigale@gmail.com',       'gestore');
$idGiov    = getOrCreateUserId($connessione, 'giovyears','giovyears@gmail.com',    'gestore');
$idOrione  = getOrCreateUserId($connessione, 'orione',  'orione@gmail.com',        'cliente');
$idElectro = getOrCreateUserId($connessione, 'electro', 'electro@gmail.com',       'cliente');
$idDrew93  = getOrCreateUserId($connessione, 'drew93',  'drew93@gmail.com',        'cliente');
$idViktor  = getOrCreateUserId($connessione, 'viktor96','viktor96@gmail.com',      'cliente');
$idDagn    = getOrCreateUserId($connessione, 'dagnelle','dagnellejojo@gmail.com',  'cliente');
$idBrigDan = getOrCreateUserId($connessione, 'brigdan', 'brigdan@hotmail.it',      'cliente');

/* --- Costruzione utenti.xml con i dati esatti --- */
$xmlFile = "risorse/XML/utenti.xml";

$doc = new DOMDocument('1.0', 'UTF-8');
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

$root = $doc->createElement('utenti');
$root->setAttributeNS(
    "http://www.w3.org/2001/XMLSchema-instance",
    "xsi:noNamespaceSchemaLocation",
    "utenti.xsd"
);
$doc->appendChild($root);

/* helper per creare <utente> */
function appendUtente(
    DOMDocument $doc, DOMElement $root, int $id,
    string $nome, string $cognome, string $tel, string $indirizzo,
    $reputazione, $portafoglio, $crediti, string $data_iscrizione
) {
    $u = $doc->createElement('utente');
    $u->setAttribute('id', (string)$id);
    $u->appendChild($doc->createElement('nome', $nome));
    $u->appendChild($doc->createElement('cognome', $cognome));
    $u->appendChild($doc->createElement('telefono', $tel));
    $u->appendChild($doc->createElement('indirizzo', $indirizzo));
    $u->appendChild($doc->createElement('reputazione', (string)$reputazione));
    $u->appendChild($doc->createElement('portafoglio', (string)$portafoglio));
    $u->appendChild($doc->createElement('crediti', (string)$crediti));
    $u->appendChild($doc->createElement('data_iscrizione', $data_iscrizione));
    $root->appendChild($u);
}

/* mappatura dati (come da tua tabella/XML) */
appendUtente($doc, $root, $idAdmin,   'Admin',    'Admin',     '0000000000', 'Via Roma, 1',                                 0,     0,    0,   '2025-01-02');
appendUtente($doc, $root, $idBrigale, 'Alessandro','Brighenti','1234567890', 'Via Milano, 10',                               0,     0,    0,   '2025-01-02');
appendUtente($doc, $root, $idGiov,    'Giovanni', 'Tagliaferri','0987654321','Via Napoli, 5',                                0,     0,    0,   '2025-01-02');
appendUtente($doc, $root, $idOrione,  'Mattia',   'Aquilina',  '1122334455', 'Via Roma, 2',                                  21.5,  2624.89, 598,'2025-01-02');
appendUtente($doc, $root, $idElectro, 'Denis',    'Boitor',    '343434343',  'Via Firenze 25, LT',                           0,     0,    0,   '2025-01-02');
appendUtente($doc, $root, $idDrew93,  'Andrea',   'Mattarelli','324234232',  'Via monte terminillo 48, LT 04100',            0,     5000, 0,   '2025-10-23');
appendUtente($doc, $root, $idViktor,  'Vincenzo', 'Ferrara',   '324232425',  'Via cavata 1, LT 04100',                       0,     4000, 200, '2025-10-23');
appendUtente($doc, $root, $idDagn,    'Daniele',  'Ardovini',  '393465728',  'Via Sandro Pertini 11, Ceccano FR',            13,    3000, 160, '2025-10-23');
appendUtente($doc, $root, $idBrigDan, 'Daniele',  'Brighenti', '3234455',    'VIa gaeta 49 LT 04100',                        0,     0,    0,   '2025-10-23');

/* Salvataggio XML */
$doc->save($xmlFile);

/* Chiudo e reindirizzo */
$connessione->close();
header("Location: homepage.php");
exit(1);
