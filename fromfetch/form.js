document.addEventListener('DOMContentLoaded', function() {
    // Az űrlap elemének elérése az ID segítségével
    const form = document.getElementById('userForm');   
    // Az elem elérése, ahol majd a hibák megjelennek
    const errorDiv = document.getElementById('error');
    // Eseményfigyelő hozzáadása az űrlaphoz, hogy a beküldést elkapjuk
    form.addEventListener('submit', function(event) {
        // Megakadályozza az űrlap alapértelmezett beküldési viselkedését
        event.preventDefault();
        // A hibadiv tartalmának törlése, hogy a régi hibák eltűnjenek
        errorDiv.innerHTML = '';
        // Az űrlap mezők értékeinek lekérése
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const course = document.querySelector('input[name="course"]:checked');
        const photo = document.getElementById('photo').files[0];
        // Adatok logolása a teszteléshez
        console.log('Név:', name);
        console.log('Email:', email);
        console.log('Telefonszám:', phone);
        console.log('Tanfolyam:', course ? course.value : 'Nincs kiválasztva');
        console.log('Fénykép:', photo ? photo.name : 'Nincs fájl kiválasztva');
//gyűjtsük össze a hibákat
        let errors = [];
        // Ellenőrzés: a név mező nem lehet üres
        if (name === '') {
            errors.push('A név megadása kötelező.');
        }
        /* Email formátum ellenőrzése; egy reguláris kifejezést (regexet) használ az email-címek érvényesítésére*/
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/;
        if (!emailPattern.test(email)) {
            errors.push('Az email formátuma helytelen.');
        }

        // Telefonszám ellenőrzése: legalább 8 karakter hosszú legyen
        if (phone.length < 8) {
            errors.push('A telefonszámnak legalább 8 karakter hosszúnak kell lennie.');
        }

        // Tanfolyam kiválasztás ellenőrzése
        if (!course) {
            errors.push('Válasszon egy tanfolyamot.');
        }

        // Hibák logolása
        console.log('Hibák:', errors);

        // Ha van hiba, megjelenítjük
        if (errors.length > 0) {
            errorDiv.innerHTML = errors.join('<br>');
        } else {
            // FormData objektum létrehozása
            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('phone', phone);
            formData.append('course', course.value);

            if (photo) {
                formData.append('photo', photo);
            }

            // FormData tartalmának logolása
            console.log('FormData:', formData);
//ezen a ponton teszteljünk!!! Addig ne adjuk át az adatokat
            // Fetch API használata
            fetch('form.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text()) // Válasz logolása
            .then(data => {
                console.log('Szerver válasza:', data);
                errorDiv.innerHTML = data;
            })
            .catch(error => {
                console.error('Fetch hiba:', error);
                errorDiv.innerHTML = 'Hiba történt az adatok küldésekor: ' + error.message;
            });
        }
    });
});
