<?php
session_start(); // Session starten
require_once 'database.php';
require_once 'helpers/umfrage_helpers.php';
require_once 'helpers/termin_helpers.php';

// Add this function at the beginning of the file, after the existing require statements
function getDayName($date) {
    return ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'][date('w', strtotime($date))];
}

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
  <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
  <script>
  function addTerminField() {
    var container = document.getElementById("termine-container");
    var wrapper = document.createElement("div");
    wrapper.className = "flex items-center space-x-2 mb-2";

    var newField = document.createElement("input");
    newField.type = "date";
    newField.name = "termine[]";
    newField.required = true;
    newField.className =
      "block w-1/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50";

    var deleteButton = document.createElement("button");
    deleteButton.type = "button";
    deleteButton.className = "text-red-600 hover:text-red-800";
    deleteButton.innerHTML = '<i data-feather="trash-2"></i>';
    deleteButton.onclick = function() {
      container.removeChild(wrapper);
    };

    wrapper.appendChild(newField);
    wrapper.appendChild(deleteButton);
    container.appendChild(wrapper);

    feather.replace();
  }

  function generateDates() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    const selectedDays = Array.from(document.querySelectorAll('input[name="days[]"]:checked')).map(cb => cb.value);

    if (!startDate || !endDate || selectedDays.length === 0) {
      alert('Bitte wählen Sie Start- und Enddatum sowie mindestens einen Wochentag aus.');
      return;
    }

    let currentDate = new Date(startDate);
    const end = new Date(endDate);

    while (currentDate <= end) {
      const dayName = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'][currentDate.getDay()];
      if (selectedDays.includes(dayName)) {
        const dateString = currentDate.toISOString().split('T')[0];
        const existingFields = document.querySelectorAll('#termine-container input[type="date"]');
        const alreadyExists = Array.from(existingFields).some(field => field.value === dateString);

        if (!alreadyExists) {
          addTerminField();
          const newFields = document.querySelectorAll('#termine-container input[type="date"]');
          newFields[newFields.length - 1].value = dateString;
        }
      }
      currentDate.setDate(currentDate.getDate() + 1);
    }
    feather.replace();
  }

  document.addEventListener('DOMContentLoaded', (event) => {
    feather.replace();
  });
  </script>
</head>

<body class="h-full">
  <div class="min-h-full">
    <header class="bg-white shadow">
      <nav class="bg-indigo-600 p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
          <h1 class="text-white text-2xl font-bold">Umfragen</h1>
          <ul class="flex space-x-4">
            <li><a href="terminumfrage-edit.php" class="text-white hover:underline">Terminumfrage</a></li>
            <li><a href="textoptionumfrage.php" class="text-white hover:underline">Textoptionumfrage</a></li>
          </ul>
        </div>
      </nav>
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900">Neue Terminumfrage erstellen</h2>
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
                  <div class="flex items-center space-x-2 mb-2">
                    <input type="date" name="termine[]" required
                      class="block w-1/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">
                      <i data-feather="trash-2"></i>
                    </button>
                  </div>
                </div>
                <button type="button" onclick="addTerminField()"
                  class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  Weiteren Termin hinzufügen
                </button>
              </div>

              <!-- New section for pattern generator -->
              <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900">Terminmuster-Generator (optional)</h3>
                <div class="mt-2 space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Wochentage auswählen:</label>
                    <div class="mt-2 space-x-2">
                      <?php
                      $days = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
                      foreach ($days as $day) {
                        echo "<label class='inline-flex items-center'>";
                        echo "<input type='checkbox' name='days[]' value='$day' class='rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50'>";
                        echo "<span class='ml-2'>$day</span>";
                        echo "</label>";
                      }
                      ?>
                    </div>
                  </div>
                  <div class="flex space-x-4">
                    <div>
                      <label for="start-date" class="block text-sm font-medium text-gray-700">Startdatum:</label>
                      <input type="date" id="start-date" name="start-date"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div>
                      <label for="end-date" class="block text-sm font-medium text-gray-700">Enddatum:</label>
                      <input type="date" id="end-date" name="end-date"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                  </div>
                  <div>
                    <button type="button" onclick="generateDates()"
                      class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                      Termine hinzufügen
                    </button>
                  </div>
                </div>
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