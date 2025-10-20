<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .error-template {
            max-width: 600px;
            margin: auto;
        }
        .error-template h1 {
            font-size: 100px;
            font-weight: bold;
            color: #343a40;
        }
        .error-template h2 {
            font-size: 30px;
            color: #6c757d;
        }
        .error-template p {
            font-size: 18px;
            color: #6c757d;
        }
        .btn-home {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="error-template">
        <img src="{{ asset('images/Hi-Tech_Water_Logo.png') }}" alt="Error Image" width="60%" class="img-fluid mb-4">
        <h1>404</h1>
        <h2>Oops! Page not found</h2>
        <p class="text-primary">Customer Relationship Management System</p>
        <a href="{{ route('dashboard.index') }}" class="btn btn-primary btn-home">{{__('Return to Home')}}</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
