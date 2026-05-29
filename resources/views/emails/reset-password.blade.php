<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation de votre mot de passe</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f5f7; padding: 20px;">
    <div style="max-width: 500px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; border: 1px solid #e3e8ee;">
        
        <h2>Projet CESIZen</h2>
        <p>Bonjour,</p>
        <p>Vous avez demandé la réinitialisation de votre mot de passe pour votre compte personnel.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="http://localhost:3000/auth/reset-password?token={{ $token }}&email={{ urlencode($email) }}" 
               style="background-color: #10b981; color: white; padding: 12px 24px; text-decoration: none; font-weight: bold; border-radius: 6px; display: inline-block;">
               Réinitialiser mon mot de passe
            </a>
        </div>
        
        <p style="font-size: 12px; color: #6b7280;">Si vous n'avez pas fait cette demande, vous pouvez ignorer cet e-mail.</p>
    </div>
</body>
</html>