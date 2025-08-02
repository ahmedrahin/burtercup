@php
    $backgroundImage = (asset('assets/images/verified-email.jpg'));
    $social = App\Models\SocialLink::first();
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to BarterCup</title>
    <style>
        p {
            font-size: 18px !important;
        }
    </style>
</head>
<body style="margin:0; padding:0;">
    <table width="30%" border="0" cellspacing="0" cellpadding="0" background="{{ $backgroundImage }}" style="background-size: cover;margin: auto;background-repeat: no-repeat; font-family: Arial, sans-serif;background-position: 50%;">
        <tr>
            <td align="center" style="padding: 40px;padding-bottom: 0;">
                <table width="700" cellpadding="0" cellspacing="0" style="color: white; border-radius: 10px; text-align: left; padding: 40px;padding-bottom: 0;">
                    <tr>
                        <td style="padding: 40px;padding-top:0;padding-bottom: 0;">
                            <img src="{{ (asset('assets/images/logo.png')) }}" alt="BarterCup" width="150" style="display:block;margin: 0 auto 50px;">
                            <h1 style="font-size: 24px; margin-bottom: 50px;text-align: center;">Welcome To Bartercup, a New World Economy</h1>
                            <a href="{{ $verificationUrl }}" style="padding: 12px 30px; background-color: #ffffff;color: #000000;text-decoration: none;font-weight: bold;border-radius: 25px;display: block;margin: 0 auto 60px;width: 170px;text-align: center;">Verify your email here</a>
                            <p style="margin-top: 20px; font-size: 14px; line-height: 1.6;">
                                Soon, shopping for free will be a very real scenario.
                                <br><br>
                                In case you're wondering, it's a real world of transactions where you get physical things you love or service you need.
                                <br><br>
                                No cash necessary. Well except for small shipping fees, because we haven't invented teleportation yet.
                                <br><br>
                                Anyhoo, you have received 100 free <strong>BarterCoins</strong> for a start, which you can instantly use when you download our App in August.
                            </p>
                            <p style="margin-top: 20px;display: flex;align-items: center;">
                                Don’t hesitate to follow us at:
                                <a href="{{ $social->instagram_link ?? '#' }}" style="color: white;"><img src="{{ asset('assets/images/instragram.png') }}" style="width: 26px;padding: 0 10px;"></a>
                                {{-- <a href="{{ $social->youtube_link ?? '#' }}" style="color: white;"><img src="{{ asset('assets/images/youtube.png') }}" style="width: 17px;"></a> --}}
                            </p>
                            <br>
                            <p>
                                and share this revolutionary new world with your loved friends so they too, enjoy 100 free BarterCoins too and shop for free.
                            </p>
                            <br>
                            <p>Chat soon,<br></p>
                            <p style="margin-top: 30px;">
                                Love,
                                <br>
                                <strong>Team BarterCup</strong>
                            </p>
                            <p style="font-size: 12px; color: #fff; margin-top: 50%;text-align: center;margin-bottom: 40px;">
                                All rights reserved ©2025 BARTERCUP
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
