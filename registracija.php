<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ulogaID = 1;
    $name = $_POST['name'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password === $confirm_password) {
        $conn = mysqli_connect('localhost', 'root', '', 'prodavnicasijalica');

        if ($conn->connect_error) {
            die("Greška: " . $conn->connect_error);
        }

        $result = $conn->query("SELECT MAX(id) AS max_id FROM kupac");
        $row = $result->fetch_assoc();
        $new_id = $row['max_id'] + 1;
        $sql = "INSERT INTO kupac (id, ulogaID, name, password) VALUES ('$new_id', '$ulogaID','$name', '$password')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Uspešno ste se registrovali!');</script>";
        } else {
            echo "<script>alert('Greška prilikom registracije.');</script>";
        }
        $conn->close();
    } else {
        echo "<script>alert('Lozinke se ne poklapaju.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registracija</title>
    <style>
        body {
            margin: 0;
            font-family: Arial;

            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #333;
            overflow: hidden;
            z-index: 1;
        }
        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .navbar a:active {
            background-color: #04AA6D;
            color: white;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        .container h1 {
            color: #04AA6D;
        }
        .container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .container button {
            background-color: #04AA6D;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .container button:hover {
            background-color: #028a4d;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="korisnikLogin.php">Login</a>
        <a href="registracija.php">Registracija</a>
    </div>
    <div class="container">
        <h1>Registracija</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="text" name="name" placeholder="Korisničko ime" required>
            <input type="password" name="password" placeholder="Lozinka" required>
            <input type="password" name="confirm_password" placeholder="Potvrdite lozinku" required>
            <button type="submit">Registruj se</button>
        </form>
    </div>
</body>
</html>