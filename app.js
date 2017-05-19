/**
 * Created by Atom on 2017/05/17.
 */
var app = angular.module('myApp', [
    'ui.tinymce',
    'ui.bootstrap',
    'jsonFormatter',
    'rt.debounce',
    'ngMaterial',
    'ngDialog'
]);

app.controller('PdfgenController', function ($scope, debounce, ngDialog, $http) {
    $scope.tinymceModel = '<p>Initial content</p> <table style="height: 71px;" width="100%" border="1"> <tbody> <tr> <td style="width: 0px;">Table Column 1</td> <td style="width: 0px;">Table Column 2</td> </tr> <tr> <td style="width: 0px;">row1</td> <td style="width: 0px;">&nbsp;</td> </tr> <tr> <td style="width: 0px;">row2</td> <td style="width: 0px;">&nbsp;</td> </tr> </tbody> </table>';

    $scope.busy = false;
    $scope.tinymceOptions = {
        plugins: 'link image code advlist autolink lists link image charmap print preview anchor searchreplace visualblocks  fullscreen  insertdatetime media  table contextmenu  imagetools    ',
        toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | code',
        style_formats: [
            {title: 'Bold text', inline: 'strong'},
            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
            {
                title: 'Badge',
                inline: 'span',
                styles: {
                    display: 'inline-block',
                    border: '1px solid #2276d2',
                    'border-radius': '5px',
                    padding: '2px 5px',
                    margin: '0 2px',
                    color: '#2276d2'
                }
            },
            {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
        ]
    };

    var _generatpdf = function(dd) {
        var pdfDocGenerator = pdfMake.createPdf(dd);
        pdfDocGenerator.getDataUrl(function (dataUrl) {
            var targetElement = document.querySelector('#iframeContainer');
            $(targetElement).html("");
            var iframe = document.createElement('iframe');
            iframe.src = dataUrl;
            iframe.width = 1140;
            iframe.height = 800;
            targetElement.appendChild(iframe);
            $scope.busy = false;
        });
    };

    $scope.sample = function() {
        var url = "invoice.docdef.json";
        $http({url: url}).then(function (rs) {
            console.log(rs.data);
            $scope.dd = rs.data;
            _generatpdf($scope.dd);
        });
    };
    $scope.sample();
    $scope.preview = debounce(400, function () {
        // var parser = new DOMParser();
        htmlDoc = jQuery.parseHTML($scope.tinymceModel);
        // $scope.dd = htmlDoc;
        $scope.busy = true;
        $scope.dd.content = [{text: $scope.tinymceModel}];
        _generatpdf($scope.dd);
    });
    // $scope.preview();

    $scope.help = function (x) {
        $scope.hform = {
            addform: 1,
            formtitle: "Add",
            btn: "ADD",
            forminput: null
        };
        ngDialog.open({
            template: 'help.html',
            scope: $scope,
            appendClassName: 'ngdialog-normal',
            controller: ['$scope', '$filter', '$rootScope', function ($scope, $filter, $rootScope) {
                $scope.cancel = function () {
                    $scope.closeThisDialog();
                }
            }
            ],
            className: 'ngdialog-theme-default ngdialog-theme-custom'
        });

    }


});
