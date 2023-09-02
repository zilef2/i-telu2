<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Smalot\PdfParser\Parser;

class TemporalPdfReader extends Controller
{
    
    public function Index() {

        return Inertia::render('leyendopdf/Index', [ //carpeta
            'breadcrumbs'       =>  [['label' => __('app.label.leyendopdf'), 'href' => route('leyendopdf')]],
            'title'             =>  'Leyendo PDF',
        ]);
    } 
    public function Read(Request $request) {

        $parser = new Parser();
        // $parser = new \Smalot\PdfParser\Parser();
        $file = Storage::url('app/public/MATRICES.pdf');
        $file = Storage::path('public/MATRICES.pdf');
        // $file = Storage::size('app/public/MATRICES.pdf');
        // $file = File::get('/storage/app/public/MATRICES.pdf');
        // dd($file);
        $pdf = $parser->parseFile($file);

        $text = $pdf->getText();
        dd(
             $text
    );

        return Inertia::render('leyendopdf/Index', [ //carpeta
            'breadcrumbs'       =>  [['label' => __('app.label.leyendopdf'), 'href' => route('leyendopdf')]],
            'title'             =>  'Leyendo PDF',
        ]);
    } 
}
