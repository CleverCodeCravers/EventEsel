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

function generateUmfrageCode($passwordLength) {
    $validCharacters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    $maximalLength = $passwordLength;
    $result = "";
    
    for ($i = 0; $i < $maximalLength; $i++) {
        $randomNumber = rand(0, strlen($validCharacters) - 1);
        $result .= $validCharacters[$randomNumber];
    }
    
    return $result;
}

$code = generateUmfrageCode(16);
$teilnahmeLink = "terminumfrage.php?code=" . $code;

// Wenn das Formular abgesendet wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titel = trim($_POST['titel']);
    $beschreibung = trim($_POST['beschreibung']);
    $termine = isset($_POST['termine']) ? $_POST['termine'] : array();
    
    // Überprüfung der Eingabelängen
    if (strlen($titel) > 200) {
        $error_message = "Der Titel darf maximal 200 Zeichen lang sein.";
    } elseif (strlen($beschreibung) > 16777215) { // MEDIUMTEXT Limit
        $error_message = "Die Beschreibung ist zu lang.";
    } elseif (empty($termine)) {
        $error_message = "Bitte geben Sie mindestens einen möglichen Termin ein.";
    } else {
        // SQL-Abfrage zum Einfügen der Terminumfrage
        $sql = "INSERT INTO Terminumfrage (Code, Titel, Beschreibung) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $code, $titel, $beschreibung);
        
        if ($stmt->execute()) {
            $terminumfrage_id = $stmt->insert_id;
            $stmt->close();
            
            // Einfügen der möglichen Termine
            $sql_termin = "INSERT INTO MoeglicherTermin (Terminumfrage, Datum) VALUES (?, ?)";
            $stmt_termin = $conn->prepare($sql_termin);
            
            foreach ($termine as $termin) {
                if (!empty($termin)) {
                    $stmt_termin->bind_param("is", $terminumfrage_id, $termin);
                    $stmt_termin->execute();
                }
            }
            
            $stmt_termin->close();
            $success_message = "Neue Terminumfrage erfolgreich erstellt. <a href='$teilnahmeLink' class='text-blue-600 hover:underline'>Hier geht's zur Abstimmung...</a>";
        } else {
            $error_message = "Fehler beim Erstellen der Terminumfrage: " . $conn->error;
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
    <title>Neue Terminumfrage erstellen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function addTerminField() {
            var container = document.getElementById("termine-container");
            var newField = document.createElement("input");
            newField.type = "date";
            newField.name = "termine[]";
            newField.required = true;
            newField.className = "mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50";
            container.appendChild(newField);
        }
    </script>
</head>
<body class="h-full">
    <div class="min-h-full">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900">Neue Terminumfrage erstellen</h1>
                <p class="mb-4">Teilnahmelink: <a href="<?php echo $teilnahmeLink; ?>" class="text-blue-600 hover:underline"><?php echo $teilnahmeLink; ?></a></p>
            </div>
        </header>
        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <div class="border-4 border-dashed border-gray-200 rounded-lg p-4">
                        <?php
                        if (!empty($error_message)) {
                            echo "<p class='text-red-500 mb-4'>$error_message</p>";
                        }
                        if (!empty($success_message)) {
                            echo "<p class='text-green-500 mb-4'>$success_message</p>";
                        }
                        ?>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="space-y-6">
                            <div>
                                <label for="titel" class="block text-sm font-medium text-gray-700">Titel:</label>
                                <input type="text" id="titel" name="titel" maxlength="200" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="beschreibung" class="block text-sm font-medium text-gray-700">Beschreibung:</label>
                                <textarea id="beschreibung" name="beschreibung" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Mögliche Termine:</label>
                                <div id="termine-container" class="space-y-2">
                                    <input type="date" name="termine[]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <button type="button" onclick="addTerminField()" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Weiteren Termin hinzufügen
                                </button>
                            </div>
                            <div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Erstellen
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>