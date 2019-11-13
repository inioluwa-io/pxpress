<?php
    interface IApiResponse {
        public function send($data);
        public function status($code);
    }
    class Response implements IApiResponse{
        private $code;
        private $supportedStatusCode = array('404', '200', '405', '400');
        private $access = TRUE;

        function __construct() {
            $this->code = '';
            $this->bootstrapSelf();
        }
        private function bootstrapSelf() {
          foreach($_SERVER as $key => $value)
          {
           $this->{$this->toCamelCase($key)} = $value;
          }
        }
  
        private function toCamelCase($string) {
          $result = strtolower($string);
              
          preg_match_all('/_[a-z]/', $result, $matches);
          foreach($matches[0] as $match)
          {
              $c = str_replace('_', '', strtoupper($match));
              $result = str_replace($match, $c, $result);
          }
          return $result;
        }
  

        function displayStatusCodeMessage($code) {
            $supportedStatusCode = array('404'=> 'Not Found', '200' => 'Ok', '405' => 'Method Not Allowed', '400'=> 'Bad request');
            return $supportedStatusCode[$code];
        }

        function send($data){
            if(!$this->access) {
                header("{$this->serverProtocol} 400 {$this->displayStatusCodeMessage('400')}");
                
                return $this;
            }
            else if($this->code == '') {
                $this->code = '200';
            }
            header("{$this->serverProtocol} {$this->code} {$this->displayStatusCodeMessage($this->code)}");
            echo json_encode($data);
            return $this;
        }
  
        function status($code) {
            $this->code= (string)$code;
            if($this->code !== '200') {
                if(!in_array($this->code, $this->supportedStatusCode)) {
                    header("{$this->serverProtocol} ");
                    return ;
                }
                header("{$this->serverProtocol} $code {$this->displayStatusCodeMessage($code)}");
                return $this;
            }
            $this->access = FALSE;
            return $this;
        }
    }
?>