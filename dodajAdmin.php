<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj</title>
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
    justify-content: center;
    align-items: center;
    flex-direction: column;
    text-align: center;
    padding: 20px;
}

h1, h2 {
    color: #333;
}

.input form {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px;
}

.input form div {
    margin-bottom: 15px;
}

.input form input[type="text"],
.input form input[type="file"] {
    width: calc(100% - 20px);
    padding: 10px;
    margin: 5px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 17px;
    box-sizing: border-box;
}

.input form input[type="submit"] {
    width: 100%;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    background-color: #04AA6D;
    color: white;
    font-size: 17px;
}

.input form input[type="submit"]:hover {
    background-color: #45a049;
}

.sijalice {
    width: 100%;
    max-width: 800px;
    margin-top: 40px;
}

.sijalica-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.sijalica-details {
    text-align: left;
    flex: 1;
}

.sijalica-details p {
    margin: 5px 0;
}

.image-container {
    max-width: 150px;
    overflow: hidden;
    border-radius: 8px;
}

.image-container img {
    max-width: 100%;
    transition: transform 0.2s ease;
}

.image-container img:hover {
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .sijalica-card {
        flex-direction: column;
        align-items: flex-start;
    }

    .image-container {
        margin-top: 10px;
    }
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
        <h1>Dodajte novu sijalicu u bazu</h1>
        <div class="input">
            <form method="post" enctype="multipart/form-data">
                <div>
                    <input type="text" class="text" name="proizvodjac" placeholder="Proizvođač">
                    <input type="text" class="text" name="snaga" placeholder="Snaga">               
                    <input type="text" class="text" name="boja" placeholder="Boja">
                    <input type="text" class="text" name="visina" placeholder="Visina">
                    <input type="text" class="text" name="sirina" placeholder="Širina">
                    <input type="text" class="text" name="cena" placeholder="Cena">
                    <input type="file" name="slika" accept="image/*">
                </div>
                <div>
                    <input type="submit" class="submit" name="submit" value="Dodaj">
                </div>
            </form>
        </div>
        <div class="sijalice">
            <h2>Baza Sijalica</h2>
            <?php
                $conn = mysqli_connect('localhost', 'root', '', 'prodavnicasijalica');

                if ($conn->connect_error) {
                    die("Greška: " . $conn->connect_error);
                }

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $proizvodjac = $_POST["proizvodjac"];
                    $snaga = $_POST["snaga"];
                    $boja = $_POST["boja"];
                    $visina = $_POST["visina"];
                    $sirina = $_POST["sirina"];
                    $cena = $_POST["cena"];
                
                    // Provera da li je slika uspešno učitana
                    if (isset($_FILES['slika']) && $_FILES['slika']['error'] === UPLOAD_ERR_OK) {
                        $slika = addslashes(file_get_contents($_FILES['slika']['tmp_name']));
                
                        if (empty($proizvodjac) || empty($snaga) || empty($boja) || empty($visina) || empty($sirina) || empty($cena) || empty($slika)) {
                            echo "<script> alert('Sva polja, uključujući sliku, moraju biti popunjena.')</script>";
                        } else {
                            $barkod = rand(100000, 999999);
                            $sql = "INSERT INTO sijalica (barkod, proizvodjac, snaga, boja, visina, sirina, cena, slika) VALUES ('$barkod', '$proizvodjac', '$snaga', '$boja', '$visina', '$sirina', '$cena', '$slika')";
                
                            if ($conn->query($sql) === TRUE) {
                                echo "<script> alert('Uspešno ste uneli novu sijalicu!')</script>";
                            } else {
                                echo "<script> alert('Greška: " . $sql . " " . $conn->error . "')</script>";
                            }
                        }
                    } else {
                        echo "<script> alert('Došlo je do greške prilikom učitavanja slike. Molimo pokušajte ponovo.')</script>";
                    }
                }

                $sql2 = "SELECT * FROM sijalica";
                $result = $conn->query($sql2);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $imgData = base64_encode($row['slika']);
                        $src = 'data:image;base64,' . $imgData;

                        echo "<div class='sijalica-card'>";
                        echo "<div class='sijalica-details'>";
                        echo "<p><strong>Barkod:</strong> " . $row['barkod'] . "</p>";
                        echo "<p><strong>Proizvođač:</strong> " . $row['proizvodjac'] . "</p>";
                        echo "<p><strong>Snaga:</strong> " . $row['snaga'] . "</p>";
                        echo "<p><strong>Boja:</strong> " . $row['boja'] . "</p>";
                        echo "<p><strong>Visina:</strong> " . $row['visina'] . "</p>";
                        echo "<p><strong>Širina:</strong> " . $row['sirina'] . "</p>";
                        echo "<p><strong>Cena:</strong> " . $row['cena'] . " RSD</p>";
                        echo "</div>";
                        echo "<div class='sijalica-details image-container'>";
                        echo "<img src='$src' width='125' alt='Slika sijalice'/>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Trenutno nema sijalica u bazi.</p>";
                }

                $conn->close();
            ?>
        </div>
    </div>
</body>
</html>