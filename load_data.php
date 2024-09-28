<?php
$conn = mysqli_connect('localhost', 'root', '', 'prodavnicasijalica');

if ($conn->connect_error) {
    die("Greška: " . $conn->connect_error);
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