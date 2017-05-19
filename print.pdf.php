<?php require_once('../../../../Connections/rootx.php'); ?>
<?php
//include "../../includes/pre_funcs.php";
//include "../../includes/joins.php";
if (!function_exists("toMoney")) {
    function toMoney($val, $symbol = 'R ', $r = 2)
    {


        $n = str_replace(",", "", $val);
        $n = str_replace(" ", "", $n);
        //return($n);
        if (is_numeric($n)) {
            $c = is_float($n) ? 1 : number_format($n, $r);
            $d = '.';
            $t = ',';
            $sign = ($n < 0) ? '-' : '';
            $i = $n = number_format(abs($n), $r);
            $j = (($j = strlen($i)) > 3) ? $j % 3 : 0;

            return $symbol . $sign . ($j ? substr($i, 0, $j) + $t : '') . preg_replace('/(\d{3})(?=\d)/', "$1" + $t, substr($i, $j));
        } else {
            return "R 0";
        }

    }
}
if (isset($_REQUEST['bid'])) {
    $bid = $_REQUEST['bid'];
} else {
    $bid = '26';
}


$sql = "SELECT * FROM loose_buyers WHERE id=" . $bid;
$rs = mysqli_query($rtx, $sql) or die(mysqli_error($rtx));
if ($buyer = mysqli_fetch_assoc($rs)) {
    $totalRows_rs = mysqli_num_rows($rs);
} else {
    die("invalid buyer");
}
foreach($buyer as $k=>$v) {
    $buyer[$k] = addslashes($v);
}

$sql = "SELECT * FROM loose_auctions WHERE id=" . $buyer['aid'];
$rs = mysqli_query($rtx, $sql) or die(mysqli_error($rtx));
$auction = mysqli_fetch_assoc($rs);
foreach($auction as $k=>$v) {
    $auction[$k] = addslashes($v);
}

$sql = "SELECT 
  loose_assets.*,
  loose_accounts.acid,
  loose_accounts.buy_id,
  loose_accounts.amount,
  loose_accounts.`paid_amt`,
  loose_accounts.`paid`,
  loose_accounts.`date_rec`,
  loose_buyers.`myid`,
  loose_buyers.`fname`
FROM
  loose_assets 
  LEFT JOIN loose_accounts 
    ON (
      loose_accounts.asset_id = loose_assets.`id`
      OR
      loose_accounts.`group_id` = loose_assets.`groupid`
    ) 
    LEFT JOIN loose_buyers
    ON (
      loose_buyers.id = loose_accounts.buy_id
    )
   WHERE loose_accounts.buy_id=" . $bid. " ORDER BY groupid,lot_nr ASC ";
$ars = mysqli_query($rtx, $sql) or die(mysqli_error($rtx));
$assets = [];
$assetgroups = array();
while ($arow = mysqli_fetch_assoc($ars)) {
    if ($arow['groupid'] != "") {
        if (!in_array($arow['groupid'], $assetgroups)) {
            array_push($assetgroups, $arow['groupid']);
            $arow['desc'] = "Group ".$arow['groupid'].": ".$arow['desc'];
            array_push($assets, $arow);
        } else {
            $arow['desc'] = "Group ".$arow['groupid'].": ".$arow['desc'];
            $arow['amount'] = "";
            array_push($assets, $arow);
        }
    } else {
        array_push($assets, $arow);
    }
}

$imgs = array();
$i = 0;
$mainimg = "";


function san($str)
{
    $str = strip_tags($str);
    $str = str_replace(PHP_EOL, " ", $str);
    $str = html_entity_decode($str);
    $str = addslashes($str);
    echo $str;
}

//if ($buyer['mobnum'] != "") $buyer['mobnum'] = "C: " . $buyer['mobnum'];
//if ($buyer['tel_w'] != "") $buyer['tel_w'] = "W: " . $buyer['tel_w'];
//if ($buyer['tel_h'] != "") $buyer['tel_h'] = "H: " . $buyer['tel_h'];
$contacts = $buyer['mobnum'] . $buyer['tel_w'] . $buyer['tel_h'];

