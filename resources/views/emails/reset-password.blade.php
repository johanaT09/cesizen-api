<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de votre mot de passe</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; background-color: #f8fafc; padding: 24px; margin: 0;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 540px; margin: 0 auto; background-color: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; overflow: hidden; border-collapse: collapse;">
        <tr>
            <td style="background-color: #111921; padding: 20px 32px; color: #ffffff;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td>
                            <div style="font-size: 11px; font-weight: bold; letter-spacing: 0.1em; text-transform: uppercase; opacity: 0.8;">République Française</div>
                            <div style="font-size: 16px; font-weight: bold; margin-top: 4px;">CESIZen</div>
                        </td>
                        <td align="right" style="font-size: 10px; opacity: 0.6; font-weight: 500;">
                            Ministère de la Santé
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 40px 32px;">
                <h1 style="color: #0f172a; font-size: 22px; font-weight: 700; margin: 0 0 16px 0;">Demande de nouveau mot de passe</h1>
                
                <p style="color: #334155; font-size: 15px; line-height: 1.6; margin: 0 0 24px 0;">
                    Une demande de réinitialisation de mot de passe a été formulée pour votre compte personnel sur la plateforme <strong>CESIZen</strong>.
                </p>
                
                <p style="color: #334155; font-size: 15px; line-height: 1.6; margin: 0 0 32px 0;">
                    Veuillez cliquer sur le bouton officiel ci-dessous pour configurer votre nouveau mot de passe :
                </p>
                
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 32px;">
                    <tr>
                        <td align="center">
                            <a href="http://localhost:3000/auth/reset-password?token={{ $token }}&email={{ urlencode($email) }}" 
                               style="background-color: #10b981; color: #ffffff; padding: 14px 32px; text-decoration: none; font-weight: bold; font-size: 14px; border-radius: 12px; display: inline-block;">
                                Confirmer la réinitialisation
                            </a>
                        </td>
                    </tr>
                </table>

                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px solid #f1f5f9; padding-top: 24px;">
                    <tr>
                        <td style="color: #64748b; font-size: 12px; line-height: 1.6;">
                            <p style="margin: 0 0 8px 0;">⏰ Ce lien expirera automatiquement dans <strong>60 minutes</strong>.</p>
                            <p style="margin: 0;">🛡️ Si vous n'êtes pas à l'origine de cette démarche, ignorez cet e-mail.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="background-color: #f8fafc; padding: 24px 32px; border-top: 1px solid #edf2f7; text-align: center; color: #94a3b8; font-size: 11px;">
                Ceci est un e-mail automatique. Merci de ne pas y répondre.
            </td>
        </tr>
    </table>
</body>
</html>