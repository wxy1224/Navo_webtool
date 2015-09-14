$(document).ready(function() {

    $(document).on("click", "#submit_con", function() {
       
        var rid1 = document.getElementById("rid1").value;
        var rid2 = document.getElementById("rid2").value;
        var x1 = document.getElementById("x1").value;
        var y1 = document.getElementById("y1").value;
        var x2 = document.getElementById("x2").value;
        var y2 = document.getElementById("y2").value;
        var connectors = document.getElementById("hidden_connectors").value;
        var building_id = document.getElementById("hidden_buildingid").value;
        var dir = $('input[name="dbl_con_dir"]:checked').val();



        var request_con = { rid1:rid1, rid2:rid2, x1: x1, y1:y1, x2:x2, y2:y2, connectors: connectors, building_id:building_id, direction:dir };

    
        var request = $.ajax({
            url: 'php/submit_con_ajax.php',
            method: 'POST',
            data: request_con,
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