$today = date('F j, Y', time());
$ac_date = date('F j, Y', strtotime($auction['a_date']));
$ref = date('dMy', strtotime($auction['a_date']));
$ref = strtoupper($ref) . "A" . $auction['ref'] . "-" . $buyer['myid'];

$subtotal = 0;
$flines = "";
foreach ($assets as $val) {
    $subtotal += $val['amount'];
    $itemcost = toMoney($val['amount']);
    $totalcost = toMoney($val['amount']);
    if ($val['amount'] == 0) {
        $itemcost = "";
        $totalcost = "";
    }
    foreach($val as $k=>$v) {
        $val[$k] = addslashes($v);
    }
    $flines .= "['" . $val['lot_nr'] . "', '" . $val['desc'] . "',{text:'1',alignment: 'right'}, {text:\"" . $itemcost . "\",alignment: 'right'}, {text:\"" . $totalcost . "\",alignment: 'right'}],";
}


$tmp = 0;
$comm = "";
if ($auction['commision'] != "") {
    $tmp = $subtotal * ($auction['commision'] / 100);
    $comm = $auction['commision'];
}
$commtitle = "";
if ($comm != "") $commtitle = "Commision (" . $comm . "%)";
$vat = ($subtotal + $tmp) * 0.14;
$total = $subtotal + $tmp + $vat;

