<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Izmeni</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
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
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background-color: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

h1, h2 {
    text-align: center;
    color: #4CAF50;
}

.input {
    margin: 20px 0;
}

.input form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.input .find,
.input .newValues {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.input .text, 
.input input[type="file"], 
.input input[type="submit"] {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.input input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s;
}

.input input[type="submit"]:hover {
    background-color: #45a049;
}

.table {
    margin: 20px 0;
}

.sijalice {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}

.sijalica-card {
    display: flex;
    flex-direction: column;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 10px;
    background-color: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    flex-basis: calc(33.333% - 20px);
    transition: transform 0.3s, box-shadow 0.3s;
}
.sijalica-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}

.sijalica-details {
    text-align: center;
    margin-bottom: 10px;
}

.sijalica-details img {
    max-width: 100%;
    border-radius: 10px;
}

.image-container {
    text-align: center;
}
    </style>
    <script>
                function enableFields() {
                    document.getElementsByName("nproizvodjac")[0].disabled = false;
                    document.getElementsByName("nsnaga")[0].disabled = false;
                    document.getElementsByName("nboja")[0].disabled = false;
                    document.getElementsByName("nvisina")[0].disabled = false;
                    document.getElementsByName("nsirina")[0].disabled = false;
                    document.getElementsByName("ncena")[0].disabled = false;
                    document.getElementsByName("nslika")[0].disabled = false;
                    document.getElementsByName("izmeni")[0].disabled = false;
                }

                function refreshData() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'load_data.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById('all-sijalice').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
            </script>
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
        <h1>Izmenite sijalicu u bazi</h1>
        <div class="input">
            <form method="post" enctype="multipart/form-data" onsubmit="return enableFields()">
                <div class="find">
                    <input type="text" class="text" name="barkod" placeholder="Barkod" value="<?php echo isset($_POST['barkod']) ? htmlspecialchars($_POST['barkod']) : ''; ?>">
                    <input type="submit" name="trazi" value="Trazi">
                </div>
                <div class="newValues">
                    <input type="text" class="text" name="nproizvodjac" placeholder="Novi proizvođač" disabled>
                    <input type="text" class="text" name="nsnaga" placeholder="Nova snaga" disabled>
                    <input type="text" class="text" name="nboja" placeholder="Nova boja" disabled>
                    <input type="text" class="text" name="nvisina" placeholder="Nova visina" disabled>
                    <input type="text" class="text" name="nsirina" placeholder="Nova širina" disabled>
                    <input type="text" class="text" name="ncena" placeholder="Nova cena" disabled>
                    <input type="file" name="nslika" accept="image/*" disabled>
                    <input type="submit" name="izmeni" class="izmeniButton" value="Izmeni" disabled>
                </div>
            </form>
        </div>
        <div class="table">
            <h2>Baza</h2>
            <?php 
                $conn = mysqli_connect('localhost', 'root', '', 'prodavnicasijalica');

                if ($conn->connect_error) {
                    die("Greška: " . $conn->connect_error);
                }

                $sql2 = "SELECT * FROM sijalica";
                $result = $conn->query($sql2);

                if ($result->num_rows > 0) {
                    echo "<div class='sijalice' id='all-sijalice'>";
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
                    echo "</div>";
                } else {
                    echo "<p>Trenutno nema sijalica u bazi.</p>";
                }

                $conn->close();
            ?>
            <?php
                $conn = mysqli_connect('localhost', 'root', '', 'prodavnicasijalica');

                if ($conn->connect_error) {
                    die("Greška: " . $conn->connect_error);
                }

                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["trazi"])) {
                    $barkod = $_POST["barkod"];

                    if (empty($barkod)) {
                        echo "<script>alert('Barkod mora biti popunjen!')</script>";
                    } else {
                        $sql = "SELECT * FROM sijalica WHERE barkod = $barkod";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            echo "<script>document.getElementById('all-sijalice').style.display = 'none';</script>";
                            echo "<script>enableFields();</script>";
                            echo "<div class='sijalice' id='searched-sijalice'>";
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
                            echo "</div>";
                        } else {
                            echo "<script>alert('Nema podataka za dati barkod.');</script>";
                            echo "<script>document.getElementById('all-sijalice').style.display = 'block';</script>";
                        }
                    }
                }
            ?>
            
            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["izmeni"])) {
                    $barkod = $_POST["barkod"];
                    $nproizvodjac = $_POST["nproizvodjac"];
                    $nsnaga = $_POST["nsnaga"];
                    $nboja = $_POST["nboja"];
                    $nvisina = $_POST["nvisina"];
                    $nsirina = $_POST["nsirina"];
                    $ncena = $_POST["ncena"];
                    $nslika = !empty($_FILES['nslika']['tmp_name']) ? addslashes(file_get_contents($_FILES['nslika']['tmp_name'])) : null;
                
                    // Provera da li su sva polja popunjena
                    if (empty($nproizvodjac) || empty($nsnaga) || empty($nboja) || empty($nvisina) || empty($nsirina) || empty($ncena) || empty($_FILES['nslika']['tmp_name'])) {
                        echo "<script>alert('Sva polja, uključujući sliku, moraju biti popunjena.')</script>";
                    } else {
                        // Ako su sva polja popunjena, izvrši upit za izmenu
                        if (!empty($_FILES['nslika']['tmp_name'])) {
                            $nslika = addslashes(file_get_contents($_FILES['nslika']['tmp_name']));
                            $updateSql = "UPDATE sijalica SET proizvodjac = '$nproizvodjac', snaga = '$nsnaga', boja = '$nboja', visina = '$nvisina', sirina = '$nsirina', cena = '$ncena', slika = '$nslika' WHERE barkod = $barkod";
                        } else {
                            $updateSql = "UPDATE sijalica SET proizvodjac = '$nproizvodjac', snaga = '$nsnaga', boja = '$nboja', visina = '$nvisina', sirina = '$nsirina', cena = '$ncena' WHERE barkod = $barkod";
                        }
                
                        // Izvršavanje SQL upita za izmenu
                        if ($conn->query($updateSql) === TRUE) {
                            echo "<script>alert('Podaci uspešno izmenjeni.');</script>";
                            echo "<script>refreshData();</script>";
                        } else {
                            echo "<script>alert('Greška prilikom izmene: " . $conn->error . "')</script>";
                        }
                    }
                }
            ?>
        </div>
    </div>
    <script>
    // Učitajte podatke kada se stranica učita
    window.onload = function() {
        refreshData();
    };
</script>
</body>
</html>