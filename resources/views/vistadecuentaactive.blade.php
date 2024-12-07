<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activación de Cuenta</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px 30px;
            max-width: 400px;
            text-align: center;
        }

        .container h1 {
            font-size: 24px;
            color: #84b6f4;
            margin-bottom: 10px;
        }

        .container p {
            font-size: 16px;
            line-height: 1.5;
        }

        .container a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #84b6f4;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .container a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>¡Cuenta Activada!</h1>
        <p><strong>{{$user}}</strong>, tu cuenta ha sido activada con éxito.</p>
    </div>
</body>
</html>
