<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .success-icon { font-size: 48px; margin-bottom: 20px; }
        .button { display: inline-block; padding: 12px 24px; background: #10b981; color: white; text-decoration: none; border-radius: 6px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="success-icon">✅</div>
            <h1>Email de Prueba</h1>
        </div>
        <div class="content">
            <h2>¡Configuración Exitosa!</h2>
            <p>Este es un email de prueba enviado desde <strong>Front-API</strong>.</p>
            <p>Si estás recibiendo este mensaje, significa que tu configuración SMTP está funcionando correctamente.</p>
            
            <div style="background: #e0f2fe; border-left: 4px solid #0284c7; padding: 15px; margin: 20px 0;">
                <strong>Información del Sistema:</strong>
                <ul style="margin: 10px 0;">
                    <li>Aplicación: Front-API</li>
                    <li>Fecha de envío: {{ now()->format('d/m/Y H:i:s') }}</li>
                    <li>Mailer: {{ config('mail.default') }}</li>
                </ul>
            </div>

            <p>Ahora puedes configurar alertas automáticas para tus APIs y recibirás notificaciones cuando ocurran errores.</p>
            
            <p style="margin-top: 30px; color: #666; font-size: 14px;">
                Este es un email automático. Por favor no respondas a este mensaje.
            </p>
        </div>
    </div>
</body>
</html>
