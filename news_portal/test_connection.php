<?php
// test_connection.php - USUŃ TEN PLIK PO TESTACH!
require_once 'config/database.php';

echo "<h1>🧪 Test połączenia z bazą danych</h1>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<div style='color: green; background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0;'>";
    echo "✅ <strong>Połączenie z bazą danych działa!</strong><br>";
    echo "📊 Baza: news_portal<br>";
    echo "🖥️ Host: localhost<br>";
    echo "</div>";
    
    // Test tabeli users
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<div style='background: #e7f3ff; padding: 10px; border: 1px solid #b3d7ff; border-radius: 5px; margin: 10px 0;'>";
    echo "👥 Liczba użytkowników w bazie: " . $result['count'] . "<br>";
    echo "</div>";
    
    // Test tabeli categories  
    $stmt = $db->query("SELECT COUNT(*) as count FROM categories");
    $result = $stmt->fetch();
    echo "<div style='background: #e7f3ff; padding: 10px; border: 1px solid #b3d7ff; border-radius: 5px; margin: 10px 0;'>";
    echo "📁 Liczba kategorii w bazie: " . $result['count'] . "<br>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 10px 0;'>";
    echo "🎉 <strong>Wszystko działa poprawnie!</strong><br>";
    echo "🚀 Możesz teraz przejść do: <a href='index.php'>index.php</a><br>";
    echo "🔐 Lub zalogować się: <a href='login.php'>login.php</a> (admin/password)<br>";
    echo "⚠️ <strong>Pamiętaj usunąć ten plik po testach!</strong>";
    echo "</div>";
    
} catch(Exception $e) {
    echo "<div style='color: red; background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ <strong>Błąd połączenia:</strong><br>";
    echo $e->getMessage() . "<br><br>";
    echo "<strong>🔧 Co sprawdzić:</strong><br>";
    echo "1. Czy XAMPP jest uruchomiony?<br>";
    echo "2. Czy MySQL działa w panelu XAMPP?<br>";
    echo "3. Czy baza 'news_portal' istnieje?<br>";
    echo "4. Czy ustawienia w config/database.php są poprawne?<br>";
    echo "</div>";
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
</style>