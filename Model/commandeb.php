public function modifierEtat($id, $nouvel_etat) {
    $conn = new mysqli("localhost", "root", "", "projetweb2a");
    
    if ($conn->connect_error) {
        return false;
    }
    
    $stmt = $conn->prepare("UPDATE commande SET etat = ? WHERE id_commande = ?");
    $stmt->bind_param("si", $nouvel_etat, $id);
    
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}