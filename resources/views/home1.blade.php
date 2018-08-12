<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>

    <script>
        window.laravel = {!!
            json_encode([

                'api'       => $api,
                'baseURL'   => $base_url,
                'csrfToken' => $csrf_token,
                'logged'    => $logged,
                'user'      => $user,
                'token'     => $token,

            ])
        !!};
    </script>

<link rel="shortcut icon" href="/favicon.ico"></head>
<body>
    <div id="app"></div>
<script type="text/javascript" src="/static/js/manifest.js"></script><script type="text/javascript" src="/static/js/vendor.js"></script><script type="text/javascript" src="/static/js/app.js"></script></body>
</html>
