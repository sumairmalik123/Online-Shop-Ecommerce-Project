<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contact Email</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
    <h1>You have received a contact email</h1>
    <h2>Name: {{ $mailData['name'] }}</h2>
    <h2>Email: {{ $mailData['email'] }}</h2>
    <h2>Subject: {{ $mailData['subject'] }}</h2>


    <p>Message:</p>
    <h2>Message: {{ $mailData['message'] }}</h2>
</body>
</html>