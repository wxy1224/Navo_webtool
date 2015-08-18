function getCentroid(points){ 
  var centroid = {x: 0, y: 0};
  for(var i = 0; i < points.length; i++) {
     var point = points[i];
     centroid.x += parseInt(point.x);
     centroid.y += parseInt(point.y);
     //alert(centroid.x);
  }
  centroid.x /= points.length;
  centroid.y /= points.length;

  return centroid;
} 