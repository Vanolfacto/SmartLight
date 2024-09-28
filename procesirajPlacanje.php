<?php
session_start();


$conn = mysqli_connect('localhost', 'root', '', 'prodavnicasijalica');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Funkcija sa unos podataka
function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($input))));
}

// Preuzimanje podataka korisnika
$username = $_SESSION["name"];
$datum = date("Y-m-d H:i:s"); // Current date and time

// Preuzimanje podataka o placanju
$imePrezime = sanitize($_POST['imePrezime']);
$adresa = sanitize($_POST['adresa']);
$grad = sanitize($_POST['grad']);
$email = sanitize($_POST['email']);
$telefon = sanitize($_POST['telefon']);
$nacinPlacanja = sanitize($_POST['payment_method']);


$insertSql = "INSERT INTO narudzbina_stavka (korisnikID, datum, imePrezime, adresa, grad, email, telefon, nacinPlacanja)
              VALUES ((SELECT id FROM kupac WHERE name = '$username'), '$datum', '$imePrezime', '$adresa', '$grad', '$email', '$telefon', '$nacinPlacanja')";

if (mysqli_query($conn, $insertSql)) {

    $orderID = mysqli_insert_id($conn);
    
    // Preuzimanje detalja korpe
    $cartSql = "SELECT * FROM narudzbina WHERE username = '$username'";
    $cartResult = mysqli_query($conn, $cartSql);
    
    while ($cartRow = mysqli_fetch_assoc($cartResult)) {
        $barkod = $cartRow['barkod'];
        $kolicina = $cartRow['kolicina'];
        
        // Preuzimanje informacija iz tabele 'sijalica'
        $productSql = "SELECT * FROM sijalica WHERE barkod = '$barkod'";
        $productResult = mysqli_query($conn, $productSql);
        
        if ($productResult && mysqli_num_rows($productResult) > 0) {
            $productRow = mysqli_fetch_assoc($productResult);
            $barkod = $productRow['barkod'];
            $proizvodjac = $productRow['proizvodjac'];
            $cena = $productRow['cena'];

            $totalPrice = $cena * $kolicina;
            
            
            $insertProductSql = "INSERT INTO proizvod_narudzbina (narudzbinaID, barkod, proizvodjac, kolicina, ukupnaCena)
                                 VALUES ('$orderID', '$barkod', '$proizvodjac', '$kolicina', '$totalPrice')";
            
            mysqli_query($conn, $insertProductSql);
        } else {
            echo "Greška prilikom dobijanja informacija o proizvodu za barkod: $barkod";
        }
    }
    
    
    echo "<!DOCTYPE html>
          <html lang='en'>
          <head>
              <meta charset='UTF-8'>
              <meta name='viewport' content='width=device-width, initial-scale=1.0'>
              <title>Narudžbina uspešno poslata</title>
              <style>
                  body {
                      font-family: Arial, sans-serif;
                      margin: 0;
                      padding: 20px;
                      background-color: #f0f0f0;
                      text-align: center;
                  }
                  .container {
                      max-width: 600px;
                      margin: 20px auto;
                      background-color: #fff;
                      padding: 20px;
                      border-radius: 8px;
                      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                  }
                  h1 {
                      color: #04AA6D;
                  }
                  p {
                      margin-bottom: 20px;
                  }
                  a {
                      text-decoration: none;
                      color: #fff;
                      background-color: #04AA6D;
                      padding: 10px 20px;
                      border-radius: 5px;
                      transition: background-color 0.3s ease;
                  }
                  a:hover {
                      background-color: #028a4d;
                  }
              </style>
          </head>
          <body>
              <div class='container'>
                  <h1>Narudžbina uspešno poslata!</h1>
                  <p>Vaša narudžbina broj $orderID je uspešno primljena.</p>
                  <p>Bićete preusmereni na početnu stranicu.</p>
                  <a href='korisnikPocetna.php'>Vrati se na početnu</a>
              </div>
          </body>
          </html>";
    
    // Brisanje korpe
    $deleteCartSql = "DELETE FROM narudzbina WHERE username = '$username'";
    mysqli_query($conn, $deleteCartSql);
    
   
    mysqli_close($conn);
    exit(); 
} else {
    echo "Greška prilikom slanja narudžbine: " . mysqli_error($conn);
}
?>