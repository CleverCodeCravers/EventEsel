<?php
session_start(); // Start session
require_once 'database.php';
require_once 'helpers/umfrage_helpers.php';
require_once 'helpers/textoption_helpers.php';

// Check if the user is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

$conn = getDatabaseConnection();

$error_message = "";
$success_message = "";

$code = generateUmfrageCode(16);
$teilnahmeLink = "textoptionumfrage.php?code=" . $code;

// If the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titel = trim($_POST['titel']);
    $beschreibung = trim($_POST['beschreibung']);
    $optionen = isset($_POST['optionen']) ? $_POST['optionen'] : array();
    // Validate input
    //$error_message = validateTextoptionInput($titel, $beschreibung, $optionen);
    
    if (empty($error_message)) {
        // SQL query to insert the text option survey
        $sql = "INSERT INTO Textoptionenumfrage (Code, Titel, Beschreibung) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $code, $titel, $beschreibung);

        if ($stmt->execute()) {
            $textoptionenumfrage_id = $stmt->insert_id;
            $stmt->close();

            // Insert the text options
            $sql_option = "INSERT INTO Textoption (Textoptionenumfrage, Text) VALUES (?, ?)";
            $stmt_option = $conn->prepare($sql_option);

            foreach ($optionen as $option) {
                if (!empty($option)) {
                    $stmt_option->bind_param("is", $textoptionenumfrage_id, $option);
                    $stmt_option->execute();
                }
            }

            $stmt_option->close();
            $success_message = "Neue Textoptionenumfrage erfolgreich erstellt. <a href='$teilnahmeLink' class='text-blue-600 hover:underline'>Hier geht's zur Abstimmung...</a>";
        } else {
            $error_message = "Fehler beim Erstellen der Textoptionenumfrage: " . $conn->error;
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
  <title>Neue Textoptionumfrage erstellen</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
  <script>
  function addOptionField() {
    var container = document.getElementById("optionen-container");
    var wrapper = document.createElement("div");
    wrapper.className = "flex items-center space-x-2 mb-2";

    var newField = document.createElement("input");
    newField.type = "text";
    newField.name = "optionen[]";
    newField.required = true;
    newField.className =
      "block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50";

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
            <li><a href="textoptionumfrage-edit.php" class="text-white hover:underline">Textoptionumfrage</a></li>
          </ul>
        </div>
      </nav>
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900">Neue Textoptionumfrage erstellen</h2>
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
                <label class="block text-sm font-medium text-gray-700">Textoptionen:</label>
                <div id="optionen-container" class="space-y-2">
                  <div class="flex items-center space-x-2 mb-2">
                    <input type="text" name="optionen[]" required
                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">
                      <i data-feather="trash-2"></i>
                    </button>
                  </div>
                </div>
                <button type="button" onclick="addOptionField()"
                  class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  Weitere Option hinzufügen
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