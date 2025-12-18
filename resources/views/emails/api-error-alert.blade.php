<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .alert-icon { font-size: 48px; margin-bottom: 20px; }
        .error-box { background: #fee2e2; border-left: 4px solid #ef4444; padding: 15px; margin: 20px 0; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        .info-table td:first-child { font-weight: bold; width: 40%; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="alert-icon">⚠️</div>
            <h1>Alerta: Error en API</h1>
        </div>
        <div class="content">
            <h2>Se ha detectado un error en tu API</h2>
            
            <table class="info-table">
                <tr>
                    <td>Cliente:</td>
                    <td>{{ $credential->client->company }}</td>
                </tr>
                <tr>
                    <td>Servicio API:</td>
                    <td>{{ $credential->apiService->name }}</td>
                </tr>
                <tr>
                    <td>Fecha del error:</td>
                    <td>{{ now()->format('d/m/Y H:i:s') }}</td>
                </tr>
            </table>

            <div class="error-box">
                <strong>Detalles del Error:</strong>
                <p style="margin: 10px 0;">{{ $errorData['message'] ?? 'Error desconocido' }}</p>
                @if(isset($errorData['code']))
                    <p style="margin: 5px 0; font-size: 14px;">Código: {{ $errorData['code'] }}</p>
                @endif
            </div>

            <h3>Acciones Recomendadas:</h3>
            <ul>
                <li>Verifica las credenciales de la API</li>
                <li>Revisa el estado del servicio externo</li>
                <li>Consulta el historial de logs en el dashboard</li>
                <li>Contacta a soporte si el problema persiste</li>
            </ul>

            <p style="margin-top: 30px; color: #666; font-size: 14px;">
                Este es un email automático. Para más detalles, ingresa al dashboard de Front-API.
            </p>
        </div>
    </div>
</body>
</html>
