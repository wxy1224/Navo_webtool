function getCentroid(points){ 

  var cx = 0;
  var cy = 0;
  var area = 0;
  var j=points.length-1;
  for(var i = 0; i<points.length; j=i++){
    var pt1 = points[i];
    var pt2 = points[j];
    x1 = parseFloat(pt1.x);
    x2 = parseFloat(pt2.x);
    y1 = parseFloat(pt1.y);
    y2 = parseFloat(pt2.y);
    var temp = x1*y2-y1*x2;
    cx+=(x1+x2)*temp;
    cy+=(y1+y2)*temp;
    //console.log(temp+" "+cx+" "+cy);
    area+=temp;
  }
  area/=2;
  cx*=1/(6*area);
  cy*=1/(6*area);
  //console.log(JSON.stringify(points));
  console.log(cx+" "+cy);
  return {x:cx, y:cy};
} 
