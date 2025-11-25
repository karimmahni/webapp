<?php
require __DIR__ . '/db.php';

// Ajout trajet
if (isset($_POST['add_trajet'])) {
    $stmt = $pdo->prepare(
        "INSERT INTO trajets (conducteur, depart, arrivee, places, date_trajet)
         VALUES (:conducteur, :depart, :arrivee, :places, :date_trajet)"
    );
    $stmt->execute([
        ':conducteur'   => $_POST['conducteur'] ?? '',
        ':depart'       => $_POST['depart'] ?? '',
        ':arrivee'      => $_POST['arrivee'] ?? '',
        ':places'       => (int)($_POST['places'] ?? 0),
        ':date_trajet'  => $_POST['date_trajet'] ?? '',
    ]);
}

// Ajout réservation
if (isset($_POST['add_reservation'])) {
    $stmt = $pdo->prepare(
        "INSERT INTO reservations (trajet_id, nom_passager, places_reservees)
         VALUES (:trajet_id, :nom_passager, :places_reservees)"
    );
    $stmt->execute([
        ':trajet_id'        => (int)($_POST['trajet_id'] ?? 0),
        ':nom_passager'     => $_POST['nom_passager'] ?? '',
        ':places_reservees' => (int)($_POST['places_reservees'] ?? 0),
    ]);
}

// Récup trajets + nb places réservées
$sql = "
    SELECT t.id, t.conducteur, t.depart, t.arrivee, t.places, t.date_trajet,
           COALESCE(SUM(r.places_reservees), 0) AS places_reservees
    FROM trajets t
    LEFT JOIN reservations r ON r.trajet_id = t.id
    GROUP BY t.id, t.conducteur, t.depart, t.arrivee, t.places, t.date_trajet
    ORDER BY t.date_trajet ASC
";
$trajets = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Covoiturage – Karim</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Covoiturage – Karim</h1>

<section>
    <h2>Créer un trajet</h2>
    <form method="POST">
        <label>Conducteur :
            <input type="text" name="conducteur" required>
        </label><br>
        <label>Départ :
            <input type="text" name="depart" required>
        </label><br>
        <label>Arrivée :
            <input type="text" name="arrivee" required>
        </label><br>
        <label>Places :
            <input type="number" name="places" min="1" required>
        </label><br>
        <label>Date :
            <input type="date" name="date_trajet" required>
        </label><br>
        <button type="submit" name="add_trajet">Ajouter le trajet</button>
    </form>
</section>

<section>
    <h2>Trajets disponibles</h2>
    <?php if (empty($trajets)): ?>
        <p>Aucun trajet pour le moment.</p>
    <?php else: ?>
        <ul>
        <?php foreach ($trajets as $t): 
            $places_restantes = $t['places'] - $t['places_reservees'];
        ?>
            <li>
                <strong><?= htmlspecialchars($t['conducteur']) ?></strong> :
                <?= htmlspecialchars($t['depart']) ?> → <?= htmlspecialchars($t['arrivee']) ?>
                (<?= (int)$t['places'] ?> places, le <?= htmlspecialchars($t['date_trajet']) ?>)<br>
                Réservées : <?= (int)$t['places_reservees'] ?> /
                Restantes : <?= $places_restantes ?>

                <?php if ($places_restantes > 0): ?>
                    <form method="POST" class="res-form">
                        <input type="hidden" name="trajet_id" value="<?= (int)$t['id'] ?>">
                        <label>Nom passager :
                            <input type="text" name="nom_passager" required>
                        </label>
                        <label>Places :
                            <input type="number" name="places_reservees"
                                   min="1" max="<?= $places_restantes ?>" required>
                        </label>
                        <button type="submit" name="add_reservation">Réserver</button>
                    </form>
                <?php else: ?>
                    <p>Plus de places disponibles.</p>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

</body>
</html>
