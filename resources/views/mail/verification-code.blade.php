<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Код верификации</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }
        .code-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
            letter-spacing: 5px;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">РЕАЛКНИГА</div>
            <h1>Код верификации</h1>
        </div>
        
        <p>Здравствуйте!</p>
        <p>Вы зарегистрировались на сайте РеалКнига. Для завершения регистрации введите этот код в форме верификации:</p>
        
        <div class="code-container">
            <p>Ваш код верификации:</p>
            <div class="code">{{ $code }}</div>
        </div>
        
        <p><strong>Важно:</strong></p>
        <ul>
            <li>Код действителен 15 минут</li>
            <li>Не сообщайте этот код никому</li>
            <li>Если вы не регистрировались, просто проигнорируйте это письмо</li>
        </ul>
        
        <div class="footer">
            <p>С уважением,<br>Команда РеалКнига</p>
            <p><small>Это автоматическое письмо, пожалуйста не отвечайте на него.</small></p>
        </div>
    </div>
</body>
</html>
