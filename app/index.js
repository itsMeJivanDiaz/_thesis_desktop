$(window).on('load', function(){

    // init calls

    resizefnc();

    // events
    // disable browser tools
    //$(document).on("keydown", disable);

    // map on

    $('#location').click(function(){
        $('#map').addClass('map-active')
        $('.leaflet-right').addClass('leaf-active')
        $('#close-map').addClass('active-close')
    })

    // close map

    $('#close-map').click(function(){
        $('#map').removeClass('map-active')
        $('.leaflet-right').removeClass('leaf-active')
        $('#close-map').removeClass('active-close')
    })

    // register animation event

    $('.mn_btn').click(function(){
        var to = $(this).attr('data-to');
        var from = $(this).attr('data-from');
        if(to == null || from == null){
            var from = $(this).attr('data-value')
            $('#scroll_1').find('.body-container').addClass('active');
            $('#'  + from).find('.body-container').addClass('active');
            setTimeout(function(){
                document.getElementById('scroll_1').scrollIntoView();
                $('#close').removeClass('active-cls');
            },700)
            setTimeout(() => {
                $('#scroll_1').find('.body-container').removeClass('active');
            }, 1500);
            document.getElementById('reg-form-id').reset();
            
        }else{
            $('#close').attr('data-value', to);
            $('#'  + from).find('.body-container').addClass('active');
            setTimeout(function(){
                document.getElementById(to).scrollIntoView();
            },700)
            setTimeout(() => {
                $('#'  + from).find('.body-container').removeClass('active');
                $('#'  + to).find('.body-container').removeClass('active');
                $('#close').addClass('active-cls');
            }, 1500);
        }
    });
    
    // functions

    setTimeout(function(){
        $('.load_wrapper').addClass('deac');
    }, 3000);   

    function disable(e) { 
        if (e.keyCode == 116 || e.keyCode == 123 ||
            e.ctrlKey && e.shiftKey && e.keyCode == 73 || 
            e.ctrlKey && (e.keyCode === 85 || e.keyCode === 83 || e.keyCode ===65 ))  {
            e.preventDefault(); 
        }
    };

    function resizefnc(){
        window.resizeTo(1000, 660);
    }

    // map

    var marker = {}
    var map = L.map('map').setView([10.639181, 122.977394], 7);
    L.Control.geocoder({
        defaultMarkGeocode: false,
    }).on('markgeocode', function(e){
        map.removeLayer(marker)
        marker = L.marker([e.geocode.center.lat, e.geocode.center.lng], 30).addTo(map)
        .bindPopup('Set this as Establishment\'s Location?'+'<br>'+'<button class="map-btn">Get Location</button>')
        .openPopup()
        $('.map-btn').click(function(){
            $('.map-btn').addClass('bgc-4').html('Saved')
            $('#loc-input').val(e.geocode.center.lat +','+ e.geocode.center.lng)
        })
        map.fitBounds(e.geocode.bbox)
    }).addTo(map);
    map.on("click", function(e){
        lat = e.latlng.lat;
        long = e.latlng.lng;
        if (marker != undefined){
            map.removeLayer(marker)
        }
        marker = L.marker([lat, long]).addTo(map)
            .bindPopup('Set this as Establishment\'s Location?'+'<br>'+'<button class="map-btn">Get Location</button>')
            .openPopup()
            $('.map-btn').click(function(){
                $('.map-btn').addClass('bgc-4').html('Saved')
                $('#loc-input').val(e.latlng.lat +','+ e.latlng.lng)
            })
    })
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);


})