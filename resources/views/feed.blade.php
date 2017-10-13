<!DOCTYPE html>
<html>
<head>

</head>
<body>
    @foreach ($tweets as $tweet)
        <p>{{ $tweet -> user}} at: {{ $tweet -> tweet_posted_at}}</p>
        <p>{{ $tweet -> tekst}}</p>
    @endforeach
</body>

</html>