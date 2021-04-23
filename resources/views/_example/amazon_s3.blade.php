<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dev | Sample Amazon S3</title>
</head>
<body>
    <form action="{{ route('dev.amazon_s3.post') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="uploaded_file" id="uploaded_file" required>
        <button type="submit">Submit</button>
    </form>
    
    <hr>

    @if (session('result'))
        <a href="{{ session('result') }}" target="_blank">{{ session('result') }}</a>
    @endif
</body>
</html>