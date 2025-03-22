<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px; color: #333;">
    <div style="max-width: 500px; margin: 50px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 30px;">
        <h2 style="text-align: center; color: #84b6f4; margin-bottom: 20px;">Restablecer Contraseña</h2>

        <p>Hola {{ $user->name ?? 'usuario' }},</p>

        <p>Recibimos una solicitud para restablecer tu contraseña.</p>

        <p>Haz clic en el siguiente botón para continuar:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}"
               style="background-color: #84b6f4; color: #fff; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-size: 16px;">
                Restablecer Contraseña
            </a>
        </div>

        <p>Si no realizaste esta solicitud, puedes ignorar este mensaje.</p>

        <p style="margin-top: 30px;">Este mensaje expirará en 10 minutos </p>
    </div>
</body>
</html>
