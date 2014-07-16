<!DOCTYPE html>
<meta charset="utf-8">
<style>

.node {
  stroke: #fff;
  stroke-width: 0.5px;
}

.link {
  stroke: #999;
  stroke-opacity: .3;
}
.unreviewed {
  stroke: #F00;
  stroke-opacity: .5;  
}

</style>
<body>
<script type="text/javascript" src="underscore.min.js"></script>
<script type="text/javascript" src="circlesort.js"></script>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script>

function dblclick(d) {
  d3.select(this).classed("fixed", d.fixed = false);
}

function dragstart(d) {
  d3.select(this).classed("fixed", d.fixed = true);
}

function getWindowSize () {
    var w = window,
        d = document,
        e = d.documentElement,
        g = d.getElementsByTagName('body')[0];

    x = w.innerWidth || e.clientWidth || g.clientWidth;
    y = w.innerHeight|| e.clientHeight|| g.clientHeight;
    
    return { x : x, y : y };
}

var windowSize = getWindowSize();
var width = windowSize.x - 10,
    height = windowSize.y - 10;
var centerX = Math.floor( width / 2 ),
    centerY = Math.floor( height / 2 );
var radiusX = Math.floor( centerX * 0.7 ),
    radiusY = Math.floor( centerY * 0.7 );

var charge = -15,
    linkDistance = 3,
    gravity = 0;


var color = d3.scale.category10();

var linkClass = function (linkClass) {
    var validClasses = ["unreviewed"];
    if ( validClasses.indexOf(linkClass) === -1 ) {
      return "link";
    }
    else {
      return linkClass;
    }
}

var force = d3.layout.force()
    .charge( charge )
    .linkDistance( linkDistance )
    .gravity( gravity )
    .size([width, height]);

var drag = force.drag()
    .on("dragstart", dragstart);

var svg = d3.select("body").append("svg")
    .attr("width", width)
    .attr("height", height);

d3.json("<?php echo $_GET['source'] ? $_GET['source'] : 'pagelinks.php'; ?>", function(error, graph) {
  







  window.users = [];
  var usersTotalWeight = 0;
  graph.nodes.forEach(function(element, index, arr) {

    if (element.group == 2) {
      users.push({
        index : index,
        weight : element.weight
      });
    }
    
  });

  // window.users = _.shuffle(window.users);
  window.users = circleReorder( window.users );


  window.users.forEach(function(e,i,arr){

    var graphIndex = e.index;
    var radians = 2 * Math.PI * (i / users.length);

    var x = centerX + radiusX * Math.cos( radians ) ;
    var y = centerY + radiusY * Math.sin( radians );

    var e = graph.nodes[graphIndex];
    e.fixed = 1;
    e.x = x;
    e.y = y;

  });




  var pixelSize = function (d) {
    var w = d.weight;

    return Math.floor(
      0.5 * Math.log(0.1 * w*w*w + 1) + 2
    );
  };






  force
      .nodes(graph.nodes)
      .links(graph.links)
      .start();

  var link = svg.selectAll(".link")
      .data(graph.links)
    .enter().append("line")
      .attr("class", function(d) { return linkClass(d.linkclass); })
      .style("stroke-width", function(d) { return Math.sqrt(d.value); });

  var node = svg.selectAll(".node")
      .data(graph.nodes)
    .enter().append("circle")
      .attr("class", "node")
      .attr("r", pixelSize ) //if (d.group == 2) return 5; else return 2; } )
      .style("fill", function(d) { return color(d.group); })
      .on("dblclick", dblclick)
      .call(drag);
      //.call(force.drag);

  node.append("title")
      .text(function(d) { return d.name; });

  force.on("tick", function() {
    link.attr("x1", function(d) { return d.source.x; })
        .attr("y1", function(d) { return d.source.y; })
        .attr("x2", function(d) { return d.target.x; })
        .attr("y2", function(d) { return d.target.y; });

    node.attr("cx", function(d) { return d.x; })
        .attr("cy", function(d) { return d.y; });
  });

});

</script>