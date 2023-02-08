<?php

namespace App\Http\Controllers;

use App\Models\PeriodsSoap;
use App\Models\SoapModel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
   

    public function dashboard(Request $request) 
    {        
        $year = $request->session()->get('year', date('Y'));
        $model = new PeriodsSoap('http://86000-app012:8055/WSDLServices.nsf/vacation?WSDL', [], PeriodsSoap::class . $year);        
        return view('dashboard',
            array_merge($model->getPeriods($year)->getStatistics($year), [
                'year' => $year,
                'years' => $this->getYears(),
            ])            
        );
    }


    public function schedule(Request $request)
    {
        $year = $request->session()->get('year', date('Y'));
        $model = new PeriodsSoap('http://86000-app012:8055/WSDLServices.nsf/vacation?WSDL', [], PeriodsSoap::class . $year);
        
        return view('schedule', [
            'data' => $model->getPeriods($year)->getDataAsArray(),
            'calendar' => $model->generateCalendar($year),
            'typesVacation' => $model->getTypesVacation(),   
            'year' => $year,
            'years' => $this->getYears(),         
        ]);
    }

    public function setYear(Request $request)
    {
        $year = $request->get('year');
        $ref = $request->get('ref', '/');
        if (!$year) {
            throw new BadRequestException('Param year is empty!');
        }
        session(['year' => $year]);
        return redirect($ref);
    }

    protected function getYears()
    {
        $model = new SoapModel('http://86000-app012:8055/WSDLServices.nsf/vacation?WSDL');
        $data = $model->Years()->getDataAsArray();
        return $data['item'] ?? $data;
    }

}
