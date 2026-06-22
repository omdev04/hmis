<?php

namespace App\Repositories;

use App\Models\FrontService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class FrontServiceRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'short_description',
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return FrontService::class;
    }

    public function store($input)
    {
        try {
            $frontService = FrontService::create($input);

            if (isset($input['icon']) && ! empty($input['icon'])) {
                $frontService->addMedia($input['icon'])->toMediaCollection(FrontService::PATH,
                    config('app.media_disc'));
            }

            return true;
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function updateFrontService($input, $frontServiceId)
    {
        try {
            $frontService = $this->update($input, $frontServiceId);

            if (isset($input['icon']) && ! empty($input['icon'])) {
                $frontService->clearMediaCollection(FrontService::PATH);
                $frontService->addMedia($input['icon'])->toMediaCollection(FrontService::PATH,
                    config('app.media_disc'));
            }
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
