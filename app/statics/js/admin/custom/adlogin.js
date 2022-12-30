!function() {
    const angularApp = angular.module('loginApp',[]);

    angularApp.controller('loginCtrl', function ($scope) {
        $scope.LoginUser = ($event) => {
            $event.preventDefault();
            const vw = new UI.View()
            const csrfToken = input(vw.getNodesByName('csrf_token')[0].value);
            const data = {
                csrf_token: csrfToken, 
                username: input($scope.usrname),
                password: input($scope.ucode)
            };
            if(data.username !== '' && data.password !== '') {
                const loader = new ButtonLoader('adlogin-form-btn','Login');
                loader.loadButtonState('Verifying...');

                const payload = {
                    url: setUrl('super/service/login'),
                    send: [data],
                    jsonParse: true,
                    async: true
                }
                servo.post(payload).then(function({response}) {
                    AppSnackbar.showMessageBox(response.state, response.message);
                    loader.refreshStateButton();
                    if(response.state === true) {
                        UI.Redirect.to(setUrl('admin/view-accounts'));
                    }
                }).catch(({error}) => error);
            } else {
                AppSnackbar.showMessageBox(false, 'Invalid Username or Password');
            }
        };
    });
    function input(text) {
        return typeof text === 'undefined' || text === null ? '' : text.trim();
    }
}();

