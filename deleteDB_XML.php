<?php
// voglio creare un file php che elimini il database e il file xml associato
require_once 'risorse/PHP/connection.php';
$connection = new mysqli($host, $user, $password, $db);
if ($connection->connect_error) {
    die("Connessione fallita: " . $connection->connect_error);
}
// elimino il database
$sql_drop_db = "DROP DATABASE $db";
if ($connection->query($sql_drop_db) === TRUE) {
    echo "Database eliminato con successo.";
} else {
    echo "Errore nell'eliminazione del database: " . $connection->error;
}
echo "<br>";
$connection->close();
// elimino il gli elemti <utente> dal file xml mantenendo la struttura e lo xsd
$xmlFile = __DIR__ . '/risorse/XML/utenti.xml';

if (file_exists($xmlFile)) {
    // Carico il file XML
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->load($xmlFile);

    // Recupero l’elemento root <utenti>
    $root = $dom->documentElement;

    // Cancello tutti i nodi <utente>
    while ($root->hasChildNodes()) {
        $root->removeChild($root->firstChild);
    }

    // Salvo il file senza rimuovere root e attributi
    $dom->save($xmlFile);

    echo "XML resettato con successo.";
} else {
    echo "Il file XML non esiste.";
}
?>

