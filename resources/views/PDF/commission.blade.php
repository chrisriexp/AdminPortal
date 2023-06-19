<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" referrerpolicy="no-referrer" />
        <title>RocketMGA | Commission Statement</title>
        @vite('resources/css/app.css')
    </head>
    <body>
        <table style="font-size: 14px" class="w-full">
            <tr class="w-full">
                <th><img style="height: 48px;" src="C:\Users\CRocha\Documents\_Admin.RocketFlood\admin.rocketflood\resources\views\PDF\favicon.png" alt="Logo"></th>
                <th class="text-center">Commission Payable Statement</th>
                <th class="text-right">{{ $today }}</th>
            </tr>

            <tr style="padding-top: 10px; padding-bottom: 10px;" class="w-full">
                <th style="color: rgb(244, 45, 45)" class="text-left"></th>
                <th class="text-center">Statement Month - {{ $month }}</th>
                <th class="text-right">All MGA Commissions</th>
            </tr>
            @foreach ($collection as $item)
                <tr>
                    <td>{{$item->policy}}</td>
                    <td>{{$item->prem}}</td>
                    <td>{{$item->comm}}</td>
                </tr>
            @endforeach
        </table>
    </body>
</html>