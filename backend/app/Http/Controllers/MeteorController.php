<?php

namespace App\Http\Controllers;

use App\Services\NasaService;
use Illuminate\Http\Request;

class MeteorController extends Controller
{
    private $nasaService;

    /**
     * Constructor
     * 
     * @param NasaService $nasaService
     */
    public function __construct(NasaService $nasaService)
    {
        $this->nasaService = $nasaService;
    }

    /**
     * Method
     * 
     * @return json
     */
    public function index()
    {
        $response = $this->nasaService->getMeteor(
            request()->get('startDate'),
            request()->get('endDate'),
            request()->get('page'),
            request()->get('search')
        );
        return response()->json($response);
    }
}
