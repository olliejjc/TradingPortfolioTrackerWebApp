<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>TradingPortfolioTrackerWebApp</title>
    </head>
    <body class="antialiased">
        <div id="app"></div>
        <link href="css/styles.css" rel="stylesheet" />
        <link href="css/portfoliotracker.css" rel="stylesheet" />
        <script src="/js/app.js"></script>
    </body>
</html>