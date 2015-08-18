

$(document).ready(function() {
    var sel_floor = document.getElementById("hidden_sel_floorid");
    var list = document.getElementById("building_list");
    var buildingid = list.options[list.selectedIndex].value;

    //alert(sel_floor);

    if(buildingid!=""){
        $('#floor_list').empty();
        
        var list = document.getElementById("building_list");
        var buildingid = list.options[list.selectedIndex].value;

        var building_request = {building_id:buildingid};

        var request = $.ajax({
            url: 'php/menu_ajax.php',
            method: 'POST',
            data: building_request,
            dataType: 'json',
            error: function(error) {
                console.log(error);
            }
        });

        request.success(function(data) {
            //alert(data);
            if(data === "nothing"){
            }else{
                if(data === "unsuccessful"){
                    alert("Getting floor unsuccessful, please try again")
                }else{          
                    var len = data.length;
                    //alert("sel_floor: "+sel_floor);
                    for(var i = 0; i<len; i+=2){
                        var floorlist = document.getElementById("floor_list");
                        var option = document.createElement("option");
                        option.value = data[i];
                        option.text = data[i+1];
                        floorlist.add(option);
                        //alert("option value: "+option.value);
                        if(option.value == sel_floor){
                            option.selected =true;
                        }
                    }

                }
            }

        });
    }

    
    $("select[name=building]").change(function() {
        // Do soomething with the previous value after the change
        $('#floor_list').empty();
        
        var list = document.getElementById("building_list");
        var buildingid = list.options[list.selectedIndex].value;

        var building_request = {building_id:buildingid};

        var request = $.ajax({
            url: 'php/menu_ajax.php',
            method: 'POST',
            data: building_request,
            dataType: 'json',
            error: function(error) {
                console.log(error);
            }
        });

        request.success(function(data) {
            if(data === "nothing"){
            }else{
                if(data === "unsuccessful"){
                    alert("Getting floor unsuccessful, please try again")
                }else{     
                console.log("sel+floor"+sel_floor);     
                    var len = data.length;
                    for(var i = 0; i<len; i+=2){
                        var floorlist = document.getElementById("floor_list");
                        var option = document.createElement("option");
                        console.log("id: "+data[i]+"text: "+data[i+1]);
                        option.value = data[i];
                        option.text = data[i+1];
                        floorlist.add(option);
                    }

                }
            }

        });

    });
});
