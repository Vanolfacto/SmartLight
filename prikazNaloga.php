<?php
session_start();

// Provera da li je admin ulogovan
if (!isset($_SESSION['username'])) {
    header("Location: korisnikLogin.php");
    exit();
}

$conn = mysqli_connect('localhost', 'root', '', 'prodavnicasijalica');

if ($conn->connect_error) {
    die("Greška: " . $conn->connect_error);
}

// Brisanje korisnika i njihovih narudžbina
if (isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];

    // Brisanje iz tabele proizvod_narudzbina
    $deleteProizvodNarudzbinaSql = "DELETE FROM proizvod_narudzbina WHERE narudzbinaID IN (SELECT id FROM narudzbina_stavka WHERE korisnikID = $userId)";
    if ($conn->query($deleteProizvodNarudzbinaSql) === TRUE) {
        // Brisanje iz tabele narudzbina_stavka
        $deleteNarudzbinaStavkaSql = "DELETE FROM narudzbina_stavka WHERE korisnikID = $userId";
        if ($conn->query($deleteNarudzbinaStavkaSql) === TRUE) {
            // Brisanje iz tabele narudzbina
            $deleteNarudzbinaSql = "DELETE FROM narudzbina WHERE korisnikID = $userId";
            if ($conn->query($deleteNarudzbinaSql) === TRUE) {
                // Brisanje korisnika
                $deleteUserSql = "DELETE FROM kupac WHERE id = $userId";
                if ($conn->query($deleteUserSql) === TRUE) {
                    echo "<script>alert('Korisnik i njegove narudžbine su uspešno obrisani.');</script>";
                } else {
                    $error = mysqli_error($conn);
                    echo "<script>alert('Greška prilikom brisanja korisnika.');</script>";
                }
            } else {
                $error = mysqli_error($conn);
                echo "<script>alert('Greška prilikom brisanja narudžbina.');</script>";
            }
        } else {
            $error = mysqli_error($conn);
            echo "<script>alert('Greška prilikom brisanja stavki narudžbina.');</script>";
        }
    } else {
        $error = mysqli_error($conn);
        echo "<script>alert('Greška pri brisanju narudžbina iz tabele proizvod_narudzbina.');</script>";
    }
}

// Brisanje radnika
if (isset($_POST['delete_worker'])) {
    $workerId = $_POST['worker_id'];
    $deleteWorkerSql = "DELETE FROM radnik WHERE id = $workerId";
    if ($conn->query($deleteWorkerSql) === TRUE) {
        echo "<script>alert('Radnik je uspešno obrisan.');</script>";
    } else {
        echo "<script>alert('Greška prilikom brisanja radnika.');</script>";
    }
}

// Prikaz informacija o korisnicima
$usersSql = "SELECT * FROM kupac";
$usersResult = $conn->query($usersSql);

// Prikaz informacija o radnicima
$workersSql = "SELECT * FROM radnik";
$workersResult = $conn->query($workersSql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Stranica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
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
        .container {
            padding: 20px;
            margin-top: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        h1 {
            color: #04AA6D;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #04AA6D;
            color: white;
        }
        .button {
            background-color: #04AA6D;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .button:hover {
            background-color: #028a4d;
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
    <div class="container">
        <div class="card">
            <h1>Kupci</h1>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Korisničko ime</th>
                    <th>Akcije</th>
                </tr>
                <?php
                if ($usersResult->num_rows > 0) {
                    while ($row = $usersResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>
                                <form method='post' action=''>
                                    <input type='hidden' name='user_id' value='" . $row['id'] . "'/>
                                    <button type='submit' name='delete_user' class='button'>Obriši</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Nema korisnika.</td></tr>";
                }
                ?>
            </table>
        </div>
        <div class="card">
            <h1>Radnici</h1>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Korisničko ime</th>
                    <th>Akcije</th>
                </tr>
                <?php
                if ($workersResult->num_rows > 0) {
                    while ($row = $workersResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['username'] . "</td>";
                        echo "<td>
                                <form method='post' action=''>
                                    <input type='hidden' name='worker_id' value='" . $row['id'] . "'/>
                                    <button type='submit' name='delete_worker' class='button'>Obriši</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Nema radnika.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>