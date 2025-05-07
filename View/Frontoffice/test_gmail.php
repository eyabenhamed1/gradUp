<?php
require_once __DIR__.'/../../lib/PHPMailer/src/Exception.php';
require_once __DIR__.'/../../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__.'/../../lib/PHPMailer/src/SMTP.php';
$mail = new PHPMailer\PHPMailer\PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'esprit.test12@gmail.com';
    $mail->Password = 'yvyj ylyx zfym vhhs';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('esprit.test12@gmail.com', 'Test ESPRIT');
    $mail->addAddress('sindasakouhi13@gmail.com'); // Votre vrai email pour recevoir le test
    
    $mail->Subject = 'TEST SMTP depuis XAMPP';
    $mail->Body = 'Si vous recevez ceci, PHPMailer fonctionne !';

    if ($mail->send()) {
        echo "✅ Email envoyé ! Vérifiez vos spams si vous ne le voyez pas.";
    }
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}