<?php
session_start(); // Session starten
require_once 'config.php';
require_once 'database.php';
require_once 'helpers/umfrage_helpers.php';
require_once 'helpers/termin_helpers.php';


// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php"); // Umleitung zur Login-Seite
    exit();
}

$conn = getDatabaseConnection();

$error_message = "";
$success_message = "";

$code = generateUmfrageCode(16);
$teilnahmeLink = "terminumfrage.php?code=" . $code;

// Wenn das Formular abgesendet wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titel = trim($_POST['titel']);
    $beschreibung = trim($_POST['beschreibung']);
    $termine = isset($_POST['termine']) ? $_POST['termine'] : array();

    // Überprüfung der Eingabelängen
    $error_message = validateInput($titel, $beschreibung, $termine);
    
    if (empty($error_message)) {
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
    newField.className =
      "mt-1 block w-1/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50";
    container.appendChild(newField);
  }
  </script>
</head>

<body class="h-full">
  <div class="min-h-full">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">Neue Terminumfrage erstellen</h1>
        <p class="mb-4">Teilnahmelink: <a href="<?php echo $teilnahmeLink; ?>"
            class="text-blue-600 hover:underline"><?php echo $teilnahmeLink; ?></a></p>
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
                <input type="text" id="titel" name="titel" maxlength="200" required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
              </div>
              <div>
                <label for="beschreibung" class="block text-sm font-medium text-gray-700">Beschreibung:</label>
                <textarea id="beschreibung" name="beschreibung" rows="4"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Mögliche Termine:</label>
                <div id="termine-container" class="space-y-2">
                  <input type="date" name="termine[]" required
                    class="mt-1 block w-1/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <button type="button" onclick="addTerminField()"
                  class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  Weiteren Termin hinzufügen
                </button>
              </div>
              <div>
                <button type="submit"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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