/* global google, jblundMapData */
/**
 * JBLund Dealers - Google Maps initialisation
 *
 * Called automatically by the Maps API via the `callback=jblundInitMap` URL
 * parameter once the API has fully loaded.
 */

var jblundMap;
var jblundOpenInfoWindow = null;

/**
 * Build the HTML content for a marker info window
 *
 * @param {Object} dealer
 * @param {Object} icons  Object with 'docks', 'lifts', 'trailers' URL strings
 * @returns {string}
 */
function jblundBuildInfoWindow(dealer, icons) {
    var html = '<div class="jblund-map-infowindow">';
    html += '<h3 class="jblund-map-name">' + dealer.name + '</h3>';

    if (dealer.address) {
        html += '<p class="jblund-map-address">' + dealer.address + '</p>';
    }

    if (dealer.phone) {
        html += '<p class="jblund-map-phone"><a href="tel:' + dealer.phone + '">' + dealer.phone + '</a></p>';
    }

    if (dealer.website) {
        html += '<p class="jblund-map-website"><a href="' + dealer.website + '" target="_blank" rel="noopener noreferrer">Visit Website &rarr;</a></p>';
    }

    var hasService = (dealer.docks === '1' || dealer.lifts === '1' || dealer.trailers === '1');
    if (hasService) {
        html += '<div class="jblund-map-services">';
        if (dealer.docks === '1') {
            html += '<span class="jblund-map-service" title="Docks"><img src="' + icons.docks + '" width="24" height="24" alt="Docks" /></span>';
        }
        if (dealer.lifts === '1') {
            html += '<span class="jblund-map-service" title="Lifts"><img src="' + icons.lifts + '" width="24" height="24" alt="Lifts" /></span>';
        }
        if (dealer.trailers === '1') {
            html += '<span class="jblund-map-service" title="Trailers"><img src="' + icons.trailers + '" width="24" height="24" alt="Trailers" /></span>';
        }
        html += '</div>';
    }

    html += '</div>';
    return html;
}

/**
 * Initialise the map — called by Google Maps API once loaded
 */
function jblundInitMap() {
    if (typeof jblundMapData === 'undefined' || !jblundMapData.dealers || !jblundMapData.dealers.length) {
        return;
    }

    var mapEl = document.getElementById('jblund-dealer-map');
    if (!mapEl) {
        return;
    }

    var dealers = jblundMapData.dealers;
    var icons   = jblundMapData.icons;

    jblundMap = new google.maps.Map(mapEl, {
        zoom: 6,
        center: { lat: dealers[0].lat, lng: dealers[0].lng },
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true,
        gestureHandling: 'cooperative',
    });

    var bounds = new google.maps.LatLngBounds();

    dealers.forEach(function (dealer) {
        var position = { lat: dealer.lat, lng: dealer.lng };

        var marker = new google.maps.Marker({
            position: position,
            map: jblundMap,
            title: dealer.name,
        });

        var infoWindow = new google.maps.InfoWindow({
            content: jblundBuildInfoWindow(dealer, icons),
            maxWidth: 280,
        });

        marker.addListener('click', function () {
            if (jblundOpenInfoWindow) {
                jblundOpenInfoWindow.close();
            }
            infoWindow.open(jblundMap, marker);
            jblundOpenInfoWindow = infoWindow;
        });

        bounds.extend(position);
    });

    // Auto-fit bounds — zoom in a little more for a single marker
    if (dealers.length === 1) {
        jblundMap.setCenter(bounds.getCenter());
        jblundMap.setZoom(12);
    } else {
        jblundMap.fitBounds(bounds);
    }
}
