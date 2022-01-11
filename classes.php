<?php

/**
 * @param $token "API Access Token"
 */
class APIRequest{
    private $token;

    public function __construct($token)
    {
        $this->token = $token;
    }
    /**
     * @param $method "POST/GET/PUT"
     * @param $url "endpoint after http://snipeit/api/v1/"
     */
    public function CallAPI($method, $url){

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "http://snipeit.riokrakow.local/api/v1/".$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Authorization: Bearer ".$this->token,
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }

    }
}

class DocumentSnipeit{
    public $type;
    public $item;
    public $person;
    public $html;
    public $filename;

    public function __construct($type, $person){
        if ($type) {
            $this->filename = "po";
            $this->type = [
                'header' => 'odbioru',
                'body' => 'odbiór',
                'sig' => 'odbierającej',
                'sig2' => 'wydającej'
            ];
        }else{
            $this->filename = "pz";
            $this->type = [
                'header' => 'zwrotu',
                'body' => 'zwrot',
                'sig' => 'zwracającej',
                'sig2' => 'przyjmującej'
            ];
        }

        $this->item = array();

        $this->person = [
            'name' => $person['name'],
            'title' => $person['jobtitle'],
            'department' => $person['department']['name']
        ];
    }

    public function addItem($item){
        $asset = [
            'name' => $item['name'],
            'model' => $item['model']['name'],
            'tag' => $item['asset_tag'],
            'serial' => $item['serial'],
            'category' => $item['category']['name']
        ];

        array_push($this->item, $asset);       
    }

    private function HTMLItems(){
        $data = "<ul>";
        foreach ($this->item as $key => $value) {
            $data .= <<<EOT
            <li>{$value['category']} {$value['model']}</li>
            <ul>
                <li><b>Numer inwentarzowy:</b> {$value['tag']}</li>
EOT;
            if ($value['serial'] != ''){
                $data .= "<li><b>Numer seryjny:</b> ".$value['serial']."</li>";
            }
            $data .= <<<EOT
            </ul>
            <br>
EOT;
        }
        return $data."</ul>";
    }

    private function encode($data){
        return str_replace(['\\','/'],'-', $data);
    }

    public function prepare(){
        $date = date("d.m.Y");
        $html = <<<EOT
        <p style="text-align: center"><strong>Potwierdzenie {$this->type['header']} z dnia $date</strong></p>
        <br>
        <p style="line-height: 1.5;">&emsp;Ja, niżej podpisany/a {$this->person['name']}, {$this->person['title']} Regionalnej Izby Obrachunkowej w Krakowie potwierdzam {$this->type['body']} niżej wymienionego sprzętu:</p>
        <p style="line-height: 1.5;">
        {$this->HTMLItems()}
        </p>
EOT;
        if ($this->type['body'] == 'odbiór'){
            $html .= <<<EOT
            <p style="line-height: 1.5;">Jednocześnie potwierdzam przyjęcie na siebie odpowiedzialności za ww. sprzęt.</p>
EOT;
        }
        $html .= <<<EOT
        <table style="margin-top:100px; width: 100%; border-collapse: collapse; border: none rgb(0, 0, 0);">
            <tbody>
                <tr style="margin-top:100px;">
                        <td style="width: 50%; border: none rgb(0, 0, 0);" align="center">
                        <p>........................................</p>
                        <p>Data i podpis osoby <br>{$this->type['sig']}</p>
                    </td>
                    <td style="width: 50%; border: none rgb(0, 0, 0);" align="center">
                        <p>........................................</p>
                        <p>Podpis i pieczątka <br>osoby {$this->type['sig2']}</p>
                    </td>
                </tr>
            </tbody>
        </table>
EOT;
        $this->html = $html;
        $this->filename .= "_".date("Y.m.d")."_".$this->person['name']."_".$this->encode($this->item[0]['model'])."_".$this->encode($this->item[0]['tag']).".pdf";
    }
}
?>