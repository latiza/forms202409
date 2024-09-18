<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kötelező mezők ellenőrzése
    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $phone = htmlspecialchars($_POST['phone']);
    $course = htmlspecialchars($_POST['course']);

    if ($name && $email && $phone && $course) {
        echo "<h2>Beküldött adatok:</h2>";
        echo "Név: " . $name . "<br>";
        echo "Email: " . $email . "<br>";
        echo "Telefonszám: " . $phone . "<br>";
        echo "Választott tanfolyam: " . $course . "<br>";

        // Fotó ellenőrzése
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoTmpPath = $_FILES['photo']['tmp_name'];
            $photoName = uniqid() . '_' . $_FILES['photo']['name']; // Egyedi fájlnév
            $uploadDir = 'uploads/';

            // Ellenőrizzük, hogy az 'uploads' mappa létezik-e, ha nem, létrehozzuk
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Létrehozás írási jogokkal
            }

            $destination = $uploadDir . basename($photoName);

            // Fájl mozgatása a cél könyvtárba
            if (move_uploaded_file($photoTmpPath, $destination)) {
                echo "Feltöltött fotó:<br>";
                echo "<img src='" . $destination . "' alt='Feltöltött fotó' style='max-width:200px;'><br>";
                echo "Fotó neve: " . $photoName . "<br>";
            } else {
                echo "Hiba történt a fotó feltöltésekor.<br>";
                echo "Temp fájl helye: " . $photoTmpPath . "<br>";
                echo "Cél: " . $destination . "<br>";
                print_r(error_get_last()); // Hibakeresés
            }
        } else {
            echo "Nem lett fotó feltöltve.<br>";
        }
    } else {
        echo "Kérjük, töltse ki az összes kötelező mezőt!";
    }
} else {
    echo "Hibás kérés!";
}
?>
