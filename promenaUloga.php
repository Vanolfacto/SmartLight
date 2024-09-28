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
// Funkcija za generisanje novog jedinstvenog ID-a
function generateNewId($conn, $table) {
    $sql = "SELECT MAX(id) AS max_id FROM $table";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['max_id'] + 1;
}
// Izmena uloge korisnika
if (isset($_POST['change_role'])) {
    $userId = $_POST['user_id'];
    $newRoleId = $_POST['new_role_id'];
    $userType = $_POST['user_type'];

    // Provera trenutne uloge
    if ($userType == 'kupac') {
        $checkRoleSql = "SELECT * FROM kupac WHERE id = $userId";
    } else if ($userType == 'radnik'){
        $checkRoleSql = "SELECT * FROM radnik WHERE id = $userId";
    }
    else if ($userType == 'admin'){
        $checkRoleSql = "SELECT * FROM admin WHERE id = $userId";
    }
    $result = $conn->query($checkRoleSql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentRoleId = $row['ulogaID'];

        // Provera da li je nova uloga ista kao trenutna uloga
        if ($newRoleId == $currentRoleId) {
            echo "<script>alert('Nemoguće je promeniti ulogu u istu ulogu.');</script>";
        } else {
             // Priprema podataka za umetanje u novu tabelu
             $name = isset($row['name']) ? $row['name'] : (isset($row['username']) ? $row['username'] : '');
             $password = $row['password'];
             // Ubacivanje u novu tabelu prema novoj ulozi
             if ($newRoleId == 1) {
                $newId = generateNewId($conn, 'kupac');
                $insertSql = "INSERT INTO kupac (id, name, password, ulogaID) VALUES ($newId, '$name', '$password', $newRoleId)";
            } else if ($newRoleId == 2) {
                $newId = generateNewId($conn, 'radnik');
                $insertSql = "INSERT INTO radnik (id, username, password, ulogaID) VALUES ($newId, '$name', '$password', $newRoleId)";
            }else if ($newRoleId == 3){
                $newId = generateNewId($conn, 'admin');
                $insertSql = "INSERT INTO admin (id, username, password, ulogaID) VALUES ($newId, '$name', '$password', $newRoleId)";
            }
            if (!empty($insertSql) && $conn->query($insertSql) === TRUE) {
                // Brisanje iz trenutne tabele
                
                if ($userType == 'kupac') {
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
                    $deleteSql = "DELETE FROM kupac WHERE id = $userId";
                } else if($userType =='radnik'){
                    $deleteSql = "DELETE FROM radnik WHERE id = $userId";
                }
                else if($userType =='admin'){
                    $deleteSql = "DELETE FROM admin WHERE id = $userId";
                }
                if ($conn->query($deleteSql) === TRUE) {
                    echo "<script>alert('Uloga je uspešno izmenjena i korisnik je transferovan.');</script>";
                } else {
                    echo "<script>alert('Greška prilikom brisanja korisnika iz trenutne tabele.');</script>";
                }
            }  else {
                echo "<script>alert('Greška prilikom umetanja korisnika u novu tabelu');</script>";
            }
        }
    }
}

// Prikaz informacija o korisnicima
$usersSql = "SELECT k.id, k.name, r.tip FROM kupac k JOIN uloga r ON k.ulogaID = r.id";
$usersResult = $conn->query($usersSql);

// Prikaz informacija o radnicima
$workersSql = "SELECT w.id, w.username, r.tip FROM radnik w JOIN uloga r ON w.ulogaID = r.id";
$workersResult = $conn->query($workersSql);

// Prikaz mogućih uloga
$rolesSql = "SELECT * FROM uloga";
$rolesResult = $conn->query($rolesSql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Izmena Uloga</title>
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
            <h1>Izmena Uloga Kupca</h1>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Ime kupca</th>
                    <th>Trenutna uloga</th>
                    <th>Nova uloga</th>
                    <th>Akcije</th>
                </tr>
                <?php
                if ($usersResult->num_rows > 0) {
                    while ($row = $usersResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['tip'] . "</td>";
                        echo "<td>
                                <form method='post' action=''>
                                    <input type='hidden' name='user_id' value='" . $row['id'] . "'/>
                                    <input type='hidden' name='user_type' value='kupac'/>
                                    <select name='new_role_id'>";
                        // Prikaz mogućih uloga
                        $rolesResult = $conn->query($rolesSql);
                        if ($rolesResult->num_rows > 0) {
                            while ($role = $rolesResult->fetch_assoc()) {
                                echo "<option value='" . $role['id'] . "'>" . $role['tip'] . "</option>";
                            }
                        }
                        echo "</select>
                              </td>";
                        echo "<td>
                                <button type='submit' name='change_role' class='button'>Izmeni</button>
                              </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Nema korisnika.</td></tr>";
                }
                ?>
            </table>
        </div>
        <div class="card">
            <h1>Izmena Uloga Radnika</h1>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Ime radnika</th>
                    <th>Trenutna uloga</th>
                    <th>Nova uloga</th>
                    <th>Akcije</th>
                </tr>
                <?php
                if ($workersResult->num_rows > 0) {
                    while ($row = $workersResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['username'] . "</td>";
                        echo "<td>" . $row['tip'] . "</td>";
                        echo "<td>
                                <form method='post' action=''>
                                    <input type='hidden' name='user_id' value='" . $row['id'] . "'/>
                                    <input type='hidden' name='user_type' value='radnik'/>
                                    <select name='new_role_id'>";
                        // Prikaz mogućih uloga
                        $rolesResult = $conn->query($rolesSql);
                        if ($rolesResult->num_rows > 0) {
                            while ($role = $rolesResult->fetch_assoc()) {
                                echo "<option value='" . $role['id'] . "'>" . $role['tip'] . "</option>";
                            }
                        }
                        echo "</select>
                              </td>";
                        echo "<td>
                                <button type='submit' name='change_role' class='button'>Izmeni</button>
                              </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Nema radnika.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>