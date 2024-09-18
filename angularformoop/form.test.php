<?php
// A tesztek eredményeinek tárolása
$testResults = [];

// Egyszerű tesztellenőrző függvény
/*assertEqual függvény: Ez a függvény ellenőrzi, hogy az expected és actual értékek megegyeznek-e. Ha igen, sikeres tesztet jelöl, különben sikertelen tesztet, és megjeleníti az elvárt és tényleges értékeket.*/
function assertEqual($expected, $actual, $testName) {
    global $testResults;
    if ($expected === $actual) {
        $testResults[] = " $testName: Teszt sikeres!";
    } else {
        $testResults[] = "$testName: Sikertelen teszt. Várt érték: '$expected', tényleges: '$actual'";
    }
}

// Include-oljuk az osztályt tartalmazó fájlt
include_once 'form.php';

// 1. teszt: Név formázása
/*Itt létrehozol egy új FormDataHandler példányt a new kulcsszóval, és inicializálod azt néhány bemeneti értékkel. Az itt megadott adatok a FormDataHandler osztály konstruktorába kerülnek.
Ez a sor a ReflectionClass osztályt használja a FormDataHandler osztály metódusainak elérésére:

new ReflectionClass('FormDataHandler') létrehoz egy ReflectionClass példányt a FormDataHandler osztályról.
getMethod('formatName') lekéri a formatName nevű metódust az osztályból.
invokeArgs($formHandler, ['john doe']) meghívja a formatName metódust a korábban létrehozott $formHandler objektumon, és átadja neki a ['john doe'] argumentumot. A invokeArgs lehetővé teszi a metódus dinamikus meghívását és argumentumok átadását.
Miért használjuk a ReflectionClass-t?
Privát és védett metódusok tesztelése: A ReflectionClass lehetővé teszi, hogy hozzáférjünk és teszteljük az osztály privát és védett metódusait, amelyeket egyébként nem érhetnénk el közvetlenül.
Dinamikus metódushívás: Ha dinamikusan akarjuk meghívni a metódusokat, például a metódus nevét csak futási időben tudjuk, a ReflectionClass segítségünkre lehet.

 */
$formHandler = new FormDataHandler("john doe", "test@example.com", "123456789", "frontend", null);
$formattedName = (new ReflectionClass('FormDataHandler'))->getMethod('formatName')->invokeArgs($formHandler, ['john doe']);
assertEqual("John Doe", $formattedName, "Név formázása");

// 2. teszt: Helyes adatok feldolgozása
$photo = ['error' => UPLOAD_ERR_OK, 'name' => 'test.jpg', 'tmp_name' => '/tmp/test.jpg'];
$formHandler = new FormDataHandler("John Doe", "test@example.com", "123456789", "frontend", $photo);

ob_start(); // Kimenet elkapása
$formHandler->processForm();
$output = ob_get_clean();
$response = json_decode($output, true);

assertEqual("John Doe", $response['name'], "Név feldolgozása");
assertEqual("test@example.com", $response['email'], "Email feldolgozása");
assertEqual("123456789", $response['phone'], "Telefonszám feldolgozása");
assertEqual("frontend", $response['course'], "Tanfolyam feldolgozása");
assertEqual(true, isset($response['photo']), "Fénykép feldolgozása");

// 3. teszt: Hiányzó mezők tesztelése
$formHandler = new FormDataHandler("", "test@example.com", "123456789", "frontend", null);
ob_start(); // Kimenet elkapása
$formHandler->processForm();
$output = ob_get_clean();
$response = json_decode($output, true);
assertEqual("Kérjük, töltse ki az összes kötelező mezőt!", $response['error'], "Hiányzó mezők kezelése");

// 4. teszt: Feltöltési hiba kezelése
$photo = ['error' => UPLOAD_ERR_NO_FILE];
$formHandler = new FormDataHandler("John Doe", "test@example.com", "123456789", "frontend", $photo);
ob_start(); // Kimenet elkapása
$formHandler->processForm();
$output = ob_get_clean();
$response = json_decode($output, true);
assertEqual(null, $response['photo'], "Feltöltési hiba kezelése");

// Az eredmények megjelenítése
echo "<h2>Teszt eredmények:</h2>";
foreach ($testResults as $result) {
    echo $result;
}
?>
