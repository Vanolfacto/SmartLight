<?php
session_start();


$conn = mysqli_connect('localhost', 'root', '', 'prodavnicasijalica');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION["name"];


$userQuery = "SELECT id FROM kupac WHERE name = '$username'";
$userResult = $conn->query($userQuery);
if ($userResult->num_rows > 0) {
    $userData = $userResult->fetch_assoc();
    $userID = $userData['id'];

    
    $sql = "SELECT s.*, (n.kolicina) AS total_quantity FROM sijalica s 
            INNER JOIN narudzbina n ON s.barkod = n.barkod 
            WHERE n.korisnikID = $userID
            GROUP BY s.barkod";
    $result = $conn->query($sql);

    if ($result !== false && $result->num_rows > 0) {
        $orderTotal = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plaćanje</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .order-table th, .order-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .order-table th {
            background-color: #f2f2f2;
        }
        .order-table td {
            vertical-align: top;
        }
        .total {
            font-weight: bold;
            text-align: right;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input[type="text"], .form-group input[type="tel"], .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .payment-method {
            margin-top: 20px;
            text-align: center;
        }
        .payment-method label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .payment-method input[type="radio"] {
            margin-right: 10px;
        }
        .submit-button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #04AA6D;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .submit-button:hover {
            background-color: #028a4d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Plaćanje</h1>
        </div>
        <table class="order-table">
            <thead>
                <tr>
                    <th>Barkod</th>
                    <th>Proizvođač</th>
                    <th>Cena (RSD)</th>
                    <th>Količina</th>
                    <th>Ukupno</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    $totalItemPrice = $row['cena'] * $row['total_quantity'];
                    $orderTotal += $totalItemPrice;
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['barkod']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['proizvodjac']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['cena']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['total_quantity']) . "</td>";
                    echo "<td>" . htmlspecialchars($totalItemPrice) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="total">Ukupno za plaćanje:</td>
                    <td><?php echo htmlspecialchars($orderTotal); ?> RSD</td>
                </tr>
            </tfoot>
        </table>
        <form method="post" action="procesirajPlacanje.php">
            <div class="form-group">
                <label for="name">Ime i prezime:</label>
                <input type="text" id="name" name="imePrezime" required>
            </div>
            <div class="form-group">
                <label for="address">Adresa dostave:</label>
                <textarea id="address" name="adresa" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="grad">Grad:</label>
                <input type="text" id="grad" name="grad" required>
            </div>
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Kontakt telefon:</label>
                <input type="tel" id="phone" name="telefon" required>
            </div>
            <div class="payment-method">
                <label for="cash">Plaćanje pouzećem:</label>
                <input type="radio" id="cash" name="payment_method" value="cash" checked>
            </div>
            <button type="submit" class="submit-button">Plati</button>
        </form>
    </div>
</body>
</html>

<?php
    } else {
        echo "No orders found for this user.";
    }
} else {
    echo "Error: User not found.";
}

$conn->close();
?>