<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Status</title>
</head>
<body>
    <h1>Database Connection Status</h1>
    @if(DB::connection()->getPdo())
        <p>You are connected to database: {{ DB::connection()->getDatabaseName() }}</p>
        <p>Tables in the database:</p>
        <ul>
            @foreach(DB::select('SHOW TABLES') as $table)
                <li>{{ $table->{'Tables_in_' . env('DB_DATABASE')} }}</li>
            @endforeach
        </ul>
    @else
        <p>Database connection failed</p>
    @endif
</body>
</html>
