function ChordDisplay(element) {

var _this = this;
this.element = element;

this.matrix = [];

var getColor = function (gender, role) {
	if (gender === "M" && role === "parent")
		return "#1D5190";
	if (gender === "F" && role === "parent")
		return "#C33742";
	if (gender === "M" && role === "child")
		return "#73A8E9";
	if (gender === "F" && role === "child")
		return "#D6757D";
}
    

this.width = 600,
this.height = 500,
this.innerRadius = Math.min(this.width, this.height) * .31,
this.outerRadius = this.innerRadius * 1.3;

this.embed = false;

this.json_location = function(id) { 
			var noC = new String((new Date().getTime())); 
			return "test/" + id + ".json?nocache=" + noC;
};

   
this.drawLegend = function(element) {

	var cont = d3.select(element);

	cont.append("h3").text("Legend");

	cont.append("h4").text("People");
	var table = cont.append("table");
	table.append("tr").append("td").style("background", getColor("M", "parent")).style("color", "#FFFFFF").text("Male Parent");
	table.append("tr").append("td").style("background", getColor("M", "child")).style("color", "#FFFFFF").text("Male Child");
	table.append("tr").append("td").style("background", getColor("F", "parent")).style("color", "#FFFFFF").text("Female Parent");
	table.append("tr").append("td").style("background", getColor("F", "child")).style("color", "#FFFFFF").text("Female Child");
	
	cont.append("h4").text("Relations");
	var table = cont.append("table");
	table.append("tr").append("td").style("background", "#A1CB87").style("color", "#000000").text("Biological");
	table.append("tr").append("td").style("background", "#FFCD81").style("color", "#000000").text("Adoption");
	table.append("tr").append("td").style("background", "#f7fcb9").style("color", "#000000").text("Colloquial");
};
    
// drawing code below:
this.drawChord = function(munit) {

d3.json(_this.json_location(munit), function(data) {

if (!data || !data.parents || !data.children || !data.relationships)
	return;
	
// recalculate the radii
_this.innerRadius = Math.min(_this.width, _this.height) * .31,
_this.outerRadius = _this.innerRadius * 1.3;

// fix up the matrix for this particular data set
// we must get the reverse of the elements of the parents because of how the chord diagram works
var parents = data.parents.reverse();
var children = data.children;
var relationships = data.relationships.reverse();

console.log(data);

var parPerc = 100.0 / parents.length;
var chiPerc = 100.0 / children.length;

var people = children.concat(parents);
var numPeople = parents.length + children.length;




_this.matrix = new Array();
for (var i=0; i < numPeople; i++) {
	_this.matrix[i] = new Array();
	for (var j=0; j < numPeople; j++) {
		if (i === j) {
			if (i < children.length)			// first entries are children
				_this.matrix[i][j] = chiPerc;
			else								// last entries are parents
				_this.matrix[i][j] = parPerc;
		} else {
				_this.matrix[i][j] = 0;			// right now, set the connections to none
		}
	}
}

// update the matrix based on the relationships between people
people.forEach(function(person) {
	person.numRels = 0;
});

relationships.forEach(function (rel) {
	people.forEach(function(person, i) {
		if (rel.from === person.name) {
			person.numRels++;
			rel.fromId = i;
		}
		if (rel.to === person.name) {
			person.numRels++;
			rel.toId = i;
		}
	});
});

relationships.forEach(function (rel) {
	// for each relationship, add a part of the matrix
	var i = rel.fromId;
	var j = rel.toId;
	
	var iPerc = (i < children.length) ? chiPerc / people[i].numRels : parPerc / people[i].numRels;
	var jPerc = (j < children.length) ? chiPerc / people[j].numRels : parPerc / people[j].numRels;
	
	_this.matrix[i][i] -= iPerc;
	_this.matrix[j][j] -= jPerc;
	_this.matrix[i][j] = iPerc;
	_this.matrix[j][i] = jPerc;
});



// set up the colors properly based on the type of person
var colorList = new Array();
for (var i=0; i < numPeople; i++) {
	if (i < children.length)
		colorList[i] = getColor(children[i].gender, "child");
	else
		colorList[i] = getColor(parents[i - children.length].gender, "parent");
}

_this.fill = d3.scale.ordinal()
    .domain(d3.range(4))
    .range(colorList);
    
_this.fillType = d3.scale.ordinal()
	.domain(["colloquial", "adoption", "biological"])
	.range(["#f7fcb9", "#FFCD81", "#A1CB87"]);


_this.chord = d3.layout.chord()
    .padding(.01)
    //.sortSubgroups(d3.descending)
    //.sortChords(function (a,b) { console.log(a); })
    .matrix(_this.matrix); 
    

// If we are not embedding, then add an SVG to the element.  If we are, then just add to the element.
_this.svg = null;
if (_this.embed) {

	// recalculate the radii
	_this.innerRadius = Math.min(_this.width, _this.height) * .31,
	_this.outerRadius = Math.min(_this.width, _this.height) / 2;

	console.log("Drawing the chord");
	console.log(_this.element);
	_this.svg = d3.select(_this.element)
		//.append("g").attr("width", _this.width).attr("height", _this.height)
		.append("g").attr("transform", "translate(7," + _this.height / 2 + ")");
//  	  .append("g")
  //  	.attr("transform", "translate(" + _this.width / 2 + "," + _this.height / 2 + ")");
} else {
	_this.svg = d3.select(_this.element).append("svg")
    	.attr("width", _this.width)
    	.attr("height", _this.height)
  	  .append("g")
    	.attr("transform", "translate(" + _this.width / 2 + "," + _this.height / 2 + ")");
}

console.log(_this.width + ", " + _this.height);

var g = _this.svg.append("g");

g.selectAll("path")
    .data(_this.chord.groups)
  .enter().append("path")
  	.attr("class", "chordperson")
    .style("fill", function(d) { return _this.fill(d.index); })
    .style("stroke", function(d) { if (d.index >= people.length - 2) return '#000000'; else return _this.fill(d.index); })
    .attr("d", d3.svg.arc().innerRadius(_this.innerRadius).outerRadius(_this.outerRadius))
    .on("mouseover", fadePerson(.1))
    .on("mouseout", fadePerson(1))
    .text("hi");

    
    $('.chordperson').tipsy({ 
        gravity: 'c', 
        html: true, 
        offset: 0,
        hoverlock: true,
        title: function() {
          var d = this.__data__;
          return people[d.index].name; 
        }
      });
      
/* // Trying to add text to SVG without MouseOver
   // Does not work correctly yet 

g.selectAll("text")
	.data(_this.chord.groups)
	.enter().append("text")
    .attr("x", function(d) { console.log(d); return "8";})
    .attr("dy", ".35em")
    //.attr("transform", function(d) { return d.angle > Math.PI ? "rotate(180)translate(-16)" : null; })
    //.style("text-anchor", function(d) { return d.angle > Math.PI ? "end" : null; })
    .text(function(d) { console.log(d); return people[d.index].name; });;
*/

/*
_this.ticks = svg.append("g").selectAll("g")
    .data(_this.chord.groups)
  .enter().append("g").selectAll("g")
    .data(_this.groupTicks)
  .enter().append("g")
    .attr("transform", function(d) {
      return "rotate(" + (d.angle * 180 / Math.PI - 90) + ")"
          + "translate(" + outerRadius + ",0)";
    });
    */
/*
ticks.append("text")
    .attr("x", 8)
    .attr("dy", ".35em")
    .attr("transform", function(d) { return d.angle > Math.PI ? "rotate(180)translate(-16)" : null; })
    .style("text-anchor", function(d) { return d.angle > Math.PI ? "end" : null; })
    .text(function(d) { console.log(d); return labelOf(d.index); });
*/

_this.svg.append("g")
    .attr("class", "chord")
  .selectAll("path")
    .data(_this.chord.chords)
  .enter().append("path")
  	.attr("class", "chordpath")
    .attr("d", d3.svg.chord().radius(_this.innerRadius))
    .style("fill", function(d) { 
    	var ret = "none";
    	relationships.forEach(function (rel) {
          	if ( (rel.fromId === d.source.index && rel.toId === d.target.index) ||
          			(rel.fromId === d.source.subindex && rel.toId === d.target.subindex) )
          		ret = _this.fillType(rel.type);
    	});
    	
    	return ret; })
    .style("stroke", function(d) { 
    	var ret = "none";
    	relationships.forEach(function (rel) {
          	if ( (rel.fromId === d.source.index && rel.toId === d.target.index) ||
          			(rel.fromId === d.source.subindex && rel.toId === d.target.subindex) )
          		ret = "#000000";
    	});
    	
    	return ret; })
    .style("opacity", 1)
    .on("mouseover", fadeLink(.1))
    .on("mouseout", fadeLink(1));
    
    $('.chordpath').tipsy({ 
        gravity: 'c', 
        html: true, 
        offset: 0,
        hoverlock: false,
        title: function() {
          var d = this.__data__;
          var ret = "";
          relationships.forEach(function(rel) {
          	if ( (rel.fromId === d.source.index && rel.toId === d.target.index) ||
          			(rel.fromId === d.source.subindex && rel.toId === d.target.subindex) )
          		ret = rel.desc;
          });
          return ret; //people[d.index].name; 
        }
      });
    

    
}); // end of json call
} // end of drawChord

// Returns an event handler for fading a given chord group.
function fadePerson(opacity) {
  return function(g, i) {
    _this.svg.selectAll(".chord path")
        .filter(function(d) { return d.source.index != i && d.target.index != i; })
      .transition()
        .style("opacity", opacity);
  };
}

// Returns an event handler for fading to one chord
function fadeLink(opacity) {
  return function(g, i) {
    _this.svg.selectAll(".chord path")
        .filter(function(d) { return d.source.index != g.source.index || d.target.index != g.target.index; })
      .transition()
        .style("opacity", opacity);
  };
}

} // end ChordDisplay
