var pie = d3.layout.pie();
            var w = 150;
            var h = 150;
            var outerRadius = w/2;
            var innerRadius = 0;
            var arc = d3.svg.arc()
                .innerRadius(innerRadius)
                .outerRadius(outerRadius)
            
            //Create SVG Element
            var svg = d3.select("#piechart")
                .append("svg")
                .attr("width", w)
                .attr("height", h);

            //Set Up Groups
            var arcs = svg.selectAll("g.arc")
                .data(pie(votes))
                .enter()
                .append("g")
                .attr("class", "arc")
                .attr("transform", "translate(" + outerRadius + ", " + outerRadius + ")");


            //Draw Arc Paths
                arcs.append("path")
                  .attr("fill", function(d){
                    return color(d.data);
                      })
                  .attr("d", arc);   

                arcs.append("text")
                .attr("transform", function(d) { return "translate(" + arc.centroid(d) + ")"; })
                .attr("dy", ".35em")
                .attr("fill", "white")
                .attr("font-size", "11px")
                  .style("text-anchor", "middle")
                  .text(function(d) { return names(d.data)});         