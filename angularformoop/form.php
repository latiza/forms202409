<?php
// Adatok kezelésére szolgáló osztály
class FormDataHandler {
/* A private $name; sor az osztályon belüli $name tulajdonság deklarációját jelöli. 
private: Ez a láthatósági módosító, amely azt jelzi, hogy a $name tulajdonság csak az adott osztályon belül érhető el. Más osztályok és külső kód nem férhet hozzá közvetlenül ehhez a tulajdonsághoz. Ez segít megvédeni az osztály állapotát, és biztosítja, hogy az adatokat csak az osztály metódusain keresztül lehessen módosítani, ami növeli a kód biztonságát és karbantarthatóságát. */
    private $name;      // Felhasználó neve
    private $email;     // Felhasználó email címe
    private $phone;     // Felhasználó telefonszáma
    private $course;    // Választott tanfolyam
    private $photo;     // Feltöltött fénykép


      // Névrendező függvény: a név első betűje nagybetűs, a többi kisbetűs
      private function formatName($name) {
        return ucwords(strtolower($name));
    }
    // Konstruktor, amely inicializálja az osztály tulajdonságait
    public function __construct($name, $email, $phone, $course, $photo) {
/* $this->name: Az osztály egy tulajdonsága, amely a felhasználó nevét tárolja.
$this->formatName($name): Egy privát metódus, amely a bemeneti $name értéket formázza a kívánt módon (pl. első betű nagybetűs, a többi kisbetűs).
$name: A konstruktor paramétere, amelyet a felhasználó ad meg, és amelyet formázni szeretnénk.
        Nevet formázunk: első betű nagybetűs és a többi kisbetűs*/
        $this->name = $this->formatName($name);
        // Email címet kisbetűssé alakítjuk
        $this->email = strtolower($email);
        // Telefonszámot HTML entitásokra kódoljuk, hogy biztonságos legyen
        $this->phone = htmlspecialchars($phone);
        // Tanfolyamot HTML entitásokra kódoljuk, hogy elkerüljük a XSS támadásokat
        $this->course = htmlspecialchars($course);
        // Fényképet eltároljuk
        $this->photo = $photo;
    }

    // Az űrlap feldolgozása és válasz készítése
    public function processForm() {
        $response = [];  // Üres válasz tömb inicializálása

        // Ellenőrizzük, hogy minden kötelező mező ki van-e töltve
        if ($this->name && $this->email && $this->phone && $this->course) {
            // Ha minden adat megvan, hozzáadjuk a választ tartalmához
            $response['name'] = $this->name;
            $response['email'] = $this->email;
            $response['phone'] = $this->phone;
            $response['course'] = $this->course;

            // Ha van fotó és nincs feltöltési hiba
            if ($this->photo && $this->photo['error'] === UPLOAD_ERR_OK) {
                // Feltöltjük a fotót és elmentjük az URL-t
                $photoUrl = $this->uploadPhoto();
                $response['photo'] = $photoUrl;  // Hozzáadjuk az URL-t a válaszhoz
            } else {
                // Ha nincs fotó, vagy hiba történt, null értéket adunk
                $response['photo'] = null;
            }
        } else {
            // Ha bármely kötelező mező hiányzik
            $response['error'] = "Kérjük, töltse ki az összes kötelező mezőt!";
        }

        // JSON válasz visszaküldése
        header('Content-Type: application/json');  // Beállítjuk a válasz típusát JSON-ra
        echo json_encode($response);  // JSON formátumban visszaadjuk a választ
    }

    // Fénykép feltöltésének kezelése
    private function uploadPhoto() {
        $uploadDir = 'uploads/';  // Feltöltési könyvtár

        // Ha a könyvtár nem létezik, létrehozzuk
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);  // Könyvtár létrehozása az összes szükséges szülőkönyvtárral
        }

        // Fénykép egyedi névvel ellátása
        $photoName = uniqid() . '_' . basename($this->photo['name']);
        $destination = $uploadDir . $photoName;  // Célmappa elérési útja

        // Fénykép áthelyezése a célkönyvtárba
        if (move_uploaded_file($this->photo['tmp_name'], $destination)) {
            return $destination;  // Ha sikerült a feltöltés, visszaadjuk az elérési utat
        } else {
            return null;  // Hiba esetén null értéket adunk vissza
        }
    }
}

// POST kérés feldolgozása
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    // Az űrlap adatait a POST változóból nyerjük ki
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $course = $_POST['course'];
    // Ha van fotó, akkor azt a FILES tömbből nyerjük ki
    $photo = isset($_FILES['photo']) ? $_FILES['photo'] : null;

    // Létrehozzuk az adatkezelő példányát és feldolgozzuk az űrlapot
    $formHandler = new FormDataHandler($name, $email, $phone, $course, $photo);
    $formHandler->processForm();
} else {
    // Ha nem POST kérés érkezik
    echo "Hibás kérés!";  // Jelzi, hogy nem POST kérést kaptunk
}
