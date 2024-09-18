// AngularJS modul létrehozása
const app = angular.module('formApp', []);

app.controller('FormController', ['$scope', '$http', function($scope, $http) {
    // Az adatok inicializálása
    $scope.formData = {};
    $scope.error = '';
    $scope.submissionResult = {};
    $scope.formSubmitted = false; // Az űrlap beküldésének állapota

    // Az űrlap adatainak küldése a PHP fájlnak
    $scope.submitForm = function() {
        // Hibák törlése
        $scope.error = '';
        $scope.submissionResult = {};  // Töröljük az előző eredményeket

        // Kötelező mezők ellenőrzése
        if (!$scope.formData.name || !$scope.formData.email || !$scope.formData.phone || !$scope.formData.course) {
            alert('Kérjük, töltse ki az összes kötelező mezőt!');
            return;  // Megakadályozza az űrlap elküldését, ha bármelyik kötelező mező üres
        }

        // Adatküldés a PHP backend felé
        const formData = new FormData();
        formData.append('name', $scope.formData.name);
        formData.append('email', $scope.formData.email);
        formData.append('phone', $scope.formData.phone);
        formData.append('course', $scope.formData.course);

       // Fotó feltöltésének ellenőrzése és hozzáadása, ha van
       if (document.getElementById('photo').files.length > 0) {
        formData.append('photo', document.getElementById('photo').files[0]);
    }

        console.log(formData); //itt nem látjuk az adatokat
        /**Az entries() egy metódus a JavaScript-ben, amely bizonyos beépített objektumok, például a FormData, Map, vagy Array esetén elérhető. Ez a metódus egy iterátort ad vissza, amely tartalmazza az objektum összes kulcs-érték párját, így lehetővé teszi az elemek végigjárását. 
         * Ezzel tudjuk tesztelni, hogy milyen adatokat küldünk tovább a php-nek
        */
        for (let i of formData.entries()) {
            console.log(i[0] + ': ' + i[1]);
        }
       /* for (let [key, value] of formData.entries()) {
            console.log(key, value); // Ellenőrizd az adatokat a konzolon
        }*/

        $http.post('form.php', formData, {
            transformRequest: angular.identity,
            headers: { 'Content-Type': undefined }
        })
        .then(function(response) {
            //ell.hogy mit küldünk el
            console.log('Válasz:', response);
            $scope.submissionResult = response.data;
            $scope.formSubmitted = true; // Az űrlap sikeres beküldésének jelzése
        }, function(error) {
            $scope.error = 'Hiba történt az adatok küldésekor: ' + error.status + ' - ' + error.statusText;

        });
    };
}])