<?php


require_once 'risorse/PHP/connection.php';

echo "<h3>Eliminazione database e XML</h3>";

$connection = @new mysqli($host, $user, $password);
if ($connection->connect_error) {
    echo "Connessione al server non riuscita: " . $connection->connect_error . "<br>";
} else {

    $db_exists = $connection->query("SHOW DATABASES LIKE '$db'");

    if ($db_exists && $db_exists->num_rows > 0) {
        $sql_drop_db = "DROP DATABASE $db";
        if ($connection->query($sql_drop_db) === TRUE) {
            echo "✅ Database <strong>$db</strong> eliminato con successo.<br>";
        } else {
            echo "❌ Errore nell'eliminazione del database: " . $connection->error . "<br>";
        }
    } else {
        echo "ℹ️ Il database <strong>$db</strong> non esiste o non è accessibile.<br>";
    }

    $connection->close();
}


$xmlFile = __DIR__ . '/risorse/XML/utenti.xml';
$xml_eliminato = false;

if (file_exists($xmlFile)) {
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;

    if ($dom->load($xmlFile)) {
        $root = $dom->documentElement;

        // Elimina tutti i nodi figli (svuotamento XML)
        while ($root->hasChildNodes()) {
            $root->removeChild($root->firstChild);
        }

        // Salva il file svuotato
        $dom->save($xmlFile);
        echo "✅ Elementi in <strong>utenti.xml</strong> cancellati con successo.<br>";
        $xml_eliminato = true;
    } else {
        echo "⚠️ Errore nel caricamento del file XML (file non valido).<br>";
    }
} else {
    echo "ℹ️ Il file <strong>utenti.xml</strong> non esiste.<br>";
}

if ((!isset($db_exists) || !$db_exists || $db_exists->num_rows == 0) && !$xml_eliminato) {
    echo "<br><strong>❌ Nessun database o file XML trovati.</strong>";
} else {
    echo "<br><strong>✅ Operazione completata.</strong>";
}
?>
