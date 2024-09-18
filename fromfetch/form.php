<?php
// A nevet kisbetűsítjük és minden szó első betűjét nagybetűre állítjuk
/*function formatName($name) {
    $name = strtolower($name); // Minden karaktert kisbetűsítünk
    $name = ucwords($name); // Minden szó első betűjét nagybetűsítjük
    return $name;
}*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Név és email formázása kiszervezhető függvényekbe
   // $name = formatName($_POST['name']);
   $name = htmlspecialchars(ucwords(strtolower($_POST['name'])));
    $email = strtolower($_POST['email']); // Emailt kisbetűsítjük
    $phone = htmlspecialchars($_POST['phone']);
    $course = htmlspecialchars($_POST['course']);

    if ($name && $email && $phone && $course) {
        echo "<h2>Beküldött adatok:</h2>";
        echo "Név: " . $name . "<br>";
        echo "Email: " . $email . "<br>";
        echo "Telefonszám: " . $phone . "<br>";
        echo "Választott tanfolyam: " . $course . "<br>";

        // Fotó ellenőrzése és feltöltése
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoTmpPath = $_FILES['photo']['tmp_name'];
            $photoName = uniqid() . '_' . $_FILES['photo']['name'];
            $uploadDir = 'uploads/';
/**Ez a feltétel azt ellenőrzi, hogy létezik-e egy adott mappa a megadott útvonalon. Az is_dir() függvény visszaadja true-t, ha a megadott útvonal egy létező mappa, és false-t, ha nem létezik. */
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
/**A mkdir() függvény egy új mappát hoz létre a megadott útvonalon.
Paraméterek:
$uploadDir: Ez az a változó, amely tartalmazza a létrehozandó mappa nevét vagy útvonalát.
0777: Ez a mappa jogosultsága, amely azt jelenti, hogy mindenki (felhasználó, csoport, mások) számára teljes hozzáférést biztosít (olvashat, írhat, futtathat). (A 0777 az oktális számrendszerben van megadva, ahol minden számjegy egy csoportnak a jogosultságait jelenti.)
true: Ez a harmadik paraméter azt mondja meg a mkdir() függvénynek, hogy rekurzívan hozza létre a mappát, vagyis, ha szükséges, létrehozza az összes szülő mappát is, amelyek még nem léteznek az útvonalban. */

            $destination = $uploadDir . basename($photoName);

            if (move_uploaded_file($photoTmpPath, $destination)) {
                echo "Feltöltött fotó:<br>";
                echo "<img src='" . $destination . "' alt='Feltöltött fotó' style='max-width:200px;'><br>";
            } else {
                echo "Hiba történt a fotó feltöltésekor.";
            }
        } else {
            echo "Nem lett fotó feltöltve.";
        }
    } else {
        echo "Kérjük, töltse ki az összes kötelező mezőt!";
    }
} else {
    echo "Hibás kérés!";
}
?>
