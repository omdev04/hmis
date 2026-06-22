<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateIpdPaymentRequest;
use App\Http\Requests\UpdateIpdPaymentRequest;
use App\Models\IpdPayment;
use App\Queries\IpdPaymentDataTable;
use App\Repositories\IpdPaymentRepository;
use DataTables;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class IpdPaymentController extends AppBaseController
{
    /** @var IpdPaymentRepository */
    private $ipdPaymentRepository;

    public function __construct(IpdPaymentRepository $ipdPaymentRepo)
    {
        $this->ipdPaymentRepository = $ipdPaymentRepo;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new IpdPaymentDataTable())->get($request->id))->make(true);
        }
    }

    public function store(CreateIpdPaymentRequest $request)
    {
        $input = $request->all();
    
        $this->ipdPaymentRepository->store($input);

        return $this->sendSuccess(__('messages.ipd_payment').' '.__('messages.common.saved_successfully'));
    }

    public function edit(IpdPayment $ipdPayment)
    {
        return $this->sendResponse($ipdPayment, 'IPD Payment retrieved successfully.');
    }

    public function update(IpdPayment $ipdPayment, UpdateIpdPaymentRequest $request)
    {
        $this->ipdPaymentRepository->updateIpdPayment($request->all(), $ipdPayment->id);

        return $this->sendSuccess(__('messages.ipd_payment').' '.__('messages.common.updated_successfully'));
    }

    public function destroy(IpdPayment $ipdPayment)
    {
        $this->ipdPaymentRepository->deleteIpdPayment($ipdPayment->id);

        return $this->sendSuccess(__('messages.ipd_payment').' '.__('messages.common.deleted_successfully'));
    }

    public function downloadMedia(IpdPayment $ipdPayment)
    {
        $media = $ipdPayment->getMedia(IpdPayment::IPD_PAYMENT_PATH)->first();
        if ($media != null) {
            $media = $media->id;
            $mediaItem = Media::findOrFail($media);

            return $mediaItem;
        }

        return '';
    }
}
