<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ProConvey</title>

  <meta http-equiv="refresh" content="0; url={{ $url }}" />
</head>
<body style="text-align: center; margin: 3rem;">
  <img src="{{ asset('img/logo.png') }}" alt="ProConvey" style="width: 80%; max-width: 350px" />

  <div style="margin-top: 8rem; display: flex; justify-content: center;">
    <div style="display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 3rem;">
      <a href="https://apps.apple.com/us/app/proconvey/id6446494939">
        <img src="{{ asset('img/ios_download.png') }}" alt="Download on the App Store" style="max-width: 200px; max-height: 70px;" />
      </a>

      <a href="https://play.google.com/store/apps/details?id=uk.co.proconvey.mobile">
        <img src="{{ asset('img/android_download.png') }}" alt="Get it on Google Play" style="max-width: 200px; max-height: 70px;" />
      </a>
    </div>
  </div>
</body>
</html>