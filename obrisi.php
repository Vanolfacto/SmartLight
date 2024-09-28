<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obrisi</title>
    <style>
        body {
    margin: 0;
    font-family: Arial, sans-serif;
    font-size: 17px;
    background-color: #f2f2f2;
    color: #333;
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
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-top: 80px; /* Adjust for navbar height */
    width: 100%;
}

.input {
    margin-bottom: 20px;
}

.text {
    font-size: 17px;
    margin-bottom: 2%;
    padding: 10px;
    width: 300px;
    box-sizing: border-box;
}

.submit {
    font-size: 17px;
    padding: 10px 20px;
    margin: 5px;
    background-color: #333;
    color: #f2f2f2;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.submit:hover {
    background-color: #555;
}

.products {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
}

.product-card {
    border: 1px solid black;
    padding: 20px;
    margin: 10px;
    text-align: left;
    display: inline-block;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}

.product-info {
    margin: 5px 0;
}

@media (max-width: 768px) {
    .product-card {
        width: calc(100% - 20px);
        margin: 10px;
    }
}
    </style>
</head>
<body>
    <div class="navbar">
        <a href="firstPage.php">Home</a>
        <a href="dodaj.php">Dodaj</a>
        <a href="izmeni.php">Izmeni</a>
        <a href="obrisi.php">Obrisi</a>
        <a href="korisnikLogout.php">Logout</a>
    </div>
    <div class="main">
        <h1>Obrišite sijalicu iz baze</h1>
        <div class="input">
            <form method="post">
                <div>
                    <div>
                        <input type="text" class="text" name="barkod" placeholder="barkod" value="<?php echo isset($_POST['barkod']) ? htmlspecialchars($_POST['barkod']) : ''; ?>">
                    </div>
                    <div>
                        <input type="submit" class="submit" name="trazi" value="Trazi">
                        <input type="submit" class="submit" name="obrisi" value="Obrisi">
                    </div>
                </div>
            </form>
        </div>
        <div class="products">
            <?php
                $conn = mysqli_connect('localhost', 'root', '', 'prodavnicasijalica');
                if ($conn->connect_error) {
                    die("Greska: " . $conn->connect_error);
                }

                $sql2 = "SELECT * FROM sijalica";
                $result = $conn->query($sql2);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $imgData = base64_encode($row['slika']);
                        $src = 'data:image;base64,' . $imgData;
                        
                        echo "<div class='product-card' data-barkod='" . $row["barkod"] . "'>";
                        echo "<div class='product-info'><img src='$src' width='125' alt='Slika sijalice'/></div>";
                        echo "<div class='product-info'>Barkod: " . $row["barkod"] . "</div>";
                        echo "<div class='product-info'>Proizvođač: " . $row["proizvodjac"] . "</div>";
                        echo "<div class='product-info'>Snaga: " . $row["snaga"] . "</div>";
                        echo "<div class='product-info'>Boja: " . $row["boja"] . "</div>";
                        echo "<div class='product-info'>Visina: " . $row["visina"] . "</div>";
                        echo "<div class='product-info'>Širina: " . $row["sirina"] . "</div>";
                        echo "<div class='product-info'>Cena: " . $row["cena"] . " RSD</div>";
                        echo "</div>";
                    }
                }
            ?>
        </div>
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["trazi"])) {
                $barkod = $_POST["barkod"];
                if (empty($barkod)) {
                    echo "<script> alert('Barkod mora biti popunjen!')</script>";
                } else {
                    $sql = "SELECT * FROM sijalica WHERE barkod = $barkod";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<script>
                            document.querySelectorAll('.product-card').forEach(card => {
                                card.style.display = 'none';
                            });
                            document.querySelector(`.product-card[data-barkod=\"$barkod\"]`).style.display = 'inline-block';
                        </script>";
                    } else {
                        echo "<script>alert('Nema podataka za dati barkod.')</script>";
                    }
                }
            }

            if (isset($_POST["obrisi"])) {
                $barkod = $_POST["barkod"];

                if (empty($barkod)) {
                    echo "<script> alert('Barkod mora biti popunjen!')</script>";
                } else {
                    $checkBarkodSql = "SELECT * FROM sijalica WHERE barkod = $barkod";
        $result = $conn->query($checkBarkodSql);
        if ($result->num_rows > 0) {
            // Obrisi
            $deleteNarudzbinaSql = "DELETE FROM narudzbina WHERE barkod = $barkod";

            if ($conn->query($deleteNarudzbinaSql) === TRUE) {
                // Obrisi
                $deleteSql = "DELETE FROM sijalica WHERE barkod = $barkod";
                if ($conn->query($deleteSql) === TRUE) {
                    echo "<script>alert('Sijalica je uspešno obrisana.')</script>";
                } else {
                    echo "<script>alert('Greška pri brisanju: " . $conn->error . "')</script>";
                }
            } else {
                echo "<script>alert('Greška pri brisanju narudžbina: " . $conn->error . "')</script>";
            }
        } else {
            echo "<script>alert('Ne možemo obrisati nepostojeću sijalicu.')</script>";
        }
    }
}
        ?>
    </div>
</body>
</html>