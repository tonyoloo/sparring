

@component('mail::message')
{{-- Greeting --}}
@if (! empty($msg['salutation']))
<p style="color:#1F497D; font-weight: bold;">
    {{$msg['salutation']}}
    </p>
@else
# Hello!
@endif




{{ $msg['emailmessage'] }}
@if (! empty($msg['tableHtml']))
<style>
         .table tr:nth-child(even) td {
            background-color: #410505;
        }

         .table tr:nth-child(odd) td {
            background-color: #1496be;
        }
    </style>
{!! $msg['tableHtml'] !!}


    @else
    
    @endif


<p></p>
<p style="color:#1F497D; font-weight: bold;">
    Sincerely,<br />
    {{ config('app.name') }}
</p>


<p style="color:#64CF95;">
    Higher Education Loans Board <br />
    19th Floor, Anniversary Towers University Way-Nairobi, Kenya <br />
    P.O. Box 69489-00400 <br />
    Tel: +254 711052000<br />
    <!-- DL: +254 711052431 <br /> -->
</p>
<p>
    <span style="color:#1F497D;">
        Website: <a href="https://www.hef.co.ke/" target="_blank" style="color:#1F497D;">https://www.hef.co.ke</a>
    </span>
</p>


<p>
<span style="color:#1F497D;">
<strong>DISCLAIMER:</strong>
        This email message and any file(s) transmitted with it is intended solely for the individual or entity to whom the content relates and may contain confidential and/or legally privileged information which confidentiality and/or privilege is not lost or waived by reason of mistaken transmission. If you have received this message by error you are not authorized to view disseminate distribute or copy the message without the written consent of Higher Education Loans Board and are requested to contact the sender by telephone or e-mail and destroy the original. Although Higher Education Loans Board takes all reasonable precautions to ensure that this message and any file transmitted with it is virus free, Higher Education Loans Board accepts no liability for any damage that may be caused by any virus transmitted by this email.
        </span>
        </p>

@endcomponent
