<?php namespace FLY_ENV\Util\Security;


trait RouterSearch {

    use RouterNISearch;

    private function directSearch(int $length,$routers,$host_dir)
    {
        if(!($length >= 1)) return null;

        if(
            ($routers[0]->{'@url'} === self::$request_url xor '/'.$host_dir.$routers[0]->{'@url'} === self::$request_url)
            xor 
            (strpos($routers[0]->{'@url'},'@') === 0 && $this->matches_route_dynamic_url($routers[0]))
        ) {
            return ['state' => true, 'payload' => $routers[0]];
        }  
        if(
            ($routers[$length - 1]->{'@url'} === self::$request_url xor '/'.$host_dir.$routers[$length - 1]->{'@url'} === self::$request_url)
            xor
            (strpos($routers[$length - 1]->{'@url'},'@')=== 0 && $this->matches_route_dynamic_url($routers[$length - 1]))
        ) {
            return ['state' => true, 'payload' => $routers[$length - 1]];
        }
        $middleIndex = ceil($length/2) - 1;
        if(
            ($routers[$middleIndex]->{'@url'} === self::$request_url xor '/'.$host_dir.$routers[$middleIndex]->{'@url'} === self::$request_url)
            xor
            (strpos($routers[$middleIndex]->{'@url'},'@')=== 0 && $this->matches_route_dynamic_url($routers[$middleIndex]))
        ) {
            return ['state' => true, 'payload' => $routers[$middleIndex]];
        }
        return ['state' => false,'payload' => self::filterRdmSearch($routers,$middleIndex)];
    }

    private function makePulse(array $routers,int $pivot,$searchValue) {
        $counter = 0;
        $payload = ['state'=> false, 'payload' => $routers];
    
        while($counter < $pivot) {
            $payload = $this->directSearch(count($routers),$routers,$searchValue);
            if($payload === null) $counter = $pivot;
            $counter++;
        }
        return $payload;
    }

    private function randSearch($routers,$host_dir) {
        $host_dir = \str_replace('//','',$host_dir.'/');
        while(!self::$has_valid_route) {
            $routerLength = count($routers);
            if($routerLength === 0) break;

            $payload = $this->makePulse(
                $routers,
                floor(log($routerLength)),
                $host_dir
            );

            if($payload === null) break;
            if(isset($payload['payload']->{'@url'}) && !is_string($payload['payload']->{'@url'}))
                throw new \Exception('Error: route with url: '.$payload['payload']->{'@url'}.' at routes/'.self::$request_method.'.json has invalid url');

            if($payload['state']) {
                self::$has_valid_route = true;
                $payload['payload']->{'@url'} = $host_dir.$payload['payload']->{'@url'};
                self::$valid_route = $payload['payload'];
                continue;
            }
            $routers = $payload['payload'];
            $randIndex = self::getRandomIndex(0,count($routers) - 1);
            if(
                ($routers[$randIndex]->{'@url'} === self::$request_url xor '/'.$host_dir.$routers[$randIndex]->{'@url'} === self::$request_url)
                xor
                (strpos($routers[$randIndex]->{'@url'},'@')=== 0 && $this->matches_route_dynamic_url($routers[$randIndex]))
            ) {
                $routers[$randIndex]->{'@url'} = $host_dir.$routers[$randIndex]->{'@url'};
                self::$valid_route = $routers[$randIndex];
                self::$has_valid_route = true;
            } else array_splice($routers,$randIndex,1);
        }
    } 

    private function matches_route_direct_url(&$routers,$host_dir)
    {
        $host_dir = \str_replace('//','',$host_dir.'/');
    
        foreach($routers as $count => $router) {

            if(is_string($router->{'@url'})) {
                $flag_url = strpos($router->{'@url'},'@');
                $router_url = $router->{'@url'};
                if($router->{'@url'} === self::$request_url xor '/'.$host_dir.$router_url === self::$request_url){
                    $router->{'@url'} = $host_dir.$router_url;
                    self::$valid_route = $router;
                    self::$has_valid_route = true;
                    break;
                }
                if($flag_url === 0 && is_int($flag_url)) {
                    if($this->matches_route_dynamic_url($router)) break;
                }    
            }  else if(is_object($router->{'@url'}) && !is_string($router->{'@url'})) {
                throw new \Exception('Error: route '.($count + 1).' at routes/'.self::$request_method.'.json has invalid url');
            }
        }
    }
};