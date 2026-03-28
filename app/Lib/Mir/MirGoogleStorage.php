<?php namespace App\Lib\Mir;

use Illuminate\Database\Eloquent\Model;
use App\Lib\CityUtil;
use Google\Cloud\Storage\StorageClient;

class MirGoogleStorage {

    //
    public static function store($abs_file_name, $gs_bucket, $object_name)
    {
        // 認証
        $service_account_json_file = storage_path(
            config('_env.GOOGLE_SERVICE_ACCOUNT_JSON_STORAGE_PATH')
        );
        putenv("GOOGLE_APPLICATION_CREDENTIALS={$service_account_json_file}");
        //logger("MirGoogleStorage::store {$service_account_json_file}");

        $client = new StorageClient();
        $bucket = $client->bucket($gs_bucket);

        $bucket->upload(
            fopen($abs_file_name, 'r'),
            [
                'name' => $object_name
            ]
        );

        logger()->info(">> {$gs_bucket}/{$object_name}");
    }

    public static function delete($gs_bucket, $object_name)
    {
        $service_account_json_file = storage_path(
            config('_env.GOOGLE_SERVICE_ACCOUNT_JSON_STORAGE_PATH')
        );
        putenv("GOOGLE_APPLICATION_CREDENTIALS={$service_account_json_file}");
        //logger("MirGoogleStorage::delete {$service_account_json_file}");

        $client = new StorageClient();
        $bucket = $client->bucket($gs_bucket);
        $object = $bucket->object($object_name);

        $object->delete();

        //logger("[GS] deleted {$gs_bucket}/{$object_name}");
    }

}
