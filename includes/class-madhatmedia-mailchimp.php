<?php

class Madhatmedia_Crm_MailChimp {

    public $apiKey;
    public $listID;
    private $dataCenter;
    private $url;
    private $json;

    public function __construct( $apiKey ) {

        $this->apiKey = $apiKey;
        $this->setDataCenter();

    }

    private function setDataCenter() {

        $dataCenter = substr($this->apiKey,strpos($this->apiKey,'-')+1);

        $this->dataCenter = $dataCenter;

    }

    public function subscriptionStatus( $listID, $email ) {

        $memberID = md5( strtolower( $email ) );

        $url = 'https://' . $this->dataCenter . '.api.mailchimp.com/3.0/lists/' . $listID . '/members/' . $memberID;

        return $this->postRequest( 'GET', json_encode(array()), $url );

    }

    public function getListDetails( $listID ) {

        $url = 'https://' . $this->dataCenter . '.api.mailchimp.com/3.0/lists/' . $listID;

        return $this->postRequest( 'GET', json_encode(array()), $url );

    }

    public function subscribeToCampaign( $listID, $email, $fname, $lname ) {

        $status = json_decode( $this->subscriptionStatus( $listID, $email ) );

        if ( $status->status == 'unsubscribed' || ( $status->status == 400 && $status->title == 'Member Exists' ) ) {

            $memberID = md5( strtolower( $email ) );
            $url = 'https://' . $this->dataCenter . '.api.mailchimp.com/3.0/lists/' . $listID . '/members/' . $memberID;

            $json = json_encode(array(
                'apikey'        => $this->apiKey,
                'email_address' => $email,
                'status'        => 'subscribed'
            ));

            return $this->postRequest( 'PUT', $json, $url );

        } else {

            $memberID = md5( strtolower( $email ) );
            $url = 'https://' . $this->dataCenter . '.api.mailchimp.com/3.0/lists/' . $listID . '/members/';

            $json = json_encode(array(
                'apikey'        => $this->apiKey,
                'email_address' => $email,
                'status'        => 'subscribed',
                'merge_fields'  => array(
                    'FNAME' => $fname,
                    'LNAME' => $lname,
                    'NAME'  => $fname . ' ' . $lname
                )
            ));

            return $this->postRequestV2( 'POST', $json, $url );

        }

    }

    public function unsubscribeToCampaign( $listID, $email ) {

        $memberID = md5( strtolower( $email ) );
        $url = 'https://' . $this->dataCenter . '.api.mailchimp.com/3.0/lists/' . $listID . '/members/' . $memberID;

        $json = json_encode(array(
            'apikey'        => $this->apiKey,
            'email_address' => $email,
            'status'        => 'unsubscribed'
        ));

        return $this->postRequest( 'PATCH', $json, $url );

    }

    public function postRequestV2( $request, $json, $url ) {

        $auth = base64_encode( 'user:'.$this->apiKey );


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                                                        'Authorization: Basic '.$auth));
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/3.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        $result = curl_exec($ch);

        return $result;

    }

    public function getLists() {

        $url = 'https://' . $this->dataCenter . '.api.mailchimp.com/3.0/lists?count=100';

        return $this->postRequest( 'GET', json_encode(array()), $url );

    }

    private function postRequest( $request, $json, $url ) {

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $this->apiKey);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200) {
            return $result;
        } else {
            return $result;
        }

    }

}