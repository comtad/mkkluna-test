<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Dev Demo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            background-color: #e5e7eb; /* светло-серый */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: system-ui, sans-serif;
            color: #111827;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        .caption {
            margin-top: 1.5rem;
            font-size: 1.5rem;
            font-weight: 600;
            color: #374151;
            letter-spacing: 0.5px;
            opacity: 0;
            transform: translateY(10px);
            animation: fadeUp 1s ease-out forwards;
            animation-delay: 0.5s;
        }
        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
<img src="{{ asset('maxwell-cat.gif') }}" alt="Maxwell Cat">
<div class="caption">Как будто бы всё работает</div>
</body>
</html>
