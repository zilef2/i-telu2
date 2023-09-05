<?php
namespace App\helpers;

class ModelFunctions {

    public static function destroyBulkHelper($model,$ids) {
        
        try {
            $modelInstance = resolve('App\\Models\\' . $model);
            $borrando = $modelInstance::whereIn('id', $ids);
            $borrando->delete();
            return [
                'success',
                count($ids) . ' registros han sido borrados'
            ];

        } catch (\Throwable $th) {
            return [
                'error',
                'Error. '.$th->getMessage(). ' L: '.$th->getLine()
            ];
        }
    }
}