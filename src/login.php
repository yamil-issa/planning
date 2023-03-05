<?php
ob_start();
session_start();

require '../model/connection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {

  // Get form data
  $username = $_POST["username"];
  $password = $_POST["password"];

  $filter = [
    'nom' => $username,
    'pwd' => $password
  ];
  $query = new MongoDB\Driver\Query($filter);

  $result = $manager->executeQuery('planning.users', $query)->toArray();

  if (count($result) == 1) {
    $_SESSION["loggedin"] = true;
    $_SESSION["username"] = $username;
    header('Location: index.php');
  } else {
    $login_error = "identifiant ou mot de passe incorrect";
  }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charSet="UTF-8" />
   <meta httpEquiv="X-UA-Compatible" content="IE=edge" />
   <link rel="icon" href="%PUBLIC_URL%/favicon.ico" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Login</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="./assets/css/style.css">

    
<h1>Login</h1>
<?php if (isset($login_error)) echo "<p class='error_label'>" . $login_error . "</p>"; ?>
<form class="login_form" method="POST" action="">
  <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">Identifiant</label>
    <input type="text" name="username" class="form-control" aria-describedby="emailHelp">
  </div>
  <div class="mb-3">
    <label for="exampleInputPassword1" class="form-label">Mot de passe</label>
    <input type="password" name="password" class="form-control">
  </div>
  <button type="submit" name="submit" class="btn btn-primary">Se connecter</button>
</form>

</body>
</html>
