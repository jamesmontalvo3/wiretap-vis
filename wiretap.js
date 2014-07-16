
// 255 => no hits
// 0 => max hits
function getHitsColor ( hits ) {
    var percentMax = hits / window.maxHits;
    percentMax = 1 - percentMax; // flip it so max hits are dark
    var dec255 = Math.floor(percentMax * 255);

    var hex = dec255.toString(16); // converts to hex, dec 45 == hex 2d

    return hex+hex+hex; // CSS grayscale
}

$(document).ready(function(){

    $.getJSON("api.php",{},function(resp){

        window.rawData = resp;
        console.log('JSON received');

        window.allPages = _.shuffle(resp.pages);
        window.allUsers = _.shuffle(resp.users);
        window.hits     = resp.hits;
        window.maxHits  = resp.maxHits;

        var numUsers = allPages.length;
        var numPages = allPages.length;
        var tr;
        var table = $('#wiretap');
        var currentQty = 0;

        var out = [];

        // for (var u = 0; u < numUsers; u++) {
        for (var u = 0; u < numUsers; u++) {

            for (var p = 0; p < numPages; p++) {
                // console.log(allUsers[u] + ' : ' + allPages[p])
                if ( hits[ allUsers[u] ] && hits[ allUsers[u] ][ allPages[p] ] ) {
                    out[ out.length ] = {
                        x : u,
                        y : p,
                        z : hits[ allUsers[u] ][ allPages[p] ]
                    };
                }
            }

        }

        window.data = out;
        console.log('complete: ' + out.length + ' rows');

        graph(0, allUsers.length, 0, allPages.length, 1000);

    });

});

