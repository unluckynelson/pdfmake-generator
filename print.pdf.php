<?php
/**
 * This is a sample PDF DocDefinition for an Invoice from another project
 */
?>
<!doctype html>
<html>
<head>
    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/angular/angular.min.js"></script>
    <script src="bower_components/html5shiv/dist/html5shiv.min.js"></script>
    <script src="bower_components/pdfmake/build/pdfmake.js"></script>
    <script src="bower_components/pdfmake/build/vfs_fonts.js"></script>

    <meta charset="utf-8">
    <title>PDF Document</title>
    <script>

        <?php
        function prnimg($path)
        {
            //$path = 'images/pdf/banner.jpg';
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            echo $base64;
        }
        ?>
        var darkOutline = {
            hLineWidth: function (i, node) {
                return (i === 0 || i === node.table.body.length) ? 2 : 0;
            },
            vLineWidth: function (i, node) {
                return (i === 0 || i === node.table.widths.length) ? 2 : 0;
            },
            hLineColor: function (i, node) {
                return (i === 0 || i === node.table.body.length) ? 'black' : 'gray';
            },
            vLineColor: function (i, node) {
                return (i === 0 || i === node.table.widths.length) ? 'black' : 'gray';
            }
        };
        var slimOutline = {
            hLineWidth: function (i, node) {
                return (i === 0 || i === node.table.body.length) ? 1 : 0;
            },
            vLineWidth: function (i, node) {
                return (i === 0 || i === node.table.widths.length) ? 1 : 0;
            },
            hLineColor: function (i, node) {
                return (i === 0 || i === node.table.body.length) ? 'gray' : 'gray';
            },
            vLineColor: function (i, node) {
                return (i === 0 || i === node.table.widths.length) ? 'gray' : 'gray';
            },
            paddingLeft: function (i, node) {
                return 8;
            },
            paddingRight: function (i, node) {
                return 8;
            }
            ,
            paddingTop: function (i, node) {
                return 8;
            },
            paddingBottom: function (i, node) {
                return 8;
            }

        };
        var slimAllBorders = {
                hLineWidth: function (i, node) {
                    return (i === 0 || i === node.table.body.length) ? 1 : 1;
                },
                vLineWidth: function (i, node) {
                    return (i === 0 || i === node.table.widths.length) ? 1 : 1;
                },
                hLineColor: function (i, node) {
                    return (i === 0 || i === node.table.body.length) ? 'gray' : 'gray';
                },
                vLineColor: function (i, node) {
                    return (i === 0 || i === node.table.widths.length) ? 'gray' : 'gray';
                },
                paddingLeft: function (i, node) {
                    return 4;
                },
                paddingRight: function (i, node) {
                    return 4;
                }
                ,
                paddingTop: function (i, node) {
                    return 4;
                },
                paddingBottom: function (i, node) {
                    return 4;
                }

            }
        ;
        var feint = "#BCBCBC";
        var feintAllBorders = {
                hLineWidth: function (i, node) {
                    return (i === 0 || i === node.table.body.length) ? 1 : 1;
                },
                vLineWidth: function (i, node) {
                    return (i === 0 || i === node.table.widths.length) ? 1 : 1;
                },
                hLineColor: function (i, node) {
                    return (i === 0 || i === node.table.body.length) ? feint : feint;
                },
                vLineColor: function (i, node) {
                    return (i === 0 || i === node.table.widths.length) ? feint : feint;
                },
                paddingLeft: function (i, node) {
                    return 4;
                },
                paddingRight: function (i, node) {
                    return 4;
                }
                ,
                paddingTop: function (i, node) {
                    return 4;
                },
                paddingBottom: function (i, node) {
                    return 0;
                }

            }
        ;
        var paddingAll = {
            paddingLeft: function (i, node) {
                return 4;
            },
            paddingRight: function (i, node) {
                return 4;
            },
            paddingTop: function (i, node) {
                return 4;
            },
            paddingBottom: function (i, node) {
                return 4;
            }
        };
        var noPadding = {
            paddingLeft: function (i, node) {
                return 0;
            },
            paddingRight: function (i, node) {
                return 0;
            },
            paddingTop: function (i, node) {
                return 0;
            },
            paddingBottom: function (i, node) {
                return 0;
            }
        };
        var docDefinition = {

            pageSize: "A4",
            pageMargins: [15, 20, 15, 150],
            content: [
                {
                    //style: "tableBanner",
                    layout: slimOutline,
                    table: {
                        body: [
                            [
                                {
                                    image: "banner",
                                    width: 550
                                }
                            ],
                            [{

                                alignment: 'left',
                                margin: 0,
                                style: 'myHeader',
                                text: [
                                    {text: "Tax Invoice: ", bold: true}, {text: "#1234\n\n"},
                                    {text: "Date: ", bold: true}, {text: "<?php echo date("Y-m-d") ?>"}

                                ]
                            }],
                            [
                                {
                                    layout: slimAllBorders,
                                    style: "contentFont",
                                    table: {
                                        widths: [255, "*"],
                                        body: [
                                            [{
                                                text: [{
                                                    text: "Company: ",
                                                    bold: true
                                                }, "Test Company"]
                                            }, {
                                                text: [{
                                                    text: "Contact: ",
                                                    bold: true
                                                }, "Joe Soap"]
                                            }],

                                            [{
                                                text: [{
                                                    text: "Office Tel: ",
                                                    bold: true
                                                }, "+1-555-1045"]
                                            }, {
                                                text: [{
                                                    text: "Cellphone: ",
                                                    bold: true
                                                }, "+1-555-7894"]
                                            }],

                                            [{
                                                text: [{
                                                    text: "Home Tel: ",
                                                    bold: true
                                                }, "+1-555-1564"]
                                            }, {
                                                text: [{
                                                    text: "Buyer's No: ",
                                                    bold: true
                                                }, "+1-555-7894"]
                                            }],

                                            [{
                                                text: [{
                                                    text: "Email: ",
                                                    bold: true
                                                }, "joesoap@gmail.com"]
                                            }, {
                                                rowSpan: 2,
                                                text: [{text: "Address: ", bold: true}, "1 Pdf, Drive, New York City"]
                                            }],

                                            [{
                                                text: [{
                                                    text: "VAT/Id: ",
                                                    bold: true
                                                }, "12332145565"]
                                            }, ""]


                                        ]
                                    }


                                    //end
                                }
                            ],
                            [{
                                style: "contentFont",
                                columns: [
                                    {
                                        width: 255,
                                        text: [{text: "Auction: ", bold: true}, "Various Assets"],

                                    },


                                    {
                                        alignment: 'right',
                                        width: "*",
                                        text: [{text: "Auction Date: ", bold: true}, "01-01-2017"],

                                    }
                                ]
                            }],
                            [{
                                style: "contentFont",
                                margin: 0,
                                columns: [
                                    {
                                        width: 255,
                                        text: [{text: "Terms: ", bold: true}, "10% Deposit required"],

                                    },


                                    {
                                        alignment: 'right',
                                        width: "*",
                                        text: [{text: "", bold: true}, "10% Commision"],

                                    }
                                ]
                            }],
                            [
                                {

                                    layout: feintAllBorders,
                                    style: "contentFont",
                                    table: {
                                        widths: [25, "*", 25, 80, 80],
                                        headerRows: 1,
                                        body: [[
                                            {
                                                text: "Lot", style: 'tableHeader'
                                            },
                                            {
                                                text: "Description", style: 'tableHeader'
                                            },
                                            {
                                                text: "QTY", style: 'tableHeader'
                                            },
                                            {
                                                text: "Item Cost", style: 'tableHeader'
                                            },
                                            {
                                                text: "Total", style: 'tableHeader'
                                            }],


                                            //["", "", "", "", ""],
                                            [{text: '', colSpan: 5}, {}, {}, {}, {}],
                                            [{
                                                text: 'SUB-TOTAL\nCommission\n14% VAT',
                                                colSpan: 4,
                                                rowSpan: 3,
                                                alignment: 'right',
                                                style: 'subcontentFont'

                                            }, {}, {}, {}, {
                                                text: '500.00',
                                                alignment: 'right',
                                                bold: true
                                            }],


                                            [{
                                                text: 'Commision (10%)',
                                                colSpan: 4,
                                                alignment: 'right',
                                                bold: true
                                            }, {}, {}, {}, {text: '50.00', alignment: 'right'}],

                                            [{
                                                text: '14% VAT',
                                                colSpan: 4,
                                                alignment: 'right',
                                                bold: true
                                            }, {}, {}, {}, {text: '5.00', alignment: 'right'}],

                                            [{text: '', colSpan: 5}, {}, {}, {}, {}],

                                            [{
                                                text: 'Less Deposit',
                                                colSpan: 4,
                                                alignment: 'right'
//                                                    bold: true,
//                                                    fontSize: 12
                                            }, {}, {}, {}, {
                                                text: '200.00',
                                                alignment: 'right'
//                                                    bold: true,
//                                                    fontSize: 12
                                            }],

                                            [{
                                                text: 'TOTAL',
                                                colSpan: 4,
                                                alignment: 'right',
                                                bold: true,
                                                fontSize: 12
                                            }, {}, {}, {}, {
                                                text: '320.00',
                                                alignment: 'right',
                                                bold: true,
                                                fontSize: 12
                                            }]

                                        ]
                                    }
                                }

                            ],
                            [{
                                style: "contentFont",
                                alignment: 'center',
                                margin: 0,
                                text: [
                                    {text: "Banking Details: \n", bold: true},
                                    {text: "ACME Auctioneers\n"},
                                    {text: "Bank - Branch: 250655\n"},
                                    {text: "Account no: 123 456 789 10\n"},
                                    {text: "Ref No: "},
                                    {text: "#1234", bold: true}
                                ]
                            }]
                        ]
                    }
                }
            ]

            ,

            styles: {
                header: {
                    fontSize: 20,
                    bold: true,
                    alignment: "left",
                    color: "#000"
                }
                ,
                tableEx: {
                    // margin: [left, top, right, bottom]
                    margin: [0, 5, 0, 15],

                }
                ,
                myHeader: {fontSize: 12, color: "black"},
                tableHeader: {bold: true, fontSize: 12, color: "black", alignment: "center"},
                tableBanner: {
                    width: "100%"

                }
                ,
                bannerStyle: {
                    //bold: true,
                    fontSize: 10,
                    color: "#3B3B3B"
                }
                ,
                contentFont: {
                    fontSize: 10,
                    color: "#3B3B3B"
                }
                , subcontentFont: {
                    fontSize: 10,
                    lineHeight: 1.3,
                    color: "#3B3B3B"
                }

            }
            ,
            images: {
                banner: "<?php prnimg('invoice-header.png') ?>"
            }
        };


        console.log(JSON.stringify(docDefinition));
        //                pdfMake.createPdf(docDefinition).open();

        function callme(data) {
            var htmlText = '<embed width="100%" height="100%"'
                + ' type="application/pdf"'
                + ' src="data:application/pdf;base64,'
                + escape(data)
                + '"></embed>';
            $("#pdfdoc").append(htmlText);
            //$("body").append('<iframe id="mypdf" type="application/pdf"  width="65%" height="2000" src="data:application/pdf;base64,'+data+'"> </iframe>');
        }
        pdfMake.createPdf(docDefinition).getBase64(callme);


        var html = {
            table: {
                class: [],
                style: [],
                tr: [
                    {
                        td: {
                            class: [],
                            style: [],
                            text: []
                        }
                    },
                    {
                        td: {
                            class: [],
                            style: [],
                            text: []
                        }
                    }
                ]
            }

        }


    </script>
</head>
<body>
<div id="pdfdoc" style="width: 70%; height: 1000px"></div>
</body>
</html>
