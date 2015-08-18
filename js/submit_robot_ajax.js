$(document).ready(function() {

    $(document).on("click", "#submit_robot", function() {


        var num_lines = document.getElementById("num_lines").value;
        var building_id = document.getElementById("buildingid").value;
        var floornumber = document.getElementById("floornumber").value;
        var img_width = parseFloat(document.getElementById("img_width").value);
        var img_height = parseFloat(document.getElementById("img_height").value);
        var floor_width = parseFloat(document.getElementById("floor_width").value);
        var floor_height = parseFloat(document.getElementById("floor_height").value);

        var array = [];
        
        for(var i = 1; i<parseInt(num_lines)+1; i++){
            input_id = "number"+i;
            var divide_num = parseInt(document.getElementById(input_id).value);
            x1_id = "x1"+i;
            y1_id = "y1"+i;
            var x1_val = parseFloat(document.getElementById(x1_id).value);
            var y1_val = parseFloat(document.getElementById(y1_id).value);
            x1_val = x1_val/img_width*floor_width;
            y1_val = (img_height-y1_val)/img_height*floor_height;
            x2_id = "x2"+i;
            y2_id = "y2"+i;
            var x2_val = parseFloat(document.getElementById(x2_id).value);
            var y2_val = parseFloat(document.getElementById(y2_id).value);
            x2_val = x2_val/img_width*floor_width;
            y2_val = (img_height-y2_val)/img_height*floor_height;
            var head_id = "include_head"+i;
            var include_head = $('input[name='+head_id+']:checked').val();
            var tail_id = "include_tail"+i;
            var include_tail = $('input[name='+tail_id+']:checked').val();

            var x,y;


            if(include_head === "true"){
                var head = new point(x1_val.toFixed(3), y1_val.toFixed(3), 1);
                var num = findPoint(array, head);
                //alert("include head fourd head "+num);
                if(num!== -1){
                    //alert(num);
                    console.log("remove: "+num);
                    array.splice(num,1);
                }
                
                if(include_tail === "false"){
                    for(var j = 0; j<divide_num; j++){
                        console.log("include head not include tail "+j);
                        x = x1_val+(x2_val-x1_val)*j/divide_num;
                        y = y1_val+(y2_val-y1_val)*j/divide_num;
                        x = x.toFixed(3);
                        y = y.toFixed(3);
                        array.push(new point(x,y,1));
                    }
                }else{
                    for(var j = 0; j<divide_num+1; j++){
                        console.log("include head include tail "+j);
                        x = x1_val+(x2_val-x1_val)*j/divide_num;
                        y = y1_val+(y2_val-y1_val)*j/divide_num;
                        x = x.toFixed(3);
                        y = y.toFixed(3);
                        array.push(new point(x,y,1));
                    } 
                }
                
            }else{
                console.log("not include head");
                x1_fixed = x1_val.toFixed(3);
                y1_fixed = y1_val.toFixed(3)
                var head = new point(x1_fixed,y1_fixed , 0);
                var num = findPoint(array, head);
                if(num=== -1){
                    array.push(head);
                }
                
                if(include_tail === "true"){
                    for(var j = 1; j<divide_num; j++){
                        console.log("not include head include tail "+j);
                        x = x1_val+(x2_val-x1_val)*(j+1)/divide_num;
                        y = y1_val+(y2_val-y1_val)*(j+1)/divide_num;
                        x = x.toFixed(3);
                        y = y.toFixed(3);
                        array.push(new point(x,y,1));
                    }
                }else{
                    for(var j = 1; j<divide_num-1; j++){
                        console.log("not include head not include tail "+j);
                        x = x1_val+(x2_val-x1_val)*(j+1)/divide_num;
                        y = y1_val+(y2_val-y1_val)*(j+1)/divide_num;
                        x = x.toFixed(3);
                        y = y.toFixed(3);
                        array.push(new point(x,y,1));
                    } 
                } 
                 
            }
        }
        //alert("number of points:"+array.length);
        //alert(array.toString());
        arraydata = JSON.stringify(array);

       // alert(num_lines);
       var request_con = {buildingid: building_id, floor_number: floornumber, pts:arraydata};

        //connectors = "abcdefg";
        //alert(building_id);

        var request = $.ajax({
            url: 'php/submit_robot_ajax.php',
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

function point(x,y,z){
    this.x = x;
    this.y = y;
    this.z = z;
}

function findPoint(array, point){
    var i = 0;
    var found = 0;
    //alert("point x: "+point.x+"y: "+point.y);
    while(i<array.length && found === 0){
        var p = array[i];
        //alert("x: "+p.x+"y: "+p.y);
        if(p.x === point.x && p.y === point.y){
            found =1;
        }
        i++;
    }
    if(found === 1){
        console.log(point.x + " "+point.y);
        return i-1;
    }
    return -1;
}
