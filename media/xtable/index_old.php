
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
        <script src="demo.js" type="text/javascript"></script>

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
                    <td>Rank</td>
                    <td><input type="text" id="src_rank" style="border:1px solid #CCC;"></td>
                    <td>Rating</td>
                    <td><input type="text" id="src_rating" style="border:1px solid #CCC;"></td>
                    <td valign="middle" rowspan="2"><button onclick="search();">Search</button> </td>
                </tr>
                <tr>
                    <td>Title</td>
                    <td><input type="text" id="src_title" style="border:1px solid #CCC;"></td>
                    <td>Vote</td>
                    <td><input type="text" id="src_vote" style="border:1px solid #CCC;"></td>
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
            s{header: 'Rank', width: 50, name: 'rank', align: 'center', order: true},
            {header: 'Rating', width: 50, name: 'rating', align: 'center', order: true},
            {header: 'Title', width: 500, name: 'movie_name', order: true},
            {header: 'Votes', width: 80, name: 'vote', align: 'center', order: true},
            {width: 70, align: 'center',
                data: function (obj, id, tr) {
                    var b = $('<button></button>');
                    b
                            .html('Edit')
                            .click(function () {
                                alert('Edit, Title:' + obj.movie_name);
                            });
                    return b;
                }
            },
            {width: 70, align: 'center',
                data: function (obj, id, tr) {
                    var b = $('<button></button>');
                    b
                            .html('Delete')
                            .click(function () {
                                alert('Delete, Title:' + obj.movie_name);
                            });
                    return b;
                }
            }],
        url: 'server.php',
        type: 'json',
        pakages: ['remote'],
        pagination: {
            message: "Displaying movies %s - %s of %s",
            record_per_page: 20
        },
        loading_message: 'Loading...',
        order: {column: 'rank', type: 'ASC'}
    };

    $('#demo').xTable(config);
    alert("s");
    function search() {
        var conditions = {
            rank: $('#src_rank').val(),
            rating: $('#src_rating').val(),
            movie_name: $('#src_title').val(),
            vote: $('#src_vote').val()
        };
        $('#demo').xTable('conditions', conditions);
        $('#demo').xTable('reload');
    }

</script>
</body></html>

