<?php
    session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Korpa</title>
    <style>
        body {
            margin: 0;
            font-family: Arial;
        }
        .navbar {
            overflow: hidden;
            background-color: #333;
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
        .card img {
            max-width: 100%;
            border-radius: 8px;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
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
            background-color: #04AA6D;
        }
        .card form .button:hover {
            background-color: #028a4d;
        }
        .total {
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
            color: #04AA6D; 
        }
        .checkout-btn {
            margin-top: 20px;
            text-align: center;
        }
        .checkout-btn a {
            display: inline-block;
            padding: 15px 30px;
            background-color: #04AA6D;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-size: 18px;
            font-weight: bold;
        }
        .checkout-btn a:hover {
            background-color: #028a4d;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="korisnikPocetna.php">Home</a>
        <a href="korisnikObrisi.php">Korpa</a>
        <a href="korisnikLogout.php">Logout</a>
    </div>
    <div class="main">
    <h1>Korpa</h1>
    
    
    <?php 
 $conn= mysqli_connect('localhost', 'root', '', 'prodavnicasijalica');
    
 if ($conn->connect_error) {
     die("Greska: " . $conn->connect_error);
 }

 $username= $_SESSION["name"];
    
 if(isset($_POST['obrisi'])){
    $barkod = $_POST['barkod'];
    $sqlDelete = "DELETE FROM narudzbina WHERE barkod = '$barkod' AND username = '$username'";
    if ($conn->query($sqlDelete) === TRUE) {
        header("Location: korisnikObrisi.php");
        exit();
    } else {
        echo"<script>alert('Greska')</script>";
    }
}

    $sql2="SELECT s.*, (n.kolicina) AS quantity, SUM(s.cena) AS total_price 
                 FROM sijalica s 
                 INNER JOIN narudzbina n ON s.barkod = n.barkod 
                 WHERE n.username='$username' 
                 GROUP BY s.barkod";
    $result= $conn->query($sql2);
    
    
    if ($result->num_rows > 0) {
        $grandTotal = 0;
        echo "<div class='cards'>";
        while ($row = $result->fetch_assoc()) {
            $imgData = base64_encode($row['slika']);
            $src = 'data:image;base64,' . $imgData;
            $totalItemPrice = $row['cena'] * $row['quantity'];
            $grandTotal += $totalItemPrice;

            echo "<div class='card'>";
            echo "<img src='$src' width='150' alt='Image'/>";
            echo "<div class='details'>";
            echo "<p class='barcode'>Barkod: " . $row['barkod'] . "</p>";
            echo "<p>Proizvodjac: " . $row['proizvodjac'] . "</p>";
            echo "<p>Snaga: " . $row['snaga'] . "</p>";
            echo "<p>Boja: " . $row['boja'] . "</p>";
            echo "<p>Visina: " . $row['visina'] . "</p>";
            echo "<p>Sirina: " . $row['sirina'] . "</p>";
            echo "<p>Cena: " . $row['cena'] . " RSD</p>";
            echo "<p>Količina: " . $row['quantity'] . "</p>";
            echo "<p>Ukupna cena artikla: " . $totalItemPrice . " RSD</p>";
            echo "</div>";
            echo "<form method='post' action='{$_SERVER['PHP_SELF']}'>";
            echo "<input type='hidden' name='barkod' value='" . $row['barkod'] . "'/>";
            echo "<input type='submit' class='button' name='obrisi' value='Obrisi'/>";
            echo "</form>";
            echo "</div>";
        }
        echo "</div>";
        echo "<div class='total'>Ukupna cena: " . $grandTotal . " RSD</div>";

        // Prikaži dugme za plaćanje ako korisnik ima nešto u korpi
        echo "<div class='checkout-btn'>";
        echo "<a href='placanje.php'>Plati</a>";
        echo "</div>";
    } else {
        echo "Nemate ništa u korpi!";
    }
?>

    </div>
    
</body>
</html>

