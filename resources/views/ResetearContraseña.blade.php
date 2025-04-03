<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr/build/toastr.min.css">
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px; color: #333;">
    <div style="max-width: 500px; margin: 50px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 30px;">
        <h2 style="text-align: center; color: #84b6f4; margin-bottom: 20px;">Restablecer Contraseña</h2>
        <form id="miFormulario">
            @csrf
            
            <input type="hidden" name="token" value="{{ $token ?? '' }}">

            <label for="email" style="display: block; font-size: 16px; margin-bottom: 10px;">Email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                required 
                style="width: 100%; padding: 10px; font-size: 16px; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 20px;"
            >

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
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr/build/toastr.min.js"></script>

    <script>
    $(document).ready(function() {
        toastr.options = {
            "timeOut": 10000,
            "maxOpened": 3,
            "preventDuplicates": true,
            "positionClass": "toast-middle-right"
        }

        $('#miFormulario').on('submit', function(event) {
            event.preventDefault(); 

            let formData = new FormData(this);
            let url = "{{ route('password.update') }}"; 

            fetch(url, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.message === "¡Contraseña cambiada exitosamente!") {
                    
                    toastr.success(data.message);
                    toastr.info("Redirigiéndote a la página de inicio de sesión...");
                    
                    setTimeout(() => {
                        window.location.href = "http://192.168.125.146:4200/login"; 
                    }, 2000); 
                } else if (data.message === "No se encontró un usuario con este correo electrónico.") {
                    toastr.error(data.message);
                } else if (data.message) {
                    toastr.warning(data.message);
                }
            })
            .catch(error => {
                toastr.error("Ocurrió un error inesperado");
            });
        });
    });
</script>
</body>
</html>
