<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/models/Website.php';

echo "Hello World<br><br>";

try {
    $w = new Website();
    
    echo "Nombre de sites demandés: 10<br>";
    $websites = $w->getAll(10, 0);
    
    echo "Nombre de sites trouvés: " . count($websites) . "<br><br>";
    
    if (empty($websites)) {
        echo "Aucun site trouvé dans la base de données.<br>";
        echo "Vérifiez que la table 'websites' contient des données.<br>";
    } else {
        foreach($websites as $website){
            echo "Site: " . htmlspecialchars($website['name']) . " (ID: " . $website['id'] . ")<br>";
        }
    }
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "<br>";
    echo "Trace: " . $e->getTraceAsString();
}
