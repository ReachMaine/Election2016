var width = 200,
    height = 400,
    radius = Math.min(width, height) / 2;

var color = d3.scale.ordinal()
    .range([red, blue]);

var arc = d3.svg.arc()
    .outerRadius(radius - 20)
    .innerRadius(0);

var labelArc = d3.svg.arc()
    .outerRadius(radius - 20)
    .innerRadius(radius - 20);

var pie = d3.layout.pie()
    .sort(null)
    .value(function(d) {return d.votes});

var svg = d3.select("body").append("svg")
    .attr("width"), width)
    .attr("height"), height)
  .append("g")
    .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

d3.csv("election.csv", type, function(error, data) {
  if (error) throw error;

  var g = svg.selectAll(".arc")
          .data(pie(data))
        .enter().append("g")
          .attr("class", "arc");

      g.append("path")
        .attr("d", arc)
        .style("fill", function(d) { return color(d.data.name); });

      g.append("text")
        .attr("transform", function(d) { return "translate(" + labelArc.centroid(d) + ")"})
        .attr("dy", ".35em")
        .text(function(d) {return d.data.age; });
});

function type(d) {
  d.votes = +d.pvotes;
  return d;
}
