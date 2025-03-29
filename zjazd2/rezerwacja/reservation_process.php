<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $num_guests = $_POST['num_guests'];
    $guest_details = [];

    // Zebranie danych gości
    for ($i = 0; $i < $num_guests; $i++) {
        $guest_details[] = [
            'first_name' => $_POST['guest_first_name'][$i],
            'last_name' => $_POST['guest_last_name'][$i],
            'age' => $_POST['guest_age'][$i],
            'gender' => $_POST['guest_gender'][$i]
        ];
    }

    $contact_email = $_POST['contact_email'];
    $arrival_date = $_POST['arrival_date'];
    $departure_date = $_POST['departure_date'];

    $arrival = new DateTime($arrival_date);
    $departure = new DateTime($departure_date);
    $stay_duration = $arrival->diff($departure)->days;
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Podsumowanie Rezerwacji</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .guest-details {
            background-color: #f4f4f4;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .guest-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <h1>Podsumowanie Rezerwacji</h1>
    
    <div class="guest-details">
        <h2>Dane Gości (<?php echo $num_guests; ?>)</h2>
        <?php foreach ($guest_details as $index => $guest): ?>
            <div class="guest-card">
                <h3>Osoba <?php echo $index + 1; ?></h3>
                <p><strong>Imię:</strong> <?php echo $guest['first_name']; ?></p>
                <p><strong>Nazwisko:</strong> <?php echo $guest['last_name']; ?></p>
                <p><strong>Wiek:</strong> <?php echo $guest['age']; ?></p>
                <p><strong>Płeć:</strong> <?php echo $guest['gender'] == 'male' ? 'Mężczyzna' : 'Kobieta'; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="guest-details">
        <h2>Szczegóły Rezerwacji</h2>
        <p><strong>Email kontaktowy:</strong> <?php echo $contact_email; ?></p>
        <p><strong>Data przyjazdu:</strong> <?php echo $arrival_date; ?></p>
        <p><strong>Data wyjazdu:</strong> <?php echo $departure_date; ?></p>
        <p><strong>Długość pobytu:</strong> <?php echo $stay_duration; ?> dni</p>
    </div>
</body>
</html>

<?php } ?>