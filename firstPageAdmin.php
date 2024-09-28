<?php
session_start();

$conn = mysqli_connect('localhost', 'root', '', 'prodavnicasijalica');

// Provera konekcije
if ($conn->connect_error) {
    die("Greska: " . $conn->connect_error);
}

// Brisanje narudžbine

if (isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];

    // Prvo obrišite podatke iz tabele proizvod_narudzbina
    $sql_delete_proizvodi = "DELETE FROM proizvod_narudzbina WHERE narudzbinaID = $order_id";
    if ($conn->query($sql_delete_proizvodi) === TRUE) {
        // Ako je brisanje uspešno, obrišite iz tabele narudzbina_stavka
        $sql_delete_narudzbina = "DELETE FROM narudzbina_stavka WHERE id = $order_id";
        if ($conn->query($sql_delete_narudzbina) === TRUE) {
            echo '<script>alert("Narudžbina je uspešno obrisana.")</script>';
        } else {
            echo '<script>alert("Greška pri brisanju narudžbine iz tabele narudzbina_stavka.")</script>';
        }
    } else {
        echo '<script>alert("Greška pri brisanju narudzbine iz tabele proizvod_narudzbina.")</script>';
    }
}


$sql = "SELECT ns.id AS stavkaID, GROUP_CONCAT(pn.id ORDER BY pn.id) AS stavke, ns.datum, ns.imePrezime, ns.adresa, ns.grad, ns.email, ns.telefon, ns.nacinPlacanja, GROUP_CONCAT(pn.barkod ORDER BY pn.barkod) AS barkodovi, GROUP_CONCAT(pn.proizvodjac ORDER BY pn.barkod) AS proizvodjaci, GROUP_CONCAT(pn.kolicina ORDER BY pn.barkod) AS kolicine, GROUP_CONCAT(s.cena ORDER BY s.barkod) AS cene, pn.kolicina, SUM(pn.ukupnaCena) as ukupnaCena, GROUP_CONCAT(s.slika ORDER BY s.barkod) AS slike,  k.name AS username
        FROM narudzbina_stavka ns
        INNER JOIN proizvod_narudzbina pn ON ns.id = pn.narudzbinaID
        INNER JOIN sijalica s ON pn.barkod = s.barkod
        INNER JOIN kupac k ON ns.korisnikID = k.id
        GROUP BY ns.id
        ORDER BY ns.datum DESC";
$result = $conn->query($sql);

if (!$result) {
    echo '<p>Greska pri izvrsavanju upita: ' . mysqli_error($conn) . '</p>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
        }
        .navbar a {
            float: left;
            display: block;
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
            text-align: center;
            margin: 20px;
        }
        .order-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s ease;
        }
        .order-card:hover {
            transform: translateY(-5px);
        }
        .order-details {
            text-align: left;
            flex: 1;
        }
        .order-details p {
            margin: 5px 0;
        }
        .order-details .image-container {
            max-width: 150px;
            overflow: hidden;
        }
        .order-details img {
            max-width: 100%;
            border-radius: 8px;
            transition: transform 0.2s ease;
        }
        .order-details img:hover {
            transform: scale(1.1);
        }
        .stavka {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }
        .stavka p {
            margin: 5px 0;
        }
        .delete-button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .delete-button:hover {
            background-color: #cc0000;
        }
        h1 {
            margin-bottom: 20px;
        }
        .empty-message {
            font-style: italic;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="firstPageAdmin.php">Home</a>
        <a href="prikazNaloga.php">Nalozi</a>
        <a href="promenaUloga.php">Uloge</a>
        <a href="dodajAdmin.php">Dodaj</a>
        <a href="izmeniAdmin.php">Izmeni</a>
        <a href="obrisiAdmin.php">Obrisi</a>
        <a href="korisnikLogout.php">Logout</a>
    </div>
    <div class="main">
        <h1>Dobrodošli <?php echo $_SESSION['username']; ?>!</h1>
        <h2>Trenutne Narudžbine:</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                $slike = explode(',', $row['slike']);
                $barkodovi = explode(',', $row['barkodovi']);
                $proizvodjaci = explode(',', $row['proizvodjaci']);
                $kolicine = explode(',', $row['kolicine']);
                $cene = explode(',', $row['cene']);
                $stavke = explode(',', $row['stavke']);
                $maxItems = max(count($slike), count($barkodovi), count($proizvodjaci), count($kolicine), count($cene));
                ?>
                <div class="order-card">
                    <div class="order-details">
                        <p><strong>Narudžbina ID:</strong> <?php echo $row['stavkaID']; ?></p>
                        <p><strong>Stavke ID:</strong> <?php echo implode(', ', $stavke); ?></p>
                        <p><strong>Datum:</strong> <?php echo $row['datum']; ?></p>
                        <p><strong>Username:</strong> <?php echo $row['username']; ?></p>
                        <p><strong>Ime i Prezime:</strong> <?php echo $row['imePrezime']; ?></p>
                        <p><strong>Adresa:</strong> <?php echo $row['adresa']; ?></p>
                        <p><strong>Grad:</strong> <?php echo $row['grad']; ?></p>
                        <p><strong>Email:</strong> <?php echo $row['email']; ?></p>
                        <p><strong>Telefon:</strong> <?php echo $row['telefon']; ?></p>
                        <p><strong>Nacin Placanja:</strong> <?php echo $row['nacinPlacanja']; ?></p>
                        <p><strong>Ukupna cena:</strong> <?php echo $row['ukupnaCena']; ?> RSD</p>
                        <p><strong>Stavke:</strong></p>
                        <?php for ($i = 0; $i < $maxItems; $i++): ?>
                            <?php if (isset($barkodovi[$i]) || isset($proizvodjaci[$i]) || isset($kolicine[$i]) || isset($cene[$i])): ?>    
                            <div class="stavka">                            
                                <?php if (isset($barkodovi[$i])): ?>
                                    <p><strong>Barkod:</strong> <?php echo $barkodovi[$i]; ?></p>
                                <?php endif; ?>
                                <?php if (isset($proizvodjaci[$i])): ?>
                                    <p><strong>Proizvođač:</strong> <?php echo $proizvodjaci[$i]; ?></p>
                                <?php endif; ?>
                                <?php if (isset($kolicine[$i])): ?>
                                        <p><strong>Količina:</strong> <?php echo $kolicine[$i]; ?></p>
                                <?php endif; ?>
                                <?php if (isset($cene[$i])): ?>
                                        <p><strong>Cena:</strong> <?php echo $cene[$i]; ?> RSD</p>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <form method="post" action="">
                        <input type="hidden" name="order_id" value="<?php echo $row['stavkaID']; ?>" />
                        <button type="submit" name="delete_order" class="delete-button">Obriši narudžbinu</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="empty-message">Nemate trenutnih narudžbina.</p>
        <?php endif; ?>
    </div>
</body>
</html>