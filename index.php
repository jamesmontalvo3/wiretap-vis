<!DOCTYPE html>
<html>
	<head>
		<meta charset=”utf-8”> 
		<title>Wiretap</title>
	    <style>
			table {
				border-collapse:collapse;
			}
			td {
				width:5px;
				height:5px;
				border: solid white 1px;
			}
	    </style>
	    <script type="text/javascript" src="protovis/protovis.min.js"></script>
      <script type="text/javascript" src="underscore.min.js"></script>
	    <script type="text/javascript" src="clusterfck.js"></script>
    <style type="text/css">
		#fig {
		  width: 430px;
		  height: 425px;
		}
    </style>
		<!--  <link href="../assets/css/bootstrap-responsive.css" rel="stylesheet"> -->
	</head>
	<body>
		<h1>Wiretap</h1>
		<!-- <div id="center"><div id="fig"></div></div> -->
		<script type="text/javascript+protovis">

// var data = pv.range(100).map(function(x) {
//     return {x: x, y: Math.random(), z: Math.pow(10, 2 * Math.random())};
//   });

function graph (xmin, xmax, ymin, ymax, zmax) {
  /* Sizing and scales. */
  var w = 800,
      h = 600,
      x = pv.Scale.linear(xmin, xmax).range(0, w),
      y = pv.Scale.linear(ymin, ymax).range(0, h),
      c = pv.Scale.log(1, zmax).range("red", "green");

  /* The root panel. */
  var vis = new pv.Panel()
      .width(w)
      .height(h)
      .bottom(20)
      .left(20)
      .right(10)
      .top(5);

  /* Y-axis and ticks. */
  vis.add(pv.Rule)
      .data(y.ticks())
      .bottom(y)
      .strokeStyle(function(d) d ? "#eee" : "#000")
    .anchor("left").add(pv.Label)
      .visible(function(d) d > 0 && d < 1)
      .text(y.tickFormat);

  /* X-axis and ticks. */
  vis.add(pv.Rule)
      .data(x.ticks())
      .left(x)
      .strokeStyle(function(d) d ? "#eee" : "#000")
    .anchor("bottom").add(pv.Label)
      .visible(function(d) d > 0 && d < 100)
      .text(x.tickFormat);

  /* The dot plot! */
  vis.add(pv.Panel)
      .data(data)
    .add(pv.Dot)
      .left(function(d) x(d.x))
      .bottom(function(d) y(d.y))
      .strokeStyle(function(d) c(d.z))
      .fillStyle(function() this.strokeStyle().alpha(.2))
      .size(function(d) d.z)
      .title(function(d) d.z.toFixed(1));

  vis.render();
}

		</script>
		<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
		<script src="json2.js"></script>
		<script type="text/javascript" src="wiretap.js"></script>
	</body>
</head><?php

/*

"Main Page", "ejmontal", 2335

$d = array(
	array(
		"Main Page",
		"ejmontal",
		2524
	),
	array(),
	array()
);


NULL
approve
approvefile
delete
edit
edit.
formcreate
formedit
history
historysubmit
info
protect
purge
revert
rollback
submit
unapprove
unapprovefile
view
watch






page_id
page_name
user_name
hit_timestamp
hit_year
hit_month
hit_day
hit_hour
hit_weekday
page_action
oldid
diff
referer_url
referer_title


<!DOCTYPE html>
<html>
<head>
	<title><?php echo $title; ?></title>
</head>
<body>
	<?php echo system("python ~/www/") ?>
</body>
</html>

*/