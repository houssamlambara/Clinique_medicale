<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #e74c3c;
            padding-bottom: 25px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #e74c3c;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            color: #666;
            margin: 10px 0 0 0;
            font-size: 16px;
        }
        .notification-type {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
        }
        .notification-type h2 {
            margin: 0 0 10px 0;
            font-size: 20px;
            font-weight: 600;
        }
        .notification-type p {
            margin: 0;
            opacity: 0.9;
        }
        .message {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            border-left: 4px solid #28a745;
            margin-bottom: 25px;
        }
        .message h3 {
            margin: 0 0 15px 0;
            color: #28a745;
            font-size: 18px;
        }
        .message p {
            margin: 0;
            font-size: 16px;
            line-height: 1.6;
        }
        .patient-info {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        .patient-info p {
            margin: 8px 0;
            font-size: 15px;
        }
        .patient-info strong {
            font-weight: 600;
        }
        .footer {
            text-align: center;
            margin-top: 35px;
            padding-top: 25px;
            border-top: 2px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🏥</div>
            <p>Notification importante</p>
        </div>

        <div class="patient-info">
            <p><strong>👤 Destinataire :</strong> {{ $patient->nom }} {{ $patient->prenom }}</p>
            <p><strong>📧 Email :</strong> {{ $patient->email }}</p>
        </div>

        <div class="notification-type">
            <h2>{{ $typeLabel }}</h2>
            <p><strong>📅 Date :</strong> {{ $notification->created_at->format('d/m/Y à H:i') }}</p>
        </div>

        <div class="message">
            <h3>💬 Message :</h3>
            <p>{{ $notification->message }}</p>
        </div>

        <div class="footer">
            <p><strong>Ce message a été envoyé automatiquement par {{ config('app.name', 'Clinique Médicale') }}</strong></p>
            <p>Pour toute question, veuillez contacter notre secrétariat</p>
            <p><small>📧 Envoyé le {{ $notification->created_at->format('d/m/Y à H:i') }}</small></p>
        </div>
    </div>
</body>
</html> 