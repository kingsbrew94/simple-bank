<?php namespace FLY_ENV\Util\Security;


trait RouterNISearch {

    private function getValidRouteIndex($routers)
    {
        foreach($routers as $index => $router) {
            if(!self::$has_valid_route) break;
            if(self::$valid_route->{'@url'} === $router->{'@url'}) return $index + 1;
            if(
                !isset(self::$valid_route->{'@name'}) && strpos($router->{'@url'},'@') === 0
                && preg_match($router->{'@url'}.'@',self::$valid_route->{'@url'})
            ) return $index + 1;
        }
        return null;
    }

    static private function getRandomIndex(int $min, int $max)
    {
        return mt_rand($min,$max < 0 ? $max + 1: $max);
    }

    static private function dSearch(int $length,$routers,$url_key) 
    {
        if(!($length >= 1)) return null;

        if(isset($routers[0]->{'@url'}) && isset($routers[0]->{'@name'})) {
            if(is_string($routers[0]->{'@url'}) && is_string($routers[0]->{'@name'})) {
                if($url_key === ':'.trim($routers[0]->{'@name'})) {
                    return ['state' => true, 'payload' => $routers[0]->{'@url'}];
                }
            }
        }
        if(isset($routers[$length - 1]->{'@url'}) && isset($routers[$length - 1]->{'@name'})) {
            if(is_string($routers[$length - 1]->{'@url'}) && is_string($routers[$length - 1]->{'@name'})) {
                if($url_key === ':'.trim($routers[$length - 1]->{'@name'})) {
                    return ['state' => true, 'payload' => $routers[$length - 1]->{'@url'}];
                }
            }
        }
        $middleIndex = ceil($length/2) - 1;

        if(isset($routers[$middleIndex]->{'@url'}) && isset($routers[$middleIndex]->{'@name'})) {
            if(is_string($routers[$middleIndex]->{'@url'}) && is_string($routers[$middleIndex]->{'@name'})) {
                if($url_key === ':'.trim($routers[$middleIndex]->{'@name'})) {
                    return ['state' => true, 'payload' => $routers[$middleIndex]->{'@url'}];
                }
            }
        }
        return ['state' => false,'payload' => self::filterRdmSearch($routers,$middleIndex)];
    }

    static private function filterRdmSearch($routers,$middle)
    {
        array_splice($routers,$middle,1);
        array_splice($routers,count($routers) - 1,1);
        array_splice($routers,0,1);
        return $routers;
    }

    static private function rdmGetUrlByName($routers, string $url_key)
    {
        $found = false;
        $url = null;
        while(!$found) {
            $routerLength = count($routers);
            if(count($routers) === 0) break;
            $payload = self::dSearch($routerLength,$routers,$url_key);
            if($payload === null) break;
            if($payload['state']) {
                $found = true;
                $url = $payload['payload'];
                continue;
            }
            $routers = $payload['payload'];
            $randIndex = self::getRandomIndex(0,count($routers) - 1);
            if(isset($routers[$randIndex]->{'@url'}) && isset($routers[$randIndex]->{'@name'})) {
                if(is_string($routers[$randIndex]->{'@url'}) && is_string($routers[$randIndex]->{'@name'})) {
                    if($url_key === ':'.trim($routers[$randIndex]->{'@name'})) {
                        $found = true;
                        $url = $routers[$randIndex]->{'@url'};
                    }
                }
            } 
            if(!$found) array_splice($routers,$randIndex,1);
        }
        return $url;
    }

    static private function getUrlByName($routers, $url_key)
    {
        foreach($routers as $router) {
            if(isset($router->{'@url'}) && isset($router->{'@name'})) {
                if(is_string($router->{'@url'}) && is_string($router->{'@name'}) ) {
                    if($url_key === ':'.trim($router->{'@name'}))
                        return $router->{'@url'};
                }
            }
        }
        return null;
    }
};