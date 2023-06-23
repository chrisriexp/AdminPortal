<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <title>RocketMGA | Commission Statement</title>
        @vite('resources/css/app.css')
        <style type="text/css">
            /* Add your CSS styles here */
            body{
                font-style: normal;
                font-family: 'Montserrat', sans-serif !important;
            }

            div.page_break + div.page_break{
                page-break-before: always;
            }

            footer{
                position: fixed;
                left: 0px;
                right: 0px;
                height: 150px;
                bottom: 0px;
                margin-bottom: -75px;
            }
            /* ... */
        </style>
    </head>
    <body>
        {{-- PDF Footer --}}
        <footer>
            {{-- Footer --}}
            <div >
                <div >&nbsp;</div>
            </div>
            <div >&nbsp;</div>
            <div >&nbsp;</div>
            <div ><span style="font-family: Montserrat; font-size: 10pt;">Rocket <span style="color: #e03e2d;">MGA </span>LLC &copy; 2023</span></div>
        </footer>

        {{-- PDF Header --}}
        <table style="border-collapse: collapse; width: 100%;" border="0">
            <tbody>
                <tr>
                    <td style="width: 18.1712%;"><span style="font-family: Montserrat; font-size: 12pt;"><img src="C:\Users\CRocha\Documents\_Admin.RocketFlood\admin.rocketflood\resources\views\PDF\logo.png" alt="" width="72" height="75" /></span></td>
                    <td style="width: 41.9712%; text-align: center;">
                    <div><span style="font-family: Montserrat; font-size: 12pt;">Rocket <span style="color: #e03e2d;">MGA </span>LLC</span></div>
                    <div><span style="font-family: Montserrat; color: #e03e2d;">commissions@rocketflood.com</span></div>
                    </td>
                    <td style="width: 39.7404%; text-align: right;">
                    {{-- Statement Date --}}
                    <div><span style="font-family: Montserrat; font-size: 12pt;">Statement Date: <span style="color: #e03e2d;"> {{ $today }} </span></span></div>
                    {{-- Statement Month --}}
                    <div><span style="font-family: Montserrat; font-size: 12pt;">Statement Month: <span style="color: #e03e2d;"> {{ $month }} </span></span></div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div >&nbsp;</div>
        <h3 ><span style="color: #ced4d9; font-family: Montserrat;">Commissions Payable Statement - All <span style="color: #e03e2d;">MGA </span>Agencies</span></h3>

        {{-- Grand Total for Month --}}
        <div  style="padding: 5px 0px 5px 0px;"><span style="color: #e03e2d;"><span style="color: #ced4d9; font-family: Montserrat;"><span style="color: #e03e2d;">Grand Totals</span></span></span></div>
        <div >
            <table style="border-collapse: collapse; width: 100%; background-color: #ecf0f1;">
                <tbody>
                    <tr style="border: 1px solid #CED4D9;">
                    <td style="width: 25.0575%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Policies: {{ $policies }}</span></td>
                    <td style="width: 25.0575%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Premium: ${{ $prem }}</span></td>
                    <td style="width: 25.0575%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Commission: ${{ $comm }}</span></td>
                    <td style="width: 25.0575%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Override: ${{ $override }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div >&nbsp;</div>

        {{-- Agency Information --}}
        @foreach($agencies as $agency)
            <br>

            <table style="border-collapse: collapse; width: 100%; background-color: #e03e2d;" border="0">
                <tbody>
                    <tr>
                        {{-- Name --}}
                        <td style="width: 80%; padding: 4px;"><span style="color: #ffffff; font-family: Montserrat; font-size: 12pt;">Agency: {{ $agency->name }}</span></td>
                        {{-- Rocket ID --}}
                        <td style="width: 20%; padding: 4px;"><span style="color: #ffffff; font-family: Montserrat; font-size: 12pt;">Rocket ID: {{ $agency->rocket_id }}</span></td>
                    </tr>
                </tbody>
            </table>

            {{-- Commissions Paid by Rocket --}}
            <div >&nbsp;</div>
            <div ><span style="font-family: Montserrat; background-color: #ecf0f1; padding: 5px 5px 5px 5px;">Paid By Rocket MGA:</span></div>
            <div >&nbsp;</div>

            {{-- Headers --}}
            <table style="border-collapse: collapse; width: 100%; height: 18px;" border="0">
                <tbody>
                    <tr style="height: 18px;">
                        <td style="width: 21.2529%; height: 18px;"><span style="font-size: 10pt; font-family: Montserrat; color: #e03e2d;">Insured</span></td>
                        <td style="width: 21.2529%; height: 18px;"><span style="font-family: Montserrat; font-size: 10pt; color: #e03e2d;">Policy</span></td>
                        <td style="width: 7.2529%; height: 18px;"><span style="font-size: 10pt; font-family: Montserrat; color: #e03e2d;">Type</span></td>
                        <td style="width: 14.2529%; height: 18px;"><span style="font-family: Montserrat; font-size: 10pt; color: #e03e2d;">Eff Date</span></td>
                        <td style="width: 7.2529%; height: 18px;"><span style="font-size: 10pt; font-family: Montserrat; color: #e03e2d;">Rate</span></td>
                        <td style="width: 14.2529%; height: 18px;"><span style="font-size: 10pt; font-family: Montserrat; color: #e03e2d;">Premium</span></td>
                        <td style="width: 14.2529%; height: 18px;"><span style="font-family: Montserrat; font-size: 10pt; color: #e03e2d;">Commission</span></td>
                    </tr>
                </tbody>
            </table>

            {{-- Policies Paid By Rocket --}}
            <div >
                <table style="border-collapse: collapse; width: 100%; height: 19px;" border="1">
                    <tbody>
                        @foreach($agency->rocketPay->policies as $policy)
                            <tr style="height: 19px;">
                                {{-- Insured --}}
                                <td style="width: 21.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->insured }}</span></td>
                                {{-- Policy --}}
                                <td style="width: 21.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->policy }}</span></td>
                                {{-- Transaction Type --}}
                                <td style="width: 7.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->trans_type }}</span></td>
                                {{-- Effective Date --}}
                                <td style="width: 14.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->eff }}</span></td>
                                {{-- Rate --}}
                                <td style="width: 7.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ (float) number_format($policy->comm / $policy->prem * 100, 1, '.', '') }}%</span></td>
                                {{-- Premium --}}
                                <td style="width: 14.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">${{ $policy->prem }}</span></td>
                                {{-- Policy --}}
                                <td style="width: 14.1686%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">${{ $policy->comm }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div >&nbsp;</div>
            {{-- Total Paid By Rocket --}}
            <div >
                <table style="border-collapse: collapse; width: 100%; background-color: #ecf0f1;">
                    <tbody>
                        <tr style="background-color: #ecf0f1; text-align: center; border: 1px solid #CED4D9;">
                            <td style="width: 25.0575%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Policies: {{ count($agency->rocketPay->policies) }}</span></td>
                            <td style="width: 25.0575%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Premium: ${{ $agency->rocketPay->prem }}</span></td>
                            <td style="width: 25.0575%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Commission: ${{ $agency->rocketPay->comm }}</span></td>
                            <td style="width: 25.0575%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Override: ${{ $agency->rocketPay->override }}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div  style="opacity: 20%"><hr /></div>
            <div >&nbsp;</div>

            {{-- Paid By Carrier --}}
            <div ><span style="font-family: Montserrat; background-color: #ecf0f1; padding: 5px 5px 5px 5px;">Paid By Carrier:</span></div>
            <div >&nbsp;</div>

            <div >
                {{-- Headers --}}
                <table style="border-collapse: collapse; width: 100%; height: 18px;" border="0">
                    <tbody>
                        <tr style="height: 18px;">
                            <td style="width: 21.2529%; height: 18px;"><span style="font-size: 10pt; font-family: Montserrat; color: #e03e2d;">Insured</span></td>
                            <td style="width: 21.2529%; height: 18px;"><span style="font-family: Montserrat; font-size: 10pt; color: #e03e2d;">Policy</span></td>
                            <td style="width: 7.2529%; height: 18px;"><span style="font-size: 10pt; font-family: Montserrat; color: #e03e2d;">Type</span></td>
                            <td style="width: 14.2529%; height: 18px;"><span style="font-family: Montserrat; font-size: 10pt; color: #e03e2d;">Eff Date</span></td>
                            <td style="width: 7.2529%; height: 18px;"><span style="font-size: 10pt; font-family: Montserrat; color: #e03e2d;">Rate</span></td>
                            <td style="width: 14.2529%; height: 18px;"><span style="font-size: 10pt; font-family: Montserrat; color: #e03e2d;">Premium</span></td>
                            <td style="width: 14.2529%; height: 18px;"><span style="font-family: Montserrat; font-size: 10pt; color: #e03e2d;">Commission</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div >
                <table style="border-collapse: collapse; width: 100%; height: 19px;" border="1">
                    <tbody>
                        @foreach($agency->carrierPay->policies as $policy)
                            <tr style="height: 19px;">
                                {{-- Insured --}}
                                <td style="width: 21.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->insured }}</span></td>
                                {{-- Policy --}}
                                <td style="width: 21.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->policy }}</span></td>
                                {{-- Transaction Type --}}
                                <td style="width: 7.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->trans_type }}</span></td>
                                {{-- Effective Date --}}
                                <td style="width: 14.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->eff }}</span></td>
                                {{-- Rate --}}
                                <td style="width: 7.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ (float) number_format($policy->comm / $policy->prem * 100, 1, '.', '') }}%</span></td>
                                {{-- Premium --}}
                                <td style="width: 14.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">${{ $policy->prem }}</span></td>
                                {{-- Policy --}}
                                <td style="width: 14.1686%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">${{ $policy->comm }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paid by Carriers Totals --}}
            <div >&nbsp;</div>
            <div >
                <table style="border-collapse: collapse; width: 100%; background-color: #ecf0f1;">
                    <tbody>
                        <tr style="background-color: #ecf0f1; text-align: center; border: 1px solid #CED4D9;">
                            <td style="width: 25.0575%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Policies: {{ count($agency->carrierPay->policies) }}</span></td>
                            <td style="width: 25.0575%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Premium: ${{ $agency->carrierPay->prem }}</span></td>
                            <td style="width: 25.0575%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Commission: ${{ $agency->carrierPay->comm }}</span></td>
                            <td style="width: 25.0575%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Override: ${{ $agency->carrierPay->override }}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div  style="opacity: 20%"><hr /></div>
            {{-- Agency Totals -- Add Agency Name Before "Totals" --}}
            <div  style="padding: 5px 0px 5px 0px;"><span style="font-family: Montserrat; color: #e03e2d;">{{ $agency->name }} Totals</span></div>

            {{-- Page Break Here --}}
            <div  style="page-break-after:always;">
                <table style="border-collapse: collapse; width: 100%; background-color: #ecf0f1; border-color: #ced4d9;" border="1" cellpadding="5px">
                    <tbody>
                        <tr style="border: 1px solid #CED4D9;">
                            <td style="width: 24.9707%; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Policies: {{$agency->policies}}</span></td>
                            <td style="width: 24.9707%; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Premium: ${{$agency->prem}}</span></td>
                            <td style="width: 24.9707%; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Commission: ${{$agency->comm}}</span></td>
                            <td style="width: 24.9707%; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Override: ${{$agency->override}}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach

        {{-- Commission Schedule --}}
        <img style="width: 100%; height: 100%;" src="C:\Users\CRocha\Documents\_Admin.RocketFlood\admin.rocketflood\resources\views\PDF\commission_schedule.png" alt="Commission Schedule">
    </body>
</html>