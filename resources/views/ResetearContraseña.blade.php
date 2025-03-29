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
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            
            <input type="hidden" name="token" value="{{ $token ?? '' }}">

            <label for="password" style="display: block; font-size: 16px; margin-bottom: 10px;">Nueva contraseña:</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required 
                style="width: 100%; padding: 10px; font-size: 16px; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 20px;"
            >

            <label for="password_confirmation" style="display: block; font-size: 16px; margin-bottom: 10px;">Confirmar nueva contraseña:</label>
            <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation" 
                required 
                style="width: 100%; padding: 10px; font-size: 16px; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 20px;"
            >

            <button 
                type="submit" 
                style="width: 100%; background-color: #84b6f4; color: #fff; padding: 12px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;"
            >
                Restablecer Contraseña
            </button>
        </form>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('redirect_to'))
        <script>
                window.location.href = "{{ session('redirect_to') }}";
         </script>
        @endif
    </div>
</body>
</html>
