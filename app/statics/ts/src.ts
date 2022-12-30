requirejs.config({
    baseUrl: staticUrl(''),
    paths: {
        /** Paths Here */
        jQuery        : 'libs/jquery/jquery.min',
        angular       : 'libs/angular/angular.min',
        ngRoute       : 'libs/angular/angular-route.min',
        ngAnimation   : 'libs/angular/angular-animation.min', 
        flyLib        : 'libs/fly-apis/lib',
        flyServo      : 'libs/fly-apis/servo',
        flyWorkers    : 'libs/fly-apis/workers'
    },
    shim: {
        jQuery  : {
            exports: 'jQuery'
        },
        flyServo: {
            exports: 'servo'
        },
        ngRoute: {
            deps: ['angular'],
            exports: 'angular'
        },
        ngAnimation: {
            deps: ['angular'],
            exports: 'angular'
        },
        angular: {
            exports: 'angular'
        }
    }
});

