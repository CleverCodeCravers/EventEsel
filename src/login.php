<?php
session_start();
require_once 'config.php';
require_once 'database.php';

$error_message = "";

// Admin-Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['admin_login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $conn = getDatabaseConnection();
    if ($conn === null) {
        $error_message = "Datenbankverbindung fehlgeschlagen. Bitte überprüfen Sie Ihre Zugangsdaten.";
    } else {
        $sql = "SELECT * FROM Admin WHERE Username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['PasswordHash'])) {
                $_SESSION['admin_logged_in'] = true;
                header("Location: terminumfrage-edit.php"); // Weiterleitung nach erfolgreicher Anmeldung
                exit();
            } else {
                $error_message = "Ungültige Anmeldedaten.";
            }
        } else {
            $error_message = "Ungültige Anmeldedaten.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="h-full bg-gray-100">
  <div class="flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded shadow-md w-96">
      <h1 class="text-2xl font-bold mb-4">Admin Login</h1>
      <?php if (!empty($error_message)) { echo "<p class='text-red-500'>$error_message</p>"; } ?>
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-4">
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700">Benutzername:</label>
          <input type="text" id="username" name="username" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Passwort:</label>
          <input type="password" id="password" name="password" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
        <button type="submit" name="admin_login"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          Anmelden
        </button>
      </form>
    </div>
  </div>
</body>

</html>