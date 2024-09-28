<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naručite Sijalicu</title>
    <style>
        body {
            margin: 0;
            font-family: Arial;
        }
        .navbar {
            overflow: hidden;
            background-color: #333;
            opacity: 0.9;
        }
        .navbar a {
            float: left;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            font-size: 17px;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .navbar a:active {
            background-color: #04AA6D;
            color: white;
        }
        .main {
            display: flex;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }
        .card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            width: 300px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f5f5f5;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .card img {
            max-width: 100%;
            border-radius: 8px;
        }
        .card .details {
            margin-top: 10px;
            text-align: left;
            width: 100%;
        }
        .card .details p {
            margin: 5px 0;
        }
        .card .details .barcode {
            font-weight: bold;
            color: #04AA6D;
        }
        .card form {
            margin-top: 10px;
        }
        .card form .button {
            padding: 10px 20px;
            background-color: #04AA6D;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .card form .button:hover {
            background-color: #028a4d;
        }
        .card form .quantity {
            width: 60px;
            text-align: center;
        }
        .main h1 {
            color: #04AA6D;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="korisnikPocetna.php">Home</a>
        <a href="<?php echo isset($_SESSION['name']) ? 'korisnikObrisi.php' : 'korisnikLogin.php'; ?>">Korpa</a>
        <?php
        if (isset($_SESSION['name'])) {
            echo '<a href="korisnikLogout.php">Logout</a>';
        } else {
            echo '<a href="korisnikLogin.php">Login</a>';
            echo '<a href="registracija.php">Registracija</a>';
        }
        ?>
    </div>
    <div class="main">
    <?php 
        if (isset($_SESSION['name'])) {
            echo "<h1>Dobrodošli {$_SESSION['name']}! Izaberite sijalice!</h1>";
        } else {
            echo "<h1>Dobrodošli! Molimo vas da se prijavite ili registrujete kako biste nastavili sa kupovinom.</h1>";
        }
        ?>
        <?php 
        $conn = mysqli_connect('localhost', 'root', '', 'prodavnicasijalica');

        if ($conn->connect_error) {
            die("Greška: " . $conn->connect_error);
        }

        $sql2 = "SELECT * FROM sijalica";
        $result = $conn->query($sql2);

        if ($result->num_rows > 0) {
            echo "<div class='cards'>";
            while ($row = $result->fetch_assoc()) {
                $imgData = base64_encode($row['slika']);
                $src = 'data:image;base64,' . $imgData;

                echo "<div class='card'>";
                echo "<img src='$src' alt='Image'/>";
                echo "<div class='details'>";
                echo "<p class='barcode'>Barkod: " . $row['barkod'] . "</p>";
                echo "<p>Proizvođač: " . $row['proizvodjac'] . "</p>";
                echo "<p>Snaga: " . $row['snaga'] . "</p>";
                echo "<p>Boja: " . $row['boja'] . "</p>";
                echo "<p>Visina: " . $row['visina'] . "</p>";
                echo "<p>Širina: " . $row['sirina'] . "</p>";
                echo "<p>Cena: " . $row['cena'] . " RSD</p>";
                echo "</div>";
                
                if (isset($_SESSION['name'])) {
                    echo "<form method='post' action='{$_SERVER['PHP_SELF']}'>";
                    echo "<input type='hidden' name='barkod' value='" . $row['barkod'] . "'/>";
                    echo "<label for='kolicina'>Količina:</label>";
                    echo "<input type='number' class='quantity' name='kolicina' value='1' min='1'/>";
                    echo "<input type='submit' class='button' name='kupi' value='Kupi'/>";
                    echo "</form>";
                } else {
                    echo "<form method='post' action='korisnikLogin.php'>";
                    echo "<input type='submit' class='button' value='Prijavite se da kupite'/>";
                    echo "</form>";
                }

                echo "</div>";
            }
            echo "</div>";
        }

        if (isset($_SESSION['name']) && isset($_POST['kupi'])) {
            $barkod = $_POST['barkod'];
            $username = $_SESSION['name'];
            $kolicina = $_POST['kolicina'];
        
            $sqlCheck = "SELECT * FROM narudzbina WHERE barkod='$barkod' AND username='$username'";
            $resultCheck = $conn->query($sqlCheck);
        
            if ($resultCheck->num_rows > 0) {
                // Ako narudžbina već postoji, ažuriraj količinu
                $row = $resultCheck->fetch_assoc();
                $newQuantity = $row['kolicina'] + $kolicina;
                $sqlUpdate = "UPDATE narudzbina SET kolicina='$newQuantity' WHERE barkod='$barkod' AND username='$username'";
                
                if ($conn->query($sqlUpdate) === TRUE) {
                    echo "<script>alert('Ažurirali ste količinu sijalice u korpi!')</script>";
                } else {
                    echo "<script>alert('Greška prilikom ažuriranja količine.')</script>";
                }
            } else {
                // Ako narudžbina ne postoji, dodaj novi zapis
                $id = rand(1, 1000000);
                $sqlInsert = "INSERT INTO narudzbina (id, korisnikID, barkod, username, kolicina) 
                              VALUES ('$id', (SELECT id FROM kupac WHERE name = '$username'),'$barkod', '$username', '$kolicina')";
                
                if ($conn->query($sqlInsert) !== TRUE) {
                    echo "<script>alert('Greška prilikom dodavanja u korpu.')</script>";
                } else {
                    echo "<script>alert('Dodali ste sijalicu u korpu!')</script>";
                }
            }
        }
        ?>
    </div>
</body>
</html>