Array.prototype.contains = function(obj) {
        var i = this.length;
            while (i--) {
                        if (this[i] === obj) {
                                        return true;
                                                }
                                                    }
                                                        return false;
}


function SankeyDisplay(element) {

var _this = this;

this.container = element;

this.margin = {top: 1, right: 1, bottom: 6, left: 1},
    this.width = 1260 - this.margin.left - this.margin.right,
    this.height = 800 - this.margin.top - this.margin.bottom;

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
    _this.marriages = jsonData.marriageUnits;
    _this.people = jsonData.people;
    _this.links = new Array();
    // Count in and out edges from each marriage unit
    _this.marriages.forEach(function(mu, index) {
        mu.orig_index = index;
        mu.inCount = 0;
        mu.outCount = 0;
        mu.type = "marriage";
        _this.people.forEach(function(person, pindex) {
            person.orig_index = pindex;
            person.inCount = 0;
            person.outCount = 0;
            person.type = "person";
            if (person.source.contains(mu.id)) { // person is from this MU
                mu.outCount++;
                if (!person.sourceMU) person.sourceMU = new Array();
                person.sourceMU.push(mu);
                if (!person.sources) person.sources = new Array();
                person.sources.push(index);
            }
            if (person.target.contains(mu.id)) { // person goes to this MU
                mu.inCount++;
                if (!person.targetMU) person.targetMU = new Array();
                person.targetMU.push(mu);
                if (!person.targets) person.targets = new Array();
                person.targets.push(index);
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
    
    // Now, we set the marriages as nodes
    _this.nodes = _this.marriages.slice(0); // make a deep copy of the array
    var i = _this.nodes.length;
    _this.people.forEach(function(person) {
        // add the person to the list of nodes
        _this.nodes[i] = person;

        // add an edge for each source to this person
        person.sources.forEach(function(src) {
            var edge = {};
            edge.source = src;
            edge.sourcePerc = _this.nodes[src].outPerc;
            edge.target = i;
            edge.targetPerc = 1;
            edge.gender = person.gender;
            _this.links.push(edge);
        });
        // add an edge for each target from this person
        person.targets.forEach(function(tgt) {
            var edge = {};
            edge.source = i;
            edge.sourcePerc = 1;
            edge.target = tgt;
            edge.targetPerc = _this.nodes[tgt].inPerc;
            edge.gender = person.gender;
            _this.links.push(edge);
        });
        
        i++; // increment index into nodes
    });




    //console.log(_this);
    _this.links.forEach(function(edge) {
        edge.value = 1; // needed for the sankey layout.  Not actually used
        edge.svalue = edge.sourcePerc;
        edge.tvalue = edge.targetPerc;
    });

  _this.sankey
      .nodes(_this.nodes)
      .links(_this.links)
      .size([_this.width, _this.height])
      .layout(32);
 
     //console.log(_this.sankey.nodes);

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
      .style("stroke-width", function(d) { return Math.max(d.sdy, d.tdy) / 2; })
      .style("stroke", function(d) { if (d.gender === "Male") return '#1D5190'; return '#C33742';})
      .sort(function(a, b) { return b.dy - a.dy; });
      
  _this.link = link;

/*  link.append("title")
     .text(function(d) { return d.name; });*/

  var node = _this.svg.append("g").selectAll(".node")
      .data(_this.nodes)
    .enter().append("g")
      .attr("class", "node")
      .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; })
    .call(d3.behavior.drag()
      .origin(function(d) { return d; })
      .on("dragstart", function() { this.parentNode.appendChild(this); })
      .on("drag", dragmove))
      .on("mouseover", function(d, i) {//console.log(d); console.log(i);
          if (d.type === "person") {
            d3.selectAll(".link").filter( function(l) { return (l.source === d || l.target === d) ? this : null; })
               .style("stroke-opacity", "0.8");
            this.style("fill-opacity", "0.8");
          } })
      .on("mouseout", function(d, i) {//console.log(d); console.log(i);
          if (d.type === "person") {
            d3.selectAll(".link").filter( function(l) { return (l.source === d || l.target === d) ? this : null; })
               .style("stroke-opacity", "");
          } });

  // Draw the marriage nodes
  node.filter(function(d) { return (d.type === "marriage") ? this : null;}).append("circle")
      .attr("r", function(d) { //console.log(d);
            var r = 0; 
            if (d.x == 0 || d.x == _this.width - _this.sankey.nodeWidth()) 
                r = Math.max(d.dy, _this.sankey.nodeWidth()) / 2;
            else 
                r = Math.max(d.dy, _this.sankey.nodeWidth()) / 1.5; 
            return r;
      })
      .attr("cy", function(d) { return d.dy / 2; })
      .attr("cx", function(d) { return _this.sankey.nodeWidth() / 2; })
      .style("fill", function(d) { //console.log(d); 
             return d.color = "#bbbbbb"; /* "#D0A9F5"; color(d.name.replace( .*, ""));*/ })
      .style("stroke", function(d) { return d3.rgb(d.color).darker(2); })
      .on("click", show_info)

   // Draw the person nodes
   node.filter(function(d) { return (d.type === "person") ? this : null;}).append("rect")
      .attr("height", function(d) { //console.log(d);
            var r = Math.max(d.dy, _this.sankey.nodeWidth()); 
            d.height = r/2;
            return d.height;
      })
      .attr("width", function(d) { //console.log(d);
            return d.height;
      })
      .attr("y", function(d) { return d.dy / 2 - (d.height / 2); })
      .attr("x", function(d) { return _this.sankey.nodeWidth() / 2 - (d.height / 2); })
      .style("fill", function(d) { //console.log(d); 
             var fill;
             if (d.gender === "Male")
                 fill = '#1D5190';
             else
                 fill = '#C33742';
             return d.color = fill; /* "#D0A9F5"; color(d.name.replace( .*, ""));*/ })
      .style("stroke", function(d) { return d.color; })
      .style("stroke-opacity", "0.5")
      .style("fill-opacity","0.2");
//    .append("title")
//      .text(function(d) { return d.name + "\n" + format(d.value); });
/*
  node.append("text")
      .attr("x", function(d) { return (_this.sankey.nodeWidth() / 2);})
      .attr("y", function(d) { return d.dy / 2; })
      .attr("dy", ".35em")
      .attr("text-anchor", "middle")
      .attr("transform", null)
      .text(function(d) { return (d.type === "person") ? d.name : ""; });
    /*.filter(function(d) { return d.x < width / 2; })
      .attr("x", 6 + sankey.nodeWidth())
      .attr("text-anchor", "start");*/
  $('.node').tipsy({ 
        gravity: 'c', 
        html: true, 
        offset: 8,
        hoverlock: true,
        title: function() {
          var d = this.__data__;
          return (d.type === "marriage" && d.name) ? d.name + "'s Marriage" : d.name; 
        }
      });

  function dragmove(d) {
    d3.select(this).attr("transform", "translate(" + d.x + "," + (d.y = Math.max(0, Math.min(_this.height - d.dy, d3.event.y))) + ")");
    _this.sankey.relayout();
    link.attr("d", _this.path);
  }
});

}; // end drawDiagram

} // end SankeyDisplay
