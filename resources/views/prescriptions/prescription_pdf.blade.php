<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "//www.w3.org/TR/html4/strict.dtd">
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="icon" href="{{ asset('web/img/hms-saas-favicon.ico') }}" type="image/png">
    <title>{{ __('messages.prescription.prescription') }}</title>
    <link href="{{ asset('assets/css/prescriptions-pdf.css') }}" rel="stylesheet" type="text/css" />
    <style>
        body {
            font-family: DejaVu Sans, Arial, "Helvetica", Arial, "Liberation Sans", sans-serif;
        }
    </style>
</head>

<body>
    <div class="px-30">
        <table>
            <tbody>
                <tr>
                    <td class="company-logo">
                        <img src="{{ $data['app_logo'] }}" alt="user">
                    </td>
                    <td class="px-30">
                        <h3 class="mb-0 lh-1">
                            {{ !empty($prescription['prescription']->doctor->doctorUser->full_name) ? $prescription['prescription']->doctor->doctorUser->full_name : '' }}
                        </h3>
                        <div class="fs-5 text-gray-600 fw-light mb-0 lh-1">
                            {{ !empty($prescription['prescription']->doctor->specialist) ? $prescription['prescription']->doctor->specialist : '' }}
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <hr>
    <div class="px-30">
        <table class="table w-100 mb-20">
            <tbody>
                <tr>
                    <td class="desc vertical-align-top bg-light">
                        <div><b>{{ __('messages.user.address_details') }} :</b></div>
                        <div class="col-md-4 co-12 mt-md-0 mt-5">
                            @if (empty($prescription['prescription']->doctor->address->address1) &&
                                    empty($prescription['prescription']->doctor->address->address2) &&
                                    empty($prescription['prescription']->doctor->address->city))
                            @else
                                {{ !empty($prescription['prescription']->doctor->address->address1) ? $prescription['prescription']->doctor->address->address1 : '' }}
                                {{ !empty($prescription['prescription']->doctor->address->address2) ? (!empty($prescription['prescription']->doctor->address->address1) ? ',' : '') : '' }}
                                {{ empty($prescription['prescription']->doctor->address->address1) || !empty($prescription['prescription']->doctor->address->address2) ? (!empty($prescription['prescription']->doctor->address->address2) ? $prescription['prescription']->doctor->address->address2 : '') : '' }}
                                {{ !empty($prescription['prescription']->doctor->address->city) ? ',' : '' }}
                                @if (!empty($prescription['prescription']->doctor->address->city))
                                    <br>
                                @endif
                                {{ !empty($prescription['prescription']->doctor->address->city) ? $prescription['prescription']->doctor->address->city : '' }}
                                {{ !empty($prescription['prescription']->doctor->address->zip) ? ',' : '' }}
                                @if ($prescription['prescription']->doctor->address->zip)
                                    <br>
                                @endif
                                {{ !empty($prescription['prescription']->doctor->address->zip) ? $prescription['prescription']->doctor->address->zip : '' }}
                                <p class="text-gray-600 mb-3">
                                    {{ !empty($prescription['prescription']->doctor->user->phone) ? $prescription['prescription']->doctor->user->phone : '' }}
                                </p>
                                <p class="text-gray-600 mb-3">
                                    {{ !empty($prescription['prescription']->doctor->user->email) ? $prescription['prescription']->doctor->user->email : '' }}
                                </p>
                            @endif
                        </div>
                    </td>
                    <td style="width:2%;">
                    </td>
                    <td class="text-end desc ms-auto vertical-align-top bg-light">
                        <table class="table w-100">
                            <tr class="">
                                <td class="">
                                    <label for="name"
                                        class="pb-2 fs-5 text-gray-600 me-1">{{ __('messages.bill.patient_name') }}:</label>

                                </td>
                                <td class="text-end fs-5 text-gray-800">

                                    {{ !empty($prescription['prescription']->patient->patientUser->full_name) ? $prescription['prescription']->patient->patientUser->full_name : '' }}

                                </td>
                            </tr>
                            <tr class="">
                                <td class="">
                                    <label for="name"
                                        class="pb-2 fs-5 text-gray-600 me-1">{{ __('messages.case.date') }}:</label>

                                </td>
                                <td class="text-end fs-5 text-gray-800">
                                    {{ !empty(\Carbon\Carbon::parse($prescription['prescription']->created_at)->isoFormat('DD/MM/Y')) ? \Carbon\Carbon::parse($prescription['prescription']->created_at)->isoFormat('DD/MM/Y') : '' }}
                                </td>
                            </tr>
                            @if ($prescription['prescription']->patient->user->dob)
                                <tr class="">
                                    <td>
                                        <label for="name"
                                            class="pb-2 fs-5 text-gray-600 me-1">{{ __('messages.blood_donor.age') }}:</label>
                                    </td>
                                    <td class="text-end fs-5 text-gray-800">
                                        {{ \Carbon\Carbon::parse($prescription['prescription']->patient->user->dob)->diff(\Carbon\Carbon::now())->y }}
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @if ($prescription['prescription']->problem_description != null)
        <div class="mb-20 px-30">
            <div class="heading">
                <div class="fw-6">{{ __('messages.prescription.problem') }}:</div>
            </div>
            <div class="">
                <p class="text-gray-600 mb-2 fs-4">{{ $prescription['prescription']->problem_description }}</p>
            </div>
        </div>
    @endif
    @if ($prescription['prescription']->test != null)
        <div class="mb-20 px-30">
            <div class="heading">
                <div class="fw-6">{{ __('messages.prescription.test') }}:</div>
            </div>
            <div class="">
                <p class="text-gray-600 mb-2 fs-4">{{ $prescription['prescription']->test }}</p>
            </div>
        </div>
    @endif
    @if ($prescription['prescription']->advice != null)
        <div class="mb-20 px-30">
            <div class="heading">
                <div class="fw-6">{{ __('messages.prescription.advice') }}:</div>
            </div>
            <div class="">
                <p class="text-gray-600 mb-2 fs-4">{{ $prescription['prescription']->advice }}</p>
            </div>
        </div>
    @endif
    <div class="px-30">
        <div class="heading mb-20">
            <div class="fw-6">{{ __('messages.prescription.rx') }}:</div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th scope="col">{{ __('messages.prescription.medicine_name') }}</th>
                    <th scope="col">{{ __('messages.ipd_patient_prescription.dosage') }}</th>
                    <th scope="col">{{ __('messages.prescription.duration') }}</th>
                    <th scope="col">{{ __('messages.medicine_bills.dose_interval') }}</th>
                </tr>
            </thead>
            <tbody>
                @if (empty($medicines))
                    {{ __('messages.common.n/a') }}
                @else
                    @foreach ($prescription['prescription']->getMedicine as $medicine)
                        @foreach ($medicine->medicines as $medi)
                            <tr>
                                <td class="py-4 border-bottom-0">{{ $medi->name }}</td>
                                <td class="py-4 border-bottom-0">
                                    {{ $medicine->dosage }}

                                    @if ($medicine->time == 0)
                                        {{ __('messages.prescription.after_meal') }}
                                    @else
                                        {{ __('messages.prescription.before_meal') }}
                                    @endif

                                </td>
                                <td class="py-4 border-bottom-0">{{ $medicine->day }}
                                    {{ __('messages.appointment.day') }}
                                </td>
                                <td class="py-4 border-bottom-0">
                                    {{ App\Models\Prescription::DOSE_INTERVAL[$medicine->dose_interval] }}</td>
                            </tr>
                        @break
                    @endforeach
                @endforeach
            @endif
        </tbody>
    </table>
</div>
<div class="px-30">
    <table width="100%">
        <tr>
            <td class="header-left">
                @if ($prescription['prescription']->next_visit_qty != null)
                    <h4>
                        {{ __('messages.prescription.next_visit') }} :
                        {{ $prescription['prescription']->next_visit_qty }}
                        @if ($prescription['prescription']->next_visit_time == 0)
                            {{ __('messages.prescription.days') }}
                        @elseif($prescription['prescription']->next_visit_time == 1)
                            {{ __('messages.month') }}
                        @else
                            {{ __('messages.year') }}
                        @endif
                    </h4>
                @endif
            </td>
            <td class="header-right">
                <h3 class="mb-0 lh-1">
                    {{ !empty($prescription['prescription']->doctor->doctorUser->full_name) ? $prescription['prescription']->doctor->doctorUser->full_name : '' }}
                </h3>
                <div class="fs-5 text-gray-600 fw-light mb-0 lh-1">
                    {{ !empty($prescription['prescription']->doctor->specialist) ? $prescription['prescription']->doctor->specialist : '' }}
                </div>
            </td>
        </tr>
    </table>
</div>
</div>
</body>

</html>
