<script type="text/javascript">
    var LocsA = [
        {
            lat: 45.9,
            lon: 10.9,
            title: 'Title A1',
            html: '<h3>Content A1</h3>',
            icon: 'http://maps.google.com/mapfiles/markerA.png',
            //animation: google.maps.Animation.DROP
        },
        {
            lat: 44.8,
            lon: 1.7,
            title: 'Title B1',
            html: '<h3>Content B1</h3>',
            icon: 'http://maps.google.com/mapfiles/markerB.png',
            show_infowindow: false
        },
        {
            lat: 51.5,
            lon: -1.1,
            title: 'Title C1',
            html: [
                '<h3>Content C1</h3>',
                '<p>Lorem Ipsum..</p>'
            ].join(''),
            zoom: 8,
            icon: 'http://maps.google.com/mapfiles/markerC.png'
        }
    ];
    var LocsB = [
        {
            lat: 52.1,
            lon: 11.3,
            title: 'Title A2',
            html: [
                '<h3>Content A2</h3>',
                '<p>Lorem Ipsum..</p>'
            ].join(''),
            zoom: 8
        },
        {
            lat: 51.2,
            lon: 22.2,
            title: 'Title B2',
            html: [
                '<h3>Content B2</h3>',
                '<p>Lorem Ipsum..</p>'
            ].join(''),
            zoom: 8
        },
        {
            lat: 49.4,
            lon: 35.9,
            title: 'Title C2',
            html: [
                '<h3>Content C2</h3>',
                '<p>Lorem Ipsum..</p>'
            ].join(''),
            zoom: 4
        },
        {
            lat: 47.8,
            lon: 15.6,
            title: 'Title D2',
            html: [
                '<h3>Content D2</h3>',
                '<p>Lorem Ipsum..</p>'
            ].join(''),
            zoom: 6
        }
    ];
    var LocsAB = LocsA.concat(LocsB);
    $(function () {
        new Maplace({
            locations: LocsAB,
            map_div: '#gmap-menu',
            controls_type: 'list',
            controls_on_map: false
        }).Load();
    });
</script>

<div class="row">
    <div class="card card-underline">
        <div class="card-head">
            <header><i class="fa fa-map-marker"></i>
                Lista de Proyectos
            </header>
        </div>
        <div class="card-body">
            <div class="col-lg-12">
                <div id="gmap"></div>
                <div id="controls" class="height-10 col-md-3"s></div>
                <div id="gmap-menu" class="height-10 col-md-9"></div>
            </div>
        </div>


    </div>
</div>




<table>
    <tbody>
        <?php foreach ($proyectos as $p): ?>
            <tr>
                <td> 
                    <?php
                    echo $p['nombre'];
                    ?>
                </td>
                <td> 
                    <?php
                    echo $p['localidad'];
                    ?>
                </td>

            </tr>

        <?php endforeach; ?>

    </tbody>
</table>
