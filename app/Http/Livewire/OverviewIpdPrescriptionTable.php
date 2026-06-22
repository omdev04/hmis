<?php

namespace App\Http\Livewire;

use App\Models\IpdPrescription;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class OverviewIpdPrescriptionTable extends LivewireTableComponent
{
    public $ipdPrescriptionId;

    protected $model = IpdPrescription::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setSearchVisibilityDisabled()
            ->setPerPageVisibilityDisabled();
    }

    public function mount(int $ipdPrescriptionId)
    {
        $this->ipdPrescriptionId = $ipdPrescriptionId;
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.ipd_patient.ipd_number'), 'patient.ipd_number')
                ->view('ipd_prescriptions.columns.ipd_no'),
            Column::make(__('messages.common.created_on'), 'created_at')
                ->view('ipd_prescriptions.columns.created_at'),
        ];
    }

    public function builder(): Builder
    {
        $query = IpdPrescription::with('patient')->where('ipd_patient_department_id',
            $this->ipdPrescriptionId)->latest()->take(5);
        if (! empty($query)) {
            $query->get()->toQuery();
        }

        return $query->select('ipd_prescriptions.*');
    }
}
