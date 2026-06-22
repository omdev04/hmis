<?php

namespace App\Repositories;

use App\Models\ChargeCategory;
use App\Models\PathologyCategory;
use App\Models\PathologyTest;

/**
 * Class PathologyTestRepository
 *
 * @version April 14, 2020, 9:33 am UTC
 */
class PathologyTestRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'test_name',
        'short_name',
        'test_type',
        'category_id',
        'unit',
        'subcategory',
        'method',
        'report_days',
        'charge_category_id',
        'standard_charge',
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return PathologyTest::class;
    }

    public function getPathologyAssociatedData()
    {
        $data['pathologyCategories'] = PathologyCategory::all()->pluck('name', 'id');
        $data['chargeCategories'] = ChargeCategory::all()->pluck('name', 'id');

        return $data;
    }
}
