<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
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
                    <td style="width: 18.1712%;"><span style="font-family: Montserrat; font-size: 12pt;"><img src="/var/www/html/AdminPortal/resources/views/PDF/logo.png" alt="Rocket MGA Logo" width="72" height="75" /></span></td>
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
        <h3 ><span style="color: #ced4d9; font-family: Montserrat;">Commissions Payable Statement - <span style="color: #e03e2d;">{{ $month }}</span></span></h3>

        <!-- Commission Paid By Disclaimer -->
        <div><span style="font-family: Montserrat; font-size: 8pt; font-style: italic;"><span style="color: #e03e2d;">**</span> Depending on each carrier's standard operating procedure, commissions are either distributed by Rocket MGA or directly by the carrier. The commission schedule at the end of this document outlines each carrier's commission % as well as who distributes commission. <span style="color: #e03e2d;">**</span></span></div>

        <div >&nbsp;</div>

        {{-- Agency Information --}}
        <table style="border-collapse: collapse; width: 100%; background-color: #e03e2d;" border="0">
            <tbody>
                <tr>
                    {{-- Name --}}
                    <td style="width: 80%; padding: 4px;"><span style="color: #ffffff; font-family: Montserrat; font-size: 12pt;">Agency: {{ $name }}</span></td>
                    {{-- Rocket ID --}}
                    <td style="width: 20%; padding: 4px;"><span style="color: #ffffff; font-family: Montserrat; font-size: 12pt;">Rocket ID: {{ $rocket_id }}</span></td>
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
                    <td style="width: 20.2529%; height: 18px;"><span style="font-family: Montserrat; font-size: 10pt; color: #e03e2d;">Policy</span></td>
                    <td style="width: 14.2529%; height: 18px;"><span style="font-size: 10pt; font-family: Montserrat; color: #e03e2d;">Carrier</span></td>
                    <td style="width: 7.2529%; height: 18px;"><span style="font-size: 10pt; font-family: Montserrat; color: #e03e2d;">Type</span></td>
                    <td style="width: 14.2529%; height: 18px;"><span style="font-family: Montserrat; font-size: 10pt; color: #e03e2d;">Eff Date</span></td>
                    <td style="width: 11.2529%; height: 18px;"><span style="font-size: 10pt; font-family: Montserrat; color: #e03e2d;">Prem</span></td>
                    <td style="width: 11.2529%; height: 18px;"><span style="font-family: Montserrat; font-size: 10pt; color: #e03e2d;">Comm</span></td>
                </tr>
            </tbody>
        </table>

        {{-- Policies Paid By Rocket --}}
        <div >
            <table style="border-collapse: collapse; width: 100%; height: 19px;" border="1">
                <tbody>
                    @foreach($rocketPay->policies as $policy)
                        <tr style="height: 19px;">
                            {{-- Insured --}}
                            <td style="width: 21.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->insured }}</span></td>
                            {{-- Policy --}}
                            <td style="width: 20.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->policy }}</span></td>
                            {{-- Carrier --}}
                            <td style="width: 14.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->carrier }}</span></td>
                            {{-- Transaction Type --}}
                            <td style="width: 7.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->trans_type }}</span></td>
                            {{-- Effective Date --}}
                            <td style="width: 14.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->eff }}</span></td>
                            {{-- Premium --}}
                            <td style="width: 11.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">${{ $policy->prem }}</span></td>
                            {{-- Policy --}}
                            <td style="width: 11.1686%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">${{ $policy->comm }}</span></td>
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
                        <td style="width: 33.3333%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Policies: {{ count($rocketPay->policies) }}</span></td>
                        <td style="width: 33.3333%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Premium: ${{ $rocketPay->prem }}</span></td>
                        <td style="width: 33.3333%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Commission: ${{ $rocketPay->comm }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div  style="opacity: 20%"><hr /></div>
        <div >&nbsp;</div>

        {{-- Paid By Carrier --}}
        <div ><span style="font-family: Montserrat; background-color: #ecf0f1; padding: 5px 5px 5px 5px;">Paid By Carrier:</span></div>
        <div >&nbsp;</div>

        <!-- Direct Pay Disclaimer -->
        <div><span style="font-family: Montserrat; font-size: 8pt; font-style: italic;"><span style="color: #e03e2d;">**</span> Your agency has received commission for these accounts directly from the carrier. <span style="color: #e03e2d;">**</span></span></div>

        <div >&nbsp;</div>

        <div >
            {{-- Headers --}}
            <table style="border-collapse: collapse; width: 100%; height: 18px;" border="0">
                <tbody>
                    <tr style="height: 18px;">
                        <td style="width: 21.2529%; height: 18px;"><span style="font-size: 10pt; font-family: Montserrat; color: #e03e2d;">Insured</span></td>
                        <td style="width: 20.2529%; height: 18px;"><span style="font-family: Montserrat; font-size: 10pt; color: #e03e2d;">Policy</span></td>
                        <td style="width: 14.2529%; height: 18px;"><span style="font-size: 10pt; font-family: Montserrat; color: #e03e2d;">Carrier</span></td>
                        <td style="width: 7.2529%; height: 18px;"><span style="font-size: 10pt; font-family: Montserrat; color: #e03e2d;">Type</span></td>
                        <td style="width: 14.2529%; height: 18px;"><span style="font-family: Montserrat; font-size: 10pt; color: #e03e2d;">Eff Date</span></td>
                        <td style="width: 11.2529%; height: 18px;"><span style="font-size: 10pt; font-family: Montserrat; color: #e03e2d;">Prem</span></td>
                        <td style="width: 11.2529%; height: 18px;"><span style="font-family: Montserrat; font-size: 10pt; color: #e03e2d;">Comm</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div >
            <table style="border-collapse: collapse; width: 100%; height: 19px;" border="1">
                <tbody>
                    @foreach($carrierPay->policies as $policy)
                        <tr style="height: 19px;">
                            {{-- Insured --}}
                            <td style="width: 21.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->insured }}</span></td>
                            {{-- Policy --}}
                            <td style="width: 20.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->policy }}</span></td>
                            {{-- Carrier --}}
                            <td style="width: 14.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->carrier }}</span></td>
                            {{-- Transaction Type --}}
                            <td style="width: 7.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->trans_type }}</span></td>
                            {{-- Effective Date --}}
                            <td style="width: 14.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">{{ $policy->eff }}</span></td>
                            {{-- Premium --}}
                            <td style="width: 11.2857%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">${{ $policy->prem }}</span></td>
                            {{-- Policy --}}
                            <td style="width: 11.1686%; height: 19px; padding: 4px;"><span style="font-family: Montserrat; font-size: 8pt;">${{ $policy->comm }}</span></td>
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
                        <td style="width: 33.3333%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Policies: {{ count($carrierPay->policies) }}</span></td>
                        <td style="width: 33.3333%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Premium: ${{ $carrierPay->prem }}</span></td>
                        <td style="width: 33.3333%; text-align: left; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Commission: ${{ $carrierPay->comm }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div  style="opacity: 20%"><hr /></div>
        {{-- Agency Totals -- Add Agency Name Before "Totals" --}}
        <div  style="padding: 5px 0px 5px 0px;"><span style="font-family: Montserrat; color: #e03e2d;">{{ $name }} Totals</span></div>

        {{-- Page Break Here --}}
        <div  style="page-break-after:always;">
            <table style="border-collapse: collapse; width: 100%; background-color: #ecf0f1; border-color: #ced4d9;" border="1" cellpadding="5px">
                <tbody>
                    <tr style="border: 1px solid #CED4D9;">
                        <td style="width: 33.3333%; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Policies: {{$policies}}</span></td>
                        <td style="width: 33.3333%; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Premium: ${{$prem}}</span></td>
                        <td style="width: 33.3333%; border-right: 1px solid #b9bdc0; padding: 3px;"><span style="font-family: Montserrat; font-size: 10pt;">Commission: ${{$comm}}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Commission Schedule --}}
        <img style="width: 100%; height: 100%;" src="/var/www/html/AdminPortal/resources/views/PDF/commission_schedule.jpg" alt="Commission Schedule">
    </body>
</html>