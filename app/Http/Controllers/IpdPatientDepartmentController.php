<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateIpdPatientDepartmentRequest;
use App\Http\Requests\UpdateIpdPatientDepartmentRequest;
use App\Models\IpdCharge;
use App\Models\IpdPatientDepartment;
use App\Models\IpdPayment;
use App\Repositories\IpdBillRepository;
use App\Repositories\IpdPatientDepartmentRepository;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class IpdPatientDepartmentController extends AppBaseController
{
    /** @var IpdPatientDepartmentRepository */
    private $ipdPatientDepartmentRepository;

    public function __construct(IpdPatientDepartmentRepository $ipdPatientDepartmentRepo)
    {
        $this->ipdPatientDepartmentRepository = $ipdPatientDepartmentRepo;
    }

    public function index()
    {
        $statusArr = IpdPatientDepartment::STATUS_ARR;

        return view('ipd_patient_departments.index', compact('statusArr'));
    }

    public function create()
    {
        $data = $this->ipdPatientDepartmentRepository->getAssociatedData();

        return view('ipd_patient_departments.create', compact('data'));
    }

    public function store(CreateIpdPatientDepartmentRequest $request)
    {
        $input = $request->all();
        $this->ipdPatientDepartmentRepository->store($input);
        $this->ipdPatientDepartmentRepository->createNotification($input);
        Flash::success(__('messages.ipd_patient.ipd_patient').' '.__('messages.common.saved_successfully'));

        return redirect(route('ipd.patient.index'));
    }

    public function show(IpdPatientDepartment $ipdPatientDepartment)
    {
        $doctors = $this->ipdPatientDepartmentRepository->getDoctorsData();

        $consultantRegister = $this->ipdPatientDepartmentRepository->getConsultantRegister($ipdPatientDepartment->id);
        $consultantDoctor = $this->ipdPatientDepartmentRepository->getConsultantDoctor($ipdPatientDepartment->id);
        $ipdTimeline = $this->ipdPatientDepartmentRepository->getIPDTimeline($ipdPatientDepartment->id);
        $ipdPrescriptions = $this->ipdPatientDepartmentRepository->getIPDPrescription($ipdPatientDepartment->id);
        $ipdCharges = $this->ipdPatientDepartmentRepository->getIPDCharges($ipdPatientDepartment->id);
        $ipdPayment = $this->ipdPatientDepartmentRepository->getIPDPayment($ipdPatientDepartment->id);
        $ipdDiagnosis = $this->ipdPatientDepartmentRepository->getIPDDiagnosis($ipdPatientDepartment->id);
        $ipdOperation = $this->ipdPatientDepartmentRepository->getIPDOperation($ipdPatientDepartment->id);
        $mealList = $this->ipdPatientDepartmentRepository->getMealList();

        $doctorsList = $this->ipdPatientDepartmentRepository->getDoctorsList();
        $operationCategory = $this->ipdPatientDepartmentRepository->getOperationCategoryList();
        $medicineCategories = $this->ipdPatientDepartmentRepository->getMedicinesCategoriesData();
        $medicineCategoriesList = $this->ipdPatientDepartmentRepository->getMedicineCategoriesList();
        $doseDurationList = $this->ipdPatientDepartmentRepository->getDoseDurationList();
        $doseIntervalList = $this->ipdPatientDepartmentRepository->getDoseIntervalList();
        $ipdPatientDepartmentRepository = App::make(IpdBillRepository::class);
        $bill = $ipdPatientDepartmentRepository->getBillList($ipdPatientDepartment);
        $chargeTypes = IpdCharge::CHARGE_TYPES;
        $paymentModes = IpdPayment::PAYMENT_MODES;

        return view('ipd_patient_departments.show',
            compact('ipdPatientDepartment', 'doctors', 'doctorsList', 'chargeTypes', 'medicineCategories',
                'medicineCategoriesList', 'paymentModes', 'bill', 'consultantRegister', 'ipdTimeline', 'ipdPrescriptions', 'ipdCharges', 'ipdPayment', 'ipdDiagnosis', 'operationCategory', 'consultantDoctor', 'ipdOperation', 'doseDurationList', 'doseIntervalList', 'mealList'));
    }

    public function edit(IpdPatientDepartment $ipdPatientDepartment)
    {
        $data = $this->ipdPatientDepartmentRepository->getAssociatedData();

        return view('ipd_patient_departments.edit', compact('data', 'ipdPatientDepartment'));
    }

    public function update(IpdPatientDepartment $ipdPatientDepartment, UpdateIpdPatientDepartmentRequest $request)
    {
        $input = $request->all();
        $this->ipdPatientDepartmentRepository->updateIpdPatientDepartment($input, $ipdPatientDepartment);
        Flash::success(__('messages.ipd_patient.ipd_patient').' '.__('messages.common.updated_successfully'));

        return redirect(route('ipd.patient.index'));
    }

    public function destroy(IpdPatientDepartment $ipdPatientDepartment)
    {
        $this->ipdPatientDepartmentRepository->deleteIpdPatientDepartment($ipdPatientDepartment);

        return $this->sendSuccess(__('messages.ipd_patient.ipd_patient').' '.__('messages.common.deleted_successfully'));
    }

    public function getPatientCasesList(Request $request)
    {
        $patientCases = $this->ipdPatientDepartmentRepository->getPatientCases($request->get('id'));

        return $this->sendResponse($patientCases, 'Retrieved successfully');
    }

    public function getPatientBedsList(Request $request)
    {
        $patientBeds = $this->ipdPatientDepartmentRepository->getPatientBeds($request->get('id'),
            $request->get('isEdit'), $request->get('bedId'), $request->get('ipdPatientBedTypeId'));

        return $this->sendResponse($patientBeds, 'Retrieved successfully');
    }
}
