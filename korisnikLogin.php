<?php
session_start();

$conn = mysqli_connect('localhost', 'root', '', 'prodavnicasijalica');

// Provera konekcije
if ($conn->connect_error) {
    die("Greska: " . $conn->connect_error);
}

if(isset($_POST['submit'])){
    $username=$_POST['username'];
    $password=$_POST['password'];

    // Provera za korisnike (kupce)
    $select=mysqli_query($conn, "SELECT * FROM kupac WHERE name='$username' AND password='$password'");
    $row= mysqli_fetch_array($select);

    if(is_array($row)){
        $_SESSION["name"]=$row['name'];
        $_SESSION["password"]=$row['password'];
        header("Location: korisnikPocetna.php");
    }

    // Provera za radnike
    $select=mysqli_query($conn, "SELECT * FROM radnik WHERE username='$username' AND password='$password'");
    $row= mysqli_fetch_array($select);

    if(is_array($row)){
        $_SESSION["username"]=$row['username'];
        $_SESSION["password"]=$row['password'];
        header("Location: firstPage.php");
    }

    // Provera za admine
    $select=mysqli_query($conn, "SELECT * FROM admin WHERE username='$username' AND password='$password'");
    $row= mysqli_fetch_array($select);

    if(is_array($row)){
        $_SESSION["username"]=$row['username'];
        $_SESSION["password"]=$row['password'];
        header("Location: firstPageAdmin.php");
    }

    // Ako nije pronađen ni korisnik ni radnik
    if (!isset($_SESSION["name"]) && !isset($_SESSION["username"])) {
        echo '<script>alert("Netacan username ili lozinka")</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 30px;
            width: 300px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .form-input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .submit-btn {
            width: 100%;
            background-color: #04AA6D;
            color: white;
            border: none;
            padding: 12px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .submit-btn:hover {
            background-color: #028a4d;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Molim Vas prijavite se</h2>
        <form method="post">
            <input type="text" class="form-input" name="username" placeholder="Username" required/><br><br>
            <input type="password" class="form-input" name="password" placeholder="Password" required /><br><br>
            <input type="submit" class="submit-btn" name="submit"  value="Login"/>
        </form>
        <?php
            if (isset($_SESSION["name"]) || isset($_SESSION["username"])) {
                echo '<div class="success-message">Uspesno ste se prijavili.</div>';
            } else if (isset($_POST['submit'])) {
                echo '<div class="error-message">Netacan username ili lozinka.</div>';
            }
        ?>
    </div>
</body>
</html>
