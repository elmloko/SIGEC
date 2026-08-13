
<html><head>
        <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
        <title>X-TABLE Full</title>
        <link href="xtable.css" media="screen" type="text/css" rel="stylesheet">
        <link href="prettify.css" media="screen" type="text/css" rel="stylesheet">
        <link href="x-table-icons.css" media="screen" type="text/css" rel="stylesheet">

        <script src="jquery.js" type="text/javascript">
        </script>
        <script src="jquery.xtable-1.1.js" type="text/javascript"></script>
        <script src="jquery.cookie.js" type="text/javascript"></script>
        <script src="prettify.js" type="text/javascript"></script>
       <!-- <script src="demo.js" type="text/javascript"></script>-->

        <style>
            body{
                background:#E5E5E5;
            }
            bodydiv {
                background:white;
                font-family: tahoma,arial,verdana,sans-serif;
                font-size: 12px;
                width: 960px;
                margin: 0 auto;
                background: none repeat scroll 0 0 #FAFBFC;
                border: 1px solid #AAAAAA;
                box-shadow: 0 1px 8px rgba(0, 0, 0, 0.25);
                padding: 3px;
            }
        </style></head>
    <body>


        <table>
            <tbody><tr>
                    <td width="10%">Rank</td>
                    <td width="10%"><input type="text" id="src_codigo" style="border:1px solid #CCC;"></td>
                    <td>Rating</td>
                    <td><input type="text" id="src_nur" style="border:1px solid #CCC;"></td>
                    <td valign="middle" rowspan="2"><button onclick="search();">Search</button> </td>
                </tr>
                <tr>
                    <td>Title</td>
                    <td><input type="text" id="src_referencia" style="border:1px solid #CCC;"></td>
                    <td>Vote</td>
                    <td><input type="text" id="src_estado" style="border:1px solid #CCC;"></td>
                </tr>
            </tbody>
        </table>

    </div>
    <div id="demo">
    </div>
    <br>
</div>

<script>
    var config = {
        width: '100%',
        height: 400,
        title: 'IMDB Top 250',
        toolbar: {align: 'right',
            buttons: [{text: 'Reload',
                    icon: 'refresh',
                    script: function (e, id) {
                        $(e).click(function () {
                            $('#' + id).xTable('reload');
                        });
                    }
                }, {
                    text: 'Add',
                    script: function (e, id) {
                        $(e).click(function () {
                            alert('Add');
                        });
                    }
                }]},
        columns: [
            {header: 'Rank', width: '20%', name: 'codigo', align: 'center', order: true},
            {header: 'Rating', width: '10%', name: 'nur', align: 'center', order: true},
            {header: 'Title', width: '40%', name: 'referencia', order: true},
            {header: 'Votes', width: '10%', name: 'estado', align: 'center', order: true},
            {width: '10%', align: 'center',
                data: function (obj, id, tr) {
                    var b = $('<button></button>');
                    b
                            .html('Edit')
                            .click(function () {
                                alert('Edit, Title:' + obj.nur);
                            });
                    return b;
                }
            },
            {width: '10%', align: 'center',
                data: function (obj, id, tr) {
                    var b = $('<button></button>');
                    b
                            .html('Delete')
                            .click(function () {
                                alert('Delete, Title:' + obj.nur);
                            });
                    return b;
                }
            }],
        url: 'server.php',
        type: 'json',
        pakages: ['remote'],
        pagination: {
            message: "Mostrando %s - %s de %s",
            record_per_page: 10
        },
        loading_message: 'Loading...',
        order: {column: 'nur', type: 'ASC'}
    };

    $('#demo').xTable(config);
    
    function search() {
        var conditions = {
            codigo: $('#src_codigo').val(),
            nur: $('#src_nur').val(),
            referencia: $('#src_referencia').val(),
            estado: $('#src_estado').val()
        };
        $('#demo').xTable('conditions', conditions);
        $('#demo').xTable('reload');
    }

</script>
</body></html>

