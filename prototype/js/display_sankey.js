
function SankeyDisplay(element) {

var _this = this;

this.container = element;

this.margin = {top: 1, right: 1, bottom: 6, left: 1},
    this.width = 960 - this.margin.left - this.margin.right,
    this.height = 500 - this.margin.top - this.margin.bottom;

this.formatNumber = d3.format(",.0f"),
    this.format = function(d) { return formatNumber(d) + " TWh"; },
    this.color = d3.scale.category20();

this.svg = null;

this.sankey = d3.sankey()
    .nodeWidth(15)
    .nodePadding(10)
    .size([this.width, this.height]);

this.path = this.sankey.link();

this.drawDiagram = function(json_location) {

d3.json(json_location, function(jsonData) {

    // We must clean up the data now
    _this.nodes = jsonData.marriageUnits;
    _this.links = jsonData.people;
    // Count in and out edges from each marriage unit
    _this.nodes.forEach(function(mu, index) {
        mu.inCount = 0;
        mu.outCount = 0;
        _this.links.forEach(function(person) {
            if (person.source === mu.id) { // person is from this MU
                mu.outCount++;
                person.sourceMU = mu;
                person.source = index;
            }
            if (person.target === mu.id) { // person goes to this MU
                mu.inCount++;
                person.targetMU = mu;
                person.target = index;
            }
        });
        if (mu.inCount > 0)
            mu.inPerc = 1 / mu.inCount;
        else
            mu.inPerc = 0;
        if (mu.outCount > 0)
            mu.outPerc = 1 / mu.outCount;
        else
            mu.outPerc = 0;
    });
    console.log(_this);
    _this.links.forEach(function(person) {
        person.value = 1; // needed for the sankey layout.  Not actually used
        person.svalue = person.sourceMU.outPerc;
        person.tvalue = person.targetMU.inPerc;
    });

  _this.sankey
      .nodes(_this.nodes)
      .links(_this.links)
      .size([_this.width, _this.height])
      .layout(32);
  
  // Clean out the element
  d3.select(_this.container).text("");

  _this.svg = d3.select(element).append("svg")
      .attr("width", _this.width + _this.margin.left + _this.margin.right)
      .attr("height", _this.height + _this.margin.top + _this.margin.bottom)
    .append("g")
      .attr("transform", "translate(" + _this.margin.left + "," + _this.margin.top + ")");



  var link = _this.svg.append("g").selectAll(".link")
      .data(_this.links)
    .enter().append("path")
      .attr("class", "link")
      .attr("d", _this.path)
      .style("stroke-width", function(d) { return Math.min(d.sdy, d.tdy); })
      .style("stroke", function(d) { if (d.gender === "M") return '#1D5190'; return '#C33742';})
      .sort(function(a, b) { return b.dy - a.dy; });
      
  $('.link').tipsy({ 
        gravity: 'c', 
        html: true, 
        offset: 0,
        hoverlock: true,
        title: function() {
          var d = this.__data__;
          return d.name; 
        }
      });

/*  link.append("title")
     .text(function(d) { return d.name; });*/

  var node = _this.svg.append("g").selectAll(".node")
      .data(_this.nodes)
    .enter().append("g")
      .attr("class", "node")
      .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; })
      .on("click", show_info)
    .call(d3.behavior.drag()
      .origin(function(d) { return d; })
      .on("dragstart", function() { this.parentNode.appendChild(this); })
      .on("drag", dragmove));

  node.append("circle")
      .attr("r", function(d) { return d.dy / 2; })
      .attr("cy", function(d) { return d.dy / 2; })
      .attr("cx", function(d) { return _this.sankey.nodeWidth() / 2; })
      .style("fill", function(d) { return d.color = "#bbbbbb"; /* "#D0A9F5"; color(d.name.replace( .*, ""));*/ })
      .style("stroke", function(d) { return d3.rgb(d.color).darker(2); });
//    .append("title")
//      .text(function(d) { return d.name + "\n" + format(d.value); });

  node.append("text")
      .attr("x", function(d) { return (_this.sankey.nodeWidth() / 2);})
      .attr("y", function(d) { return d.dy / 2; })
      .attr("dy", ".35em")
      .attr("text-anchor", "middle")
      .attr("transform", null)
      .text(function(d) { return d.name; });
    /*.filter(function(d) { return d.x < width / 2; })
      .attr("x", 6 + sankey.nodeWidth())
      .attr("text-anchor", "start");*/

  function dragmove(d) {
    d3.select(this).attr("transform", "translate(" + d.x + "," + (d.y = Math.max(0, Math.min(_this.height - d.dy, d3.event.y))) + ")");
    _this.sankey.relayout();
    link.attr("d", _this.path);
  }
});

}; // end drawDiagram

} // end SankeyDisplay
