angular.module('footer', [
    
])

.config(function ($stateProvider) {
    $stateProvider
        .state('aperfectday.footer', {
            url: 'footer',
            views: {
                //target the ui-view named 'content' in ROOT state (aperfectday)
                'gauge@':{
                    template: ''
                },
                'content@': {
                    template: ''
                },
                'about@': {
                    template: ''
                },
                'intro@': {
                    template: ''
                },
                'footer@': {                     
                    controller: 'FooterCtrl as footerCtrl',          
                    templateUrl: 'app/footer/footer.tmpl.html'
                }                
            },
            data : {
                routeClass : 'footer'
            },
            params: {
                
            }
        })
    ;
})