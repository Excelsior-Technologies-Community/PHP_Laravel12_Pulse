<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;


class PulseExportController extends Controller
{


    public function export()
    {

        $data = DB::table('pulse_entries')->get();


        $file = fopen(
            storage_path('pulse.csv'),
            'w'
        );


        foreach ($data as $row) {

            fputcsv(
                $file,
                (array)$row
            );
        }


        fclose($file);


        return response()->download(
            storage_path('pulse.csv')
        );
    }
}
