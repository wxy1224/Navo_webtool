$(document).ready(function() {

    $(document).on("click", "#submit", function() {
       
        var geoid = document.getElementById("geoid").value;
        var parentid = document.getElementById("parent_id").value;
        var geointro = document.getElementById("geointro").value;
        var geodetail = document.getElementById("geodetail").value;
        var buildingid = document.getElementById("buildingid").value;
        var floornumber = document.getElementById("floornumber").value;
        var geotype = $('input[name="geo_type"]:checked').val();
        var poly_pts = document.getElementById("poly_pts").value;

        //alert(JSON.stringify(poly_pts));

        console.log(poly_pts);
        
        var request_geo = {geo_id: geoid, parent_id: parentid, building_id:buildingid, floor_number: floornumber, geo_intro: geointro, geo_detail: geodetail, geo_type:geotype, geo_pts: poly_pts};
    
        var request = $.ajax({
            url: 'php/submit_ajax.php',
            method: 'POST',
            data: request_geo,
            dataType: 'text',
            error: function(error) {
                console.log(error);
            }
        });

        request.success(function(data) {
            alert(data);
        });
    });
});
