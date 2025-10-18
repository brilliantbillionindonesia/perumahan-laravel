<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Constants\BloodTypeOption;
use App\Constants\CitizenshipOption;
use App\Constants\EducationTypeOption;
use App\Constants\GenderOption;
use App\Constants\MaritalStatusOption;
use App\Constants\RelationshipStatusOption;
use App\Constants\ReligionOption;
use App\Constants\WorkTypeOption;
use App\Models\ComplaintCategory;
use App\Models\ComplaintStatus;

class OptionController extends Controller
{

    protected $options = [
        'blood-type' => BloodTypeOption::class,
        'gender' => GenderOption::class,
        'religion' => ReligionOption::class,
        'citizenship' => CitizenshipOption::class,
        'education_type' => EducationTypeOption::class,
        'marital_status' => MaritalStatusOption::class,
        'relationship_status' => RelationshipStatusOption::class,
        'work_type' => WorkTypeOption::class,
    ];

    public function index()
    {
        // List semua constant yg tersedia
        return response()->json([
            'success' => true,
            'data' => array_keys($this->options),
        ]);
    }

    public function show($constant)
    {
        if (!isset($this->options[$constant])) {
            return response()->json([
                'success' => false,
                'message' => 'Option not found'
            ], 404);
        }

        $class = $this->options[$constant];

        // Ambil semua constant dalam bentuk value saja
        $refClass = new \ReflectionClass($class);
        $values   = array_values($refClass->getConstants());

        return response()->json([
            'success' => true,
            'data' => $values
        ]);
    }
}
