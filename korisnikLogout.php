<?php
session_start();
session_destroy();
header("Location: korisnikPocetna.php");
exit();
?>