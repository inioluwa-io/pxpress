<?php
class Router extends Request
{
  private $request;
  private $response;
  private $supportedHttpMethods = array(
    "GET",
    "POST",
    "DELETE",
    "UPDATE"
  );
  function __construct($request, IApiResponse $response)
  {
   $this->request = $request;
   $this->response = $response;
  }
  function __call($name, $args)
  {
    $this->name = $name;
    list($route, $method) = $args;
    $this->method = $method;
    if(!in_array(strtoupper($name), $this->supportedHttpMethods))
    {
      $this->invalidMethodHandler();
    }
    $this->{strtolower($name)}[$this->formatRoute($route)] = $method;
  }
  /**
   * Removes trailing forward slashes from the right of the route and removes search querys.
   * @param route (string)
   */
  private function formatRoute($route)
  {
    $result = $route;
    $queryStart = strrpos($route, "?");
    $params = strrpos($result, ":");

    if($queryStart){ 
      $result = str_replace(substr($route, $queryStart), "", $route);
    } 
    
    if($params){
      $this->request->setparams(array(substr($result, $params+1) => $this->getUriParam() ));
      $result = str_replace(substr($result, $params), "{$this->getUriParam()}", $result);
    } 

    $result = rtrim($result, '/');
    if ($result === '')
    {
      return '/';
    }
    return $result;
  }
  private function invalidMethodHandler()
  {
    header("{$this->request->serverProtocol} 405 Method Not Allowed");
  }
  private function defaultRequestHandler()
  {
    header("{$this->request->serverProtocol} 404 Not Found");
  }
  /**
   * Resolves a route
   */
  private function resolve()
  {
    $methodDictionary = $this->{strtolower($this->request->requestMethod)};
    $formatedRoute = $this->formatRoute($this->request->requestUri);
    $method = $methodDictionary[$formatedRoute];
    if(is_null($method)){
      
      $this->defaultRequestHandler();
      return;
    };
    
    call_user_func_array($method, array($this->request, $this->response));
  }

  function getUriParam($data = null){
    $uri = $this->request->requestUri;
    $paramIndex = strripos($uri, "/");
    $queryIndex = strripos($uri, "?");
    if($queryIndex) {
      $uri = substr($uri, 0, $queryIndex);
    }

    if($paramIndex) {
      return substr($uri, $paramIndex+1);
    }

  }
  function __destruct()
  {
    $this->resolve();
  }
}
?>