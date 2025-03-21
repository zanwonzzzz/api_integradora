<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activación de Cuenta</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px;">
        <h1 style="font-size: 24px; text-align: center; color: #84b6f4;">¡Bienvenido!</h1>
        <p style="font-size: 16px; line-height: 1.5; text-align: center;">
            Hola <strong>{{$user}}</strong>,<br>
            Gracias por registrarte. Para activar tu cuenta, ingresa este codigo {{$codigo}} en el siguiente enlace
        </p>
        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ $url }}" style="display: inline-block; background-color: #84b6f4; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 16px;">
                Activar Cuenta
            </a>
        </div>
        <p style="font-size: 14px; text-align: center; color: #777;">
            Este correo expirará en 5 minutos.
        </p>
    </div>
</body>
</html>
