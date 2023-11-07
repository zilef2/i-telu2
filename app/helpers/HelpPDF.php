<?php

namespace App\helpers;

use App\Models\Archivo;
use Smalot\PdfParser\Parser;

class HelpPDF {

//usado en temporalPdfReader y en GuardarResumirArchivoPDF: para procesar un archivo
    public static function ParserPDF($archivo){
        $parser = new Parser();

        if(isset($archivo->NombreOriginal)){
            $file = storage_path('app/public/archivosSubidos/'.$archivo->NombreOriginal);
        }else{
            $file = $archivo;
        }
        return $parser->parseFile($file)->getText();
    }

    public function AproximarUsoDeTokens($numeroPalabras){

        return ceil(($numeroPalabras / 100));
    }
    public static function TextTOJsonl($text){
        // Step 1: Read the plain-text document
        // $text = file_get_contents('plain-text-document.txt');

        // Step 2: Convert to JSONL format
        $lines = explode("\n", $text);

        $jsonlContent = '';
        foreach ($lines as $line) {
            // Assuming each line represents a separate data point
            // You might need to format this line according to your specific use case
            $jsonlContent .= json_encode(['text' => $line]) . "\n";
        }

        // Step 3: Write to a file
        file_put_contents('my-file1.jsonl', $jsonlContent);
    }
}
