<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\languag;
class HelperLanguage extends Model
{
    public static function retrieve_message_from_arb_file($code,$object){
        
        try {
            $cache_key = 'arb_data_' . $code;
            if (Cache::has($cache_key)) {
                $data = Cache::get($cache_key);
                $desiredValue = isset($data[$object]) && !empty($data[$object]) ? $data[$object] :null;
                return $desiredValue;
            }

            $languages = languag::where('code', $code)->first();
            if(isset($languages->arb_url) && !empty($languages->arb_url) ){
                // $jsonString = file_get_contents($languages->arb_file_url);
                // $jsonString = self::language_arb_url_call('http://127.0.0.1:8000/storage/arb/app_en.arb');
                $jsonString = Storage::disk('public')->get($languages->arb_url);
                $data = json_decode($jsonString, true);

                Cache::forever($cache_key, $data);
                // Decode the JSON string into a PHP associative array
                $data = json_decode($jsonString, true);
              
                // Access the particular value you're interested in
                $desiredValue = isset($data[$object]) && !empty($data[$object]) ? $data[$object] : '';
                return $desiredValue;
            } else {
                return;
            }  
        } catch (Exception $e) {
            return '';
        } 
       
    }

    public static function language_arb_url_call($url){
        try {
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        if (curl_errno($ch)) {
            throw new Exception("Unable to retrieve arb file");
        }
      
        } catch (Exception $e) {
            return '';
        } finally {
            curl_close($curl);
            return $response; 
        }

    }
}
