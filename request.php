<?php
require_once('Router.php');


    class Request
    {
      public $query = array();
      public $params = array();
      public $body = array();

      function __construct() {
        $this->bootstrapSelf();
        $this->setQuery();
        $this->setBody();
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

      private function setQuery(){
        if(isset($this->queryString)){
          $query = explode("&", $this->queryString);

          foreach($_GET as $key => $value){
            $this->query[$key] = $value;
          }
        }
        $this->query = (object)$this->query;
      }

      private function setBody(){
        if ($this->requestMethod == "POST") {
          $this->body = json_decode(file_get_contents('php://input'), true);
        }
        $this->body = (object)$this->body;
      }

      protected function setParams($data){
        $this->params = $data;
        $this->params = (object)$this->params;
      }
    }
?>