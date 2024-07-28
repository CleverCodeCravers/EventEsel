<?php
require_once 'config.php';

// Verbindung zur Datenbank herstellen
$conn = new mysqli($servername, $username, $password, $dbname);

// Verbindung überprüfen
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

$error_message = "";
$success_message = "";
$umfrage = null;
$moegliche_termine = [];

// Code aus der URL holen
$code = isset($_GET['code']) ? $_GET['code'] : '';

if (empty($code)) {
    $error_message = "Kein gültiger Code angegeben.";
} else {
    // Umfrage aus der Datenbank laden
    $sql = "SELECT * FROM Terminumfrage WHERE Code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $umfrage = $result->fetch_assoc();
        
        // Mögliche Termine laden
        $sql_termine = "SELECT * FROM MoeglicherTermin WHERE Terminumfrage = ? ORDER BY Datum";
        $stmt_termine = $conn->prepare($sql_termine);
        $stmt_termine->bind_param("i", $umfrage['TerminumfrageId']);
        $stmt_termine->execute();
        $result_termine = $stmt_termine->get_result();
        
        while ($termin = $result_termine->fetch_assoc()) {
            $moegliche_termine[] = $termin;
        }
    } else {
        $error_message = "Keine gültige Umfrage für diesen Code gefunden.";
    }
}

// Verarbeitung der Abstimmung
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $teilnehmer = trim($_POST['teilnehmer']);
    $gewaehlte_termine = isset($_POST['termine']) ? $_POST['termine'] : [];
    
    if (empty($teilnehmer)) {
        $error_message = "Bitte geben Sie Ihren Namen ein.";
    } elseif (empty($gewaehlte_termine)) {
        $error_message = "Bitte wählen Sie mindestens einen Termin aus.";
    } else {
        // Antwort in die Datenbank einfügen
        $sql_antwort = "INSERT INTO TerminAntwort (Teilnehmer) VALUES (?)";
        $stmt_antwort = $conn->prepare($sql_antwort);
        $stmt_antwort->bind_param("s", $teilnehmer);
        
        if ($stmt_antwort->execute()) {
            $antwort_id = $stmt_antwort->insert_id;
            
            // Gewählte Termine einfügen
            $sql_termin_antwort = "INSERT INTO TerminAntwortMoeglicherTermin (TerminAntwort, MoeglicherTermin) VALUES (?, ?)";
            $stmt_termin_antwort = $conn->prepare($sql_termin_antwort);
            
            foreach ($gewaehlte_termine as $termin_id) {
                $stmt_termin_antwort->bind_param("ii", $antwort_id, $termin_id);
                $stmt_termin_antwort->execute();
            }
            
            $success_message = "Ihre Antwort wurde erfolgreich gespeichert. Vielen Dank für Ihre Teilnahme!";
        } else {
            $error_message = "Fehler beim Speichern Ihrer Antwort. Bitte versuchen Sie es erneut.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="de" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminumfrage</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full">
    <div class="min-h-full">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900">Terminumfrage</h1>
            </div>
        </header>
        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <div class="border-4 border-dashed border-gray-200 rounded-lg p-4">
                        <?php if (!empty($error_message)): ?>
                            <p class="text-red-500 mb-4"><?php echo $error_message; ?></p>
                        <?php endif; ?>
                        <?php if (!empty($success_message)): ?>
                            <p class="text-green-500 mb-4"><?php echo $success_message; ?></p>
                        <?php endif; ?>
                        
                        <?php if ($umfrage): ?>
                            <h2 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($umfrage['Titel']); ?></h2>
                            <p class="mb-4"><?php echo nl2br(htmlspecialchars($umfrage['Beschreibung'])); ?></p>
                            
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?code=' . $code);?>" class="space-y-6">
                                <div>
                                    <label for="teilnehmer" class="block text-sm font-medium text-gray-700">Ihr Name:</label>
                                    <input type="text" id="teilnehmer" name="teilnehmer" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Mögliche Termine:</label>
                                    <?php foreach ($moegliche_termine as $termin): ?>
                                        <div class="flex items-center mt-2">
                                            <input type="checkbox" id="termin_<?php echo $termin['MoeglicherTerminId']; ?>" name="termine[]" value="<?php echo $termin['MoeglicherTerminId']; ?>" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <label for="termin_<?php echo $termin['MoeglicherTerminId']; ?>" class="ml-2 block text-sm text-gray-900">
                                                <?php echo date('d.m.Y', strtotime($termin['Datum'])); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div>
                                    <button type="submit" name="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Abstimmen
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>