<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
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
        // $file = Storage::size('app/public/MATRICES.pdf');
        // $file = File::get('/storage/app/public/MATRICES.pdf');
        // dd($file);
        $file = Storage::url('app/public/MATRICES.pdf');
        $file = Storage::path('public/MATRICES.pdf');
        $pdf = $parser->parseFile($file);
        $text = $pdf->getText();
        dd( $text );

        return Inertia::render('leyendopdf/Index', [ //carpeta
            'breadcrumbs'       =>  [['label' => __('app.label.leyendopdf'), 'href' => route('leyendopdf')]],
            'title'             =>  'Leyendo PDF',
        ]);
    } 
    public function verPdf($archivoid) {

        $archivo = Archivo::find($archivoid);
        // $parser = new Parser();
        // $parser = new \Smalot\PdfParser\Parser();
        // $file = Storage::url('app/public/MATRICES.pdf');
        $file = public_path('storage/archivosSubidos/'.$archivo->NombreOriginal);
        // $pdf = $parser->parseFile($file);

        $pathToFile = $file;
        // $text = $pdf->getText();

        $headers = ['Content-Type' => 'application/pdf'];
        // pdfview.vue
        return response()->file($pathToFile, $headers);
    } 


    public function vistaPDF($archivoid) {
        $parser = new Parser();
        $archivo = Archivo::find($archivoid);

        // $file = Storage::path('public/MATRICES.pdf');
        $file = public_path('storage/archivosSubidos/'.$archivo->NombreOriginal);

        $pdf = $parser->parseFile($file);
        $text = $pdf->getText();
        // dd( $text );
        $resumen = substr($text,0,1000);
        return Inertia::render('materia/docs/pdfview', [ //carpeta
            'resumen'           =>  $resumen,
            'archivinid'       =>  $archivoid,
        ]);
    } 
}
