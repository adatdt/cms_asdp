<script async defer
    src="https://maps.google.com/maps/api/js?key=AIzaSyDk_JhHOVjSy5xU4FnKUXcomihclcuU170&q=Space+Needle,Seattle+WA&sensor=false&libraries=places&v=weekly">
</script>
<script>
    function initMapView(getLat, getLong, radius) {


        var  midPoint = new google.maps.LatLng(parseFloat(getLat), parseFloat(getLong));

        // console.log(midPoint)
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,                      
            center: midPoint,
            disableDefaultUI: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP,

        });

        var radiusCount = 1000;
        if(radius.radiusType.toUpperCase() == "M")
        {
            radiusCount = 1;
        }
        var getRadius =parseInt(radius.radius);
        if(getLat && getLong ){
            var midPoint = {lat: parseFloat(getLat), lng: parseFloat(getLong)};
            var totRadius = getRadius * radiusCount;
            
            var antennasCircle = new google.maps.Circle({
                strokeColor: "#FF0000",
                strokeOpacity: 0.8,
                // strokeWeight: 2,
                strokeWeight: 0,
                fillColor: "#FF0000",
                fillOpacity: 0.35,
                map: map,
                clickable: false,
                // center: midPoint,
                center: new google.maps.LatLng(getLat, getLong),
                radius: totRadius

            });
        map.fitBounds(antennasCircle.getBounds());
        addMarker(midPoint);
        }

    }

    // Add marker 
    function addMarker(location) {
        var marker = new google.maps.Marker({
            position: location,
            map: map
        });
    }

    function downloadBase64File(base64Data, fileName) {
        //const linkSource = `data:${contentType};base64,${base64Data}`;
        const linkSource = base64Data;
        // console.log(linkSource)
        const downloadLink = document.createElement("a");
        downloadLink.href = linkSource;
        downloadLink.download = fileName;
        downloadLink.click();
    }
</script>