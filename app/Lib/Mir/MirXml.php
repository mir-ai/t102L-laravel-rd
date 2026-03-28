<?php namespace App\Lib\Mir;

use SimpleXMLElement;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class MirXml {

    /**
     * XMLファイルをフォーマットする
     *
     * @param string $content
     * @return string
     */
    public static function formatXml(string $xml_content): string
    {
        try {
            // XMLを機械が読みやすいように整形する。
            // １行に最大１つのノードにする

            // 改行を全部消す。
            $xml_content = str_replace(["\n", "\r"], "", $xml_content);

            // > <間のスペースを除去する。
            $xml_content = preg_replace('/>[\s]+</', '><', $xml_content);

            // そうすると、きれいにインデントされた、１行１ノードのXMLに整形してくれる。
            $simpleXmlElement = new SimpleXMLElement($xml_content);
            $dom = dom_import_simplexml($simpleXmlElement)->ownerDocument;
            $dom->formatOutput = true;
            $content = $dom->saveXML();
            return $content;
        } catch (\Exception $e) {
            logger("formatXML {$e}");
            return '';
        }
    }

    public static function xml_to_array($string)
    {
        /*
        $string = <<<END
<?xml version='1.0' encoding='UTF-8' ?>
<mail>
<delivery id='0'>
    <deliver_id>00006d</deliver_id>
    <sent_list>
    <data id='00006d-00000'>
    <sent_date>2022/02/23 14:31</sent_date>
    <to_addr><![CDATA[111@mir-ai.net]]></to_addr>
    <from_addr><![CDATA[sender@mail.a-alert.net]]></from_addr>
    <key_field><![CDATA[127]]></key_field>
    <status>0</status>
    <message code='250'><![CDATA[250 Sent]]></message>
    </data>
    <data id='00006d-00001'>
    <sent_date>2022/02/23 14:31</sent_date>
    <to_addr><![CDATA[222@mir-ai.net]]></to_addr>
    <from_addr><![CDATA[sender@mail.a-alert.net]]></from_addr>
    <key_field><![CDATA[128]]></key_field>
    <status>0</status>
    <message code='250'><![CDATA[250 Sent]]></message>
    </data>
    <data id='00006d-00002'>
    <sent_date>2022/02/23 14:31</sent_date>
    <to_addr><![CDATA[333@mir-ai.net]]></to_addr>
    <from_addr><![CDATA[sender@mail.a-alert.net]]></from_addr>
    <key_field><![CDATA[129]]></key_field>
    <status>0</status>
    <message code='250'><![CDATA[250 Sent]]></message>
    </data>
    <data id='00006d-00003'>
    <sent_date>2022/02/23 14:31</sent_date>
    <to_addr><![CDATA[444@mir-ai.net]]></to_addr>
    <from_addr><![CDATA[sender@mail.a-alert.net]]></from_addr>
    <key_field><![CDATA[130]]></key_field>
    <status>0</status>
    <message code='250'><![CDATA[250 Sent]]></message>
    </data>
    </sent_list>
    <result code='0'>処理が正常に終了しました</result>
</delivery>
</mail>
END;       
        echo($string);

        $string = <<<END
<?xml version='1.0' encoding='UTF-8' ?>
<mail>
    <delivery id='0'>
    <deliver_id>00006u</deliver_id>
    <request_id>6215e6509c57e</request_id>
    <exec_cnt>2</exec_cnt>
    <result code='0'>処理が正常に終了しました</result>
    </delivery>
</mail>
END;       

$string = <<<END
<?xml version='1.0' encoding='UTF-8' ?>
<mail>
 <delivery id='0'>
  <deliver_id>000088</deliver_id>
  <sent_list>
   <data id='000088-00000'>
    <sent_date>2022/02/23 21:19</sent_date>
    <to_addr><![CDATA[mms01@mir-ai.net]]></to_addr>
    <from_addr><![CDATA[sender@mail.a-alert.net]]></from_addr>
    <key_field><![CDATA[3765]]></key_field>
    <status>0</status>
    <message code='250'><![CDATA[250 Sent]]></message>
   </data>
   <data id='000088-00001'>
    <sent_date>2022/02/23 21:19</sent_date>
    <to_addr><![CDATA[mms02@mir-ai.net]]></to_addr>
    <from_addr><![CDATA[sender@mail.a-alert.net]]></from_addr>
    <key_field><![CDATA[3766]]></key_field>
    <status>0</status>
    <message code='250'><![CDATA[250 Sent]]></message>
   </data>
   <data id='000088-00002'>
    <sent_date>2022/02/23 21:19</sent_date>
    <to_addr><![CDATA[mms03@mir-ai.net]]></to_addr>
    <from_addr><![CDATA[sender@mail.a-alert.net]]></from_addr>
    <key_field><![CDATA[3767]]></key_field>
    <status>0</status>
    <message code='250'><![CDATA[250 Sent]]></message>
   </data>
   <data id='000088-00003'>
    <sent_date>2022/02/23 21:19</sent_date>
    <to_addr><![CDATA[mms04@mir-ai.net]]></to_addr>
    <from_addr><![CDATA[sender@mail.a-alert.net]]></from_addr>
    <key_field><![CDATA[3768]]></key_field>
    <status>0</status>
    <message code='250'><![CDATA[250 Sent]]></message>
   </data>
   <data id='000088-00004'>
    <sent_date>2022/02/23 21:19</sent_date>
    <to_addr><![CDATA[mms05@mir-ai.net]]></to_addr>
    <from_addr><![CDATA[sender@mail.a-alert.net]]></from_addr>
    <key_field><![CDATA[3769]]></key_field>
    <status>0</status>
    <message code='250'><![CDATA[250 Sent]]></message>
   </data>
   <data id='000088-00005'>
    <sent_date>2022/02/23 21:19</sent_date>
    <to_addr><![CDATA[mms06@mir-ai.net]]></to_addr>
    <from_addr><![CDATA[sender@mail.a-alert.net]]></from_addr>
    <key_field><![CDATA[3770]]></key_field>
    <status>0</status>
    <message code='250'><![CDATA[250 Sent]]></message>
   </data>
   <data id='000088-00006'>
    <sent_date>2022/02/23 21:19</sent_date>
    <to_addr><![CDATA[mms07@mir-ai.net]]></to_addr>
    <from_addr><![CDATA[sender@mail.a-alert.net]]></from_addr>
    <key_field><![CDATA[3771]]></key_field>
    <status>0</status>
    <message code='250'><![CDATA[250 Sent]]></message>
   </data>
  </sent_list>
  <result code='0'>処理が正常に終了しました</result>
 </delivery>
</mail>
END;       
        */

        if (! Str::startsWith($string, '<?xml ')) {
            return null;
        }

        $xml = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);

/*

$sent_list = $array['delivery']['sent_list']['data'];

foreach ($sent_list as $i => $item) {
    $delivery_id = $item['key_field'] ?? '';
    logger("delivery_id $delivery_id");
}

$arara_deliver_id1 = $array['delivery']['deliver_id'] ?? '';
$arara_deliver_id2 = $array['deliver_id'] ?? '';

logger("arara_deliver_id1 $arara_deliver_id1");
logger("arara_deliver_id2 $arara_deliver_id2");
*/



        return $array;
    }
}
