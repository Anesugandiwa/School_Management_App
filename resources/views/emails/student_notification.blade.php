<!DOCTYPE html>
<html>
<head>
    <title>New Activity</title>
</head>
<body>
    <h1>Hello {{ $student->name }},</h1>

    <p>A new activity has been created:</p>

    <h2>{{ $activity->title }}</h2>
    <p>{{ $activity->description }}</p>

    <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($activity->start_date)->format('m/d/Y') }}</p>

    @if($activity->end_date)
        <p><strong>End Date:</strong> {{ \Carbon\Carbon::parse($activity->end_date)->format('m/d/Y') }}</p>
    @endif

    <p>Thank you and have a great day!</p>
</body>
</html>
