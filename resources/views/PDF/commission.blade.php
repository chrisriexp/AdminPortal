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
        <table style="font-size: 12px" class="w-full">
            <tr class="w-full font-medium">
                <th class="text-left">Rocket MGA LLC</th>
                <th class="text-center">Commission Payable Statement</th>
                <th class="text-right">{{ $today }}</th>
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