?>
<!doctype html>
<html>
<head>
    <script src="../../../../ceo/app/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="../../../../ceo/app/bower_components/angular/angular.min.js"></script>
    <script src="../../../../ceo/app/bower_components/html5shiv/dist/html5shiv.min.js"></script>
    <script src="../../../../ceo/app/bower_components/pdfmake/build/pdfmake.js"></script>
    <script src="../../../../ceo/app/bower_components/pdfmake/build/vfs_fonts.js"></script>

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
        }
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
                                        {text: "Tax Invoice: ", bold: true}, {text: "<?php echo $ref ?>\n\n"},
                                        {text: "Date: ", bold: true}, {text: "<?php echo $today ?>"}

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
                                                    }, "<?php san($buyer['company']); ?>"]
                                                }, {
                                                    text: [{
                                                        text: "Contact: ",
                                                        bold: true
                                                    }, "<?php san($buyer['fname'] . " " . $buyer['sname']); ?>"]
                                                }],

                                                [{
                                                    text: [{
                                                        text: "Office Tel: ",
                                                        bold: true
                                                    }, "<?php san($buyer['tel_w']); ?>"]
                                                }, {
                                                    text: [{
                                                        text: "Cellphone: ",
                                                        bold: true
                                                    }, "<?php san($buyer['mobnum']); ?>"]
                                                }],

                                                [{
                                                    text: [{
                                                        text: "Home Tel: ",
                                                        bold: true
                                                    }, "<?php san($buyer['tel_h']); ?>"]
                                                }, {
                                                    text: [{
                                                        text: "Buyer's No: ",
                                                        bold: true
                                                    }, "<?php san($buyer['myid']);  ?>"]
                                                }],

                                                [{
                                                    text: [{
                                                        text: "Email: ",
                                                        bold: true
                                                    }, "<?php san($buyer['email']); ?>"]
                                                }, {
                                                    rowSpan: 2,
                                                    text: [{text: "Address: ", bold: true}, <?php echo json_encode($buyer['addy']); ?>]
                                                }],

                                                [{
                                                    text: [{
                                                        text: "VAT/Id: ",
                                                        bold: true
                                                    }, "<?php san($buyer['id_no']); ?>"]
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
                                            text: [{text: "Auction: ", bold: true}, <?php echo json_encode($auction['desc']); ?>],

                                        },


                                        {
                                            alignment: 'right',
                                            width: "*",
                                            text: [{text: "Auction Date: ", bold: true}, "<?php echo $ac_date; ?>"],

                                        }
                                    ]
                                }],
                                [{
                                    style: "contentFont",
                                    margin: 0,
                                    columns: [
                                        {
                                            width: 255,
                                            text: [{text: "Terms: ", bold: true}, "<?php san($auction['terms']); ?>"],

                                        },


                                        {
                                            alignment: 'right',
                                            width: "*",
                                            text: [{text: "<?php echo $commtitle ?>", bold: true}, ""],

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

                                                <?php echo $flines;


                                                ?>

                                                //["", "", "", "", ""],
                                                [{text: '', colSpan: 5}, {}, {}, {}, {}],
                                                [{
                                                    text: 'SUB-TOTAL\n<?php echo $commtitle ?>\n14% VAT',
                                                    colSpan: 4,
                                                    rowSpan: 3,
                                                    alignment: 'right',
                                                    style:'subcontentFont'

                                                }, {}, {}, {}, {
                                                    text: '<?php echo toMoney($subtotal) ?>',
                                                    alignment: 'right',
                                                    bold: true
                                                }],

                                                <?php if ($comm != "") { ?>
                                                [{
                                                    text: 'Commision (<?php echo $comm; ?>)',
                                                    colSpan: 4,
                                                    alignment: 'right',
                                                    bold: true
                                                }, {}, {}, {}, {text: '<?php echo toMoney($tmp) ?>', alignment: 'right'}],
                                                <?php } else { ?>
                                                [{
                                                    text: '',
                                                    colSpan: 4,
                                                    alignment: 'right',
                                                    bold: true
                                                }, {}, {}, {}, '\n'],
                                                <?php } ?>
                                                [{
                                                    text: '14% VAT',
                                                    colSpan: 4,
                                                    alignment: 'right',
                                                    bold: true
                                                }, {}, {}, {}, {text: '<?php echo toMoney($vat) ?>', alignment: 'right'}],

                                                [{text: '', colSpan: 5}, {}, {}, {}, {}],
                                                <?php if ($buyer['depositpaid']) {
                                                $total = $total - $auction['deposit'];

                                                    ?>
                                                [{
                                                    text: 'Less Deposit',
                                                    colSpan: 4,
                                                    alignment: 'right'
//                                                    bold: true,
//                                                    fontSize: 12
                                                }, {}, {}, {}, {
                                                    text: '<?php echo toMoney($auction['deposit']) ?>',
                                                    alignment: 'right'
//                                                    bold: true,
//                                                    fontSize: 12
                                                }],
<?php } ?>
                                                [{
                                                    text: 'TOTAL',
                                                    colSpan: 4,
                                                    alignment: 'right',
                                                    bold: true,
                                                    fontSize: 12
                                                }, {}, {}, {}, {
                                                    text: '<?php echo toMoney($total) ?>',
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
                                        {text: "Root-X Auctioneers\n"},
                                        {text: "FNB Bank - Menlyn Branch: 250655\n"},
                                        {text: "Account no: 625 318 305 46\n"},
                                        {text: "Ref No: "},
                                        {text: "<?php echo $ref ?>", bold: true}
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
                    ,subcontentFont: {
                        fontSize: 10,
                        lineHeight: 1.3,
                        color: "#3B3B3B"
                    }

                }
                ,

                images: {
                    banner: "<?php prnimg('../../../../images/pdf/invoice-header.png') ?>"
                }
            }
            ;

//        var parentScope = [];
//        if($window.parent != null)
//        {
//            parentScope = $window.opener.ScopeToShare;
//        }

//        pdfMake.createPdf(docDefinition).download("invoice - <?php //san($ref)?>//.pdf");

                pdfMake.createPdf(docDefinition).open();

                function callme(data) {
                    var htmlText = '<embed width="100%" height="100%"'
                        + ' type="application/pdf"'
                        + ' src="data:application/pdf;base64,'
                        + escape(data)
                        + '"></embed>';
                    $("#damnpdf").append(htmlText);
                    //$("body").append('<iframe id="mypdf" type="application/pdf"  width="65%" height="2000" src="data:application/pdf;base64,'+data+'"> </iframe>');
                }
                pdfMake.createPdf(docDefinition).getBase64(callme);



        var html = {
            table: {
                class: [],
                style: [],
                tr : [
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
<div id="damnpdf" style="width: 70%; height: 1000px"></div>
</body>
</html>
