<?php

    $zipcodes = get_post_meta( $post_id, '_mhmcrm__real_estate_zip_code', true );

    $zipcodes_array = array_filter( ( array ) explode( ",", $zipcodes ) );

    if ( ! empty( $zipcodes_array ) ) :
?>


<div class="bootstrap-iso" style="height: 700px;">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <p class="form-field">
                    <label>Zip Codes</label>
                    <input type="text" class="form-control" name="_real_estate_zip_code" value="<?php echo $zipcodes; ?>">
                </p>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div id="mhmcrm-map-canvas" style="height: 590px; width: 100%; position: absolute; top: 0px; left: 0px; background-color: rgb(229, 227, 223); overflow: hidden;"></div>
            </div>
        </div>
    </div>
</div>
<script>

jQuery( document ).ready( function($) {

    var map;
    map = new google.maps.Map(document.getElementById('mhmcrm-map-canvas'), {
        center: {lat: 37.45117949999999, lng: -122.2029832},
        zoom: 8
    });

    <?php foreach ( $zipcodes_array as $zipcode ) : ?>
        $.getJSON( 'https://maps.googleapis.com/maps/api/geocode/json?address=<?php echo trim($zipcode); ?>+America&key=AIzaSyDugRsKl3salPgJSVdSqf7InwcjhRU3r2g', function( data ) {
            var results = data.results;

            for ( var i = 0; i < results.length; i++ ) {

                var lat = results[i].geometry.location.lat;
                var lng = results[i].geometry.location.lng;

                var infowindow =  new google.maps.InfoWindow({});
                var marker, count;

                marker = new google.maps.Marker({
                    position: new google.maps.LatLng( lat, lng ),
                    map: map,
                    title: "<?php echo $zipcode; ?>"
                });

                if ( i == 0 ) {
                    var latlng = marker.getPosition();
                    map.setCenter( latlng );
                }

                google.maps.event.addListener(marker, 'click', (function (marker, count) {
                    return function () {
                        infowindow.setContent("<?php echo $zipcode; ?>");
                        infowindow.open(map, marker);
                    }
                })(marker, count));

            }
        });
    <?php endforeach; ?>

});
</script>

<?php else: ?>
<div class="bootstrap-iso">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <p class="form-field">
                    <label>Zip Codes</label>
                    <input type="text" class="form-control" name="_real_estate_zip_code" value="<?php echo $zipcodes; ?>">
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>