-- Tables pour l'appli de covoiturage

CREATE TABLE IF NOT EXISTS trajets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conducteur VARCHAR(100) NOT NULL,
    depart VARCHAR(100) NOT NULL,
    arrivee VARCHAR(100) NOT NULL,
    places INT NOT NULL,
    date_trajet DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trajet_id INT NOT NULL,
    nom_passager VARCHAR(100) NOT NULL,
    places_reservees INT NOT NULL,
    FOREIGN KEY (trajet_id) REFERENCES trajets(id) ON DELETE CASCADE
);
