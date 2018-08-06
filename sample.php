<?php

class HttpGeocoder
{
    
    var $request_url = "https://maps.googleapis.com/maps/api/geocode";

    var $api_key = null; //

    var $type_select = array("json", "xml");

    function getValue($address, $type = 'json', $sensor = false)
    {

        //apiキー または 検索する住所が未設定ならば、処理を中断
        if (empty($this->api_key) || empty($address)) {
            return false;
        }

        //出力タイプは json, xml のいずれか
        //それ以外ならば デフォルトの json にする
        if (array_search($type, $this->type_select) === false) {
            $type = "json";
        }

        $url = $this->request_url;
        $url .= "/" . $type;
        $url .= "?address=" . urlencode($address);
        $url .= "&key=" . $this->api_key;
        $url .= "&language=ja";

        // cURLセッションを初期化
        $ch = curl_init();

        // オプションを設定
        curl_setopt_array($ch, [
            CURLOPT_URL => $url, // 取得するURLを指定
            CURLOPT_RETURNTRANSFER => true, // 実行結果を文字列で返す
            CURLOPT_FAILONERROR => true,
            CURLOPT_SSL_VERIFYPEER => true, // サーバー証明書の検証を行わない
        ]);
        
        // URLの情報を取得
        $body = curl_exec($ch);
        $info = curl_getinfo($ch);
    
        $errno = curl_errno($ch);
        $error = curl_error($ch);

        // セッションを終了
        curl_close($ch);

        if (CURLE_OK !== $errno) {
            throw new RuntimeException($error, $errno);
        }
    
        return [$body, $info];
    }
}


$geo = new HttpGeocoder();

$geo->api_key = ''; //取得したAPI KEYを設定する

$address = '';

$googleMapsApiData = json_decode($geo->getValue($address, "json")[0], true);

// 緯度経度を取得
$place_id          = $googleMapsApiData["results"][0]["place_id"];
$status            = $googleMapsApiData["status"];
$lat               = $googleMapsApiData["results"][0]["geometry"]["location"]["lat"];
$lng               = $googleMapsApiData["results"][0]["geometry"]["location"]["lng"];
$location_type     = $googleMapsApiData["results"][0]["geometry"]["location_type"];
$formatted_address = $googleMapsApiData["results"][0]["formatted_address"];

foreach($googleMapsApiData["results"][0]["address_components"] as $key => $value) {
    $search_result = null;
    if (($search_result = array_search("postal_code", $value["types"])) !== false) {
        $postal_code = $value["long_name"];
    } else if (($search_result = array_search("country", $value["types"])) !== false) {
        $country = $value["long_name"];
    } else if (($search_result = array_search("administrative_area_level_1", $value["types"])) !== false) {
        $administrative_area_level_1 = $value["long_name"];
    } else if (($search_result = array_search("administrative_area_level_2", $value["types"])) !== false) {
        $administrative_area_level_2 = $value["long_name"];
    } else if (($search_result = array_search("colloquial_area", $value["types"])) !== false) {
        $colloquial_area = $value["long_name"];
    } else if (($search_result = array_search("locality", $value["types"])) !== false) {
        $locality = $value["long_name"];
    } else if (($search_result = array_search("ward", $value["types"])) !== false) {
        $ward = $value["long_name"];
    } else if (($search_result = array_search("sublocality_level_1", $value["types"])) !== false) {
        $sublocality_level_1 = $value["long_name"];
    } else if (($search_result = array_search("sublocality_level_2", $value["types"])) !== false) {
        $sublocality_level_2 = $value["long_name"];
    } else if (($search_result = array_search("sublocality_level_3", $value["types"])) !== false) {
        $sublocality_level_3 = $value["long_name"];
    } else if (($search_result = array_search("sublocality_level_4", $value["types"])) !== false) {
        $sublocality_level_4 = $value["long_name"];
    } else if (($search_result = array_search("sublocality_level_5", $value["types"])) !== false) {
        $sublocality_level_5 = $value["long_name"];
    } else if (($search_result = array_search("premise", $value["types"])) !== false) {
        $premise = $value["long_name"];
    }
}

?>

<table>
<thead>
<tr>
<th>place_id</th>
<th>lat</th>
<th>lng</th>
<th>location_type</th>
<th>formatted_address</th>
<th>postal_code</th>
<th>country</th>
<th>administrative_area_level_1</th>
<th>administrative_area_level_2</th>
<th>colloquial_area</th>
<th>locality</th>
<th>ward</th>
<th>sublocality_level_1</th>
<th>sublocality_level_2</th>
<th>sublocality_level_3</th>
<th>sublocality_level_4</th>
<th>sublocality_level_5</th>
<th>premise</th>
</tr>
</thead>
<tbody>

<tr>
<td><?php echo($place_id ?? ''); ?></td>
<td><?php echo($lat ?? ''); ?></td>
<td><?php echo($lng ?? ''); ?></td>
<td><?php echo($location_type ?? ''); ?></td>
<td><?php echo($formatted_address ?? ''); ?></td>
<td><?php echo($postal_code ?? ''); ?></td>
<td><?php echo($country ?? ''); ?></td>
<td><?php echo($administrative_area_level_1 ?? ''); ?></td>
<td><?php echo($administrative_area_level_2 ?? ''); ?></td>
<td><?php echo($colloquial_area ?? ''); ?></td>
<td><?php echo($locality ?? ''); ?></td>
<td><?php echo($ward ?? ''); ?></td>
<td><?php echo($sublocality_level_1 ?? ''); ?></td>
<td><?php echo($sublocality_level_2 ?? ''); ?></td>
<td><?php echo($sublocality_level_3 ?? ''); ?></td>
<td><?php echo($sublocality_level_4 ?? ''); ?></td>
<td><?php echo($sublocality_level_5 ?? ''); ?></td>
<td><?php echo($premise ?? ''); ?></td>
</tr>

</tbody>
</table>
