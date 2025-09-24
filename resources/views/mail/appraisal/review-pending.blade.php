<x-mail::message>
# Appraisal Record Pending Review

Hello {{ $currentApprover ?? 'Approver' }},

The appraisal record for **{{ $appraisee->full_name }}** is pending for your review. 
Please click the button below to review it.
 
<x-mail::panel>
<div>{{ $appraisee->fullname }} - {{ '#'.$appraisee->employee_no }}</div>
<div>{{ $appraisee->role->name }} ({{ $appraisee->position->name }})</div>
<div>
    @isset($departments)
        {{ $departments->join(', ') }}
    @endisset
</div>
<div>
    {{ $season }}
</div>

</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>