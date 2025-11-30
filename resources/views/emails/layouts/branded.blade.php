<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
</head>
<body>
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    {{-- Logo --}}
                    <tr>
                        <td class="header">
                            <a href="{{ config('app.url') }}" style="display: inline-block;">
                                <img src="{{ asset('logo.png') }}" class="logo" alt="{{ config('app.name') }}" height="50" style="height: 50px; width: auto; max-width: 100%; display: block;">
                            </a>
                        </td>
                    </tr>

                    {{-- Email Body --}}
                    <tr>
                        <td class="body" width="100%">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                {{-- Body content --}}
                                <tr>
                                    <td class="content-cell">
                                        {{ $slot }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td>
                            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td align="center">
                                        <p>&copy; {{ date('Y') }} Lift Station. All rights reserved.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <style>
        {!! file_get_contents(resource_path('views/vendor/mail/html/themes/liftstation.css')) !!}
    </style>
</body>
</html>
