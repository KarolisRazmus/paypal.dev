<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<script>
    function initMap(){
        var location = {lat: 54.710, lng: 25.261};
        var map = new google.maps.Map(document.getElementById("map"), {
            zoom: 14,
            center: location,
        });
        var marker = new google.maps.Marker({
            position: location,
            map: map
        });
    }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1KbfGbpPv7w_z9qqzOE_wciG7wLZeFDg&callback=initMap"></script>