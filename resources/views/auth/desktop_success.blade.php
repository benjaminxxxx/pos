<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sincronizando con Mi POS</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #121212; color: white; }
        .card { text-align: center; padding: 2rem; border-radius: 12px; background: #1e1e1e; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        .loader { border: 4px solid #333; border-top: 4px solid #3b82f6; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 1rem; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        a { color: #3b82f6; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <div class="loader"></div>
        <h2>¡Autenticación completada!</h2>
        <p>Estamos abriendo la aplicación de escritorio...</p>
        <p style="font-size: 0.8rem; color: #888;">Si no sucede nada, <a id="manual-link" href="mi-pos://auth?uuid={{ $uuid }}">haz clic aquí para volver</a></p>
    </div>

    <script>
        // Definimos la URL del protocolo
        const protocolUrl = "mi-pos://auth?uuid={{ $uuid }}";
        console.log(protocolUrl);
        // Intentamos disparar el protocolo inmediatamente
        window.location.assign(protocolUrl);

        // Fallback: Si después de 3 segundos sigue aquí, es que no se cerró o no saltó
        // (Aunque normalmente el SO abrirá Electron y esta pestaña quedará en segundo plano)
        setTimeout(() => {
            // Opcional: intentar un segundo método si el primero falló
           // window.location.href = protocolUrl;
        }, 500);
    </script>
</body>
</html>