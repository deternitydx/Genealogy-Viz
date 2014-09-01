function ChordDisplay(element) {

    var _this = this;
    this.element = element;

    this.innerElement = null;
    
    this.originalTime = null;

    this.matrix = [];

    var getColor = function (gender, role) {
        if (gender === "Male" && role === "parent")
            return "#1D5190";
        if (gender === "Female" && role === "parent")
            return "#C33742";
        if (gender === "Male" && role === "child")
            return "#73A8E9";
        if (gender === "Female" && role === "child")
            return "#D6757D";
        if (gender === "Male" && role === "divorce")
            return "#0d233e";
        if (gender === "Female" && role === "divorce")
            return "#391013";
    }


    this.width = 600,
    this.height = 500,
    this.innerRadius = Math.min(this.width, this.height) * .31,
    this.outerRadius = this.innerRadius * 1.3,
    this.drawNumSigOthers = false;
    this.patriarchal = true;
    this.nameAsTitle = false;


    this.embed = false;

    this.slider = null;

    this.json_location = function(id) { 
        var noC = new String((new Date().getTime())); 
        return "test/" + id + ".json?nocache=" + noC;
    };

    this.drawTitle = function(element, title) {
        d3.select(element).append("div").append("h1").text(title);
    }

    this.updateNumSigOthers = function(element, numWives) {
        var type = ' wives';
        if (!_this.patriarchal)
             type = ' husbands';
        element.append("div").append("h4").text(numWives + type);
    }

    this.drawLegend = function(element) {

        var cont = d3.select(element);

        cont.append("h3").text("Legend");

        cont.append("h4").text("People");
        var table = cont.append("table");
        table.append("tr").append("td").style("background", getColor("Male", "parent")).style("color", "#FFFFFF").text("Male Parent");
        table.append("tr").append("td").style("background", getColor("Male", "child")).style("color", "#FFFFFF").text("Male Child");
        table.append("tr").append("td").style("background", getColor("Female", "parent")).style("color", "#FFFFFF").text("Female Parent");
        table.append("tr").append("td").style("background", getColor("Female", "child")).style("color", "#FFFFFF").text("Female Child");

        cont.append("h4").text("Relations");
        var table = cont.append("table");
        table.append("tr").append("td").style("background", "#A1CB87").style("color", "#000000").text("Biological");
        table.append("tr").append("td").style("background", "#FFCD81").style("color", "#000000").text("Adoption");
        //table.append("tr").append("td").style("background", "#f7fcb9").style("color", "#000000").text("Colloquial");
        table.append("tr").append("td").style("background", "#C9BCD6").style("color", "#000000").text("Married (BYU)");
        table.append("tr").append("td").style("background", "#AD85FF").style("color", "#000000").text("Married (Eternity)");
        table.append("tr").append("td").style("background", "#f7fcb9").style("color", "#000000").text("Married (Time)");
        table.append("tr").append("td").style("background", "#FFB2E6").style("color", "#000000").text("Married (Civil)");
        
        var time = "All Time";
        if (_this.originalTime != null) {
            time = _this.originalTime;
        }
        cont.append("h4").text("Time Slice");
        cont.append("p").attr("id", "timeText").text(_this.originalTime);
    };

    this.setMatrix = function(timepoint) {
        _this.timepoint = timepoint;    
        var children = _this.data.children;
        var parents = _this.data.parents;
        if (timepoint != null) { // we actually need to look into and build the structures
            children = new Array();
            parents = new Array();

            _this.data.parents.forEach(function(parent) {
                if (/*parent.birthDate <= timepoint && parent.deathDate >= timepoint &&*/
                    ((parent.marriageDate <= timepoint && ((parent.deathDate >= timepoint)) // or they are married eternally or jilldb
                        && (parent.divorceDate >= timepoint || parent.divorceDate == "")) || parent.gender == "Male")) // parent is alive and in marriage
                        parents.push(parent);
            });
            _this.data.children.forEach(function(child) {
                if ((child.adoptionDate != "" && child.adoptionDate <= timepoint) || // child has been adopted
                    (child.adoptionDate == "" && child.birthDate <= timepoint && child.deathDate >= timepoint)) // child is alive
                    children.push(child);
            });
        }

        _this.parents = parents;
        _this.children = children;
        _this.relationships = _this.data.relationships;

        _this.parPerc = 100.0 / parents.length;
        _this.chiPerc = 100.0 / children.length;

        _this.people = children.concat(parents);
        _this.numPeople = parents.length + children.length;


        console.log(_this);
        _this.matrix = new Array();
        for (var i=0; i < _this.numPeople; i++) {
            _this.matrix[i] = new Array();
            for (var j=0; j < _this.numPeople; j++) {
                if (i === j) {
                    if (i < _this.children.length)          // first entries are _this.children
                        _this.matrix[i][j] = _this.chiPerc;
                    else                                // last entries are _this.parents
                        _this.matrix[i][j] = _this.parPerc;
                } else {
                    _this.matrix[i][j] = 0;         // right now, set the connections to none
                }
            }
        }

        // update the matrix based on the _this.relationships between _this.people
        _this.people.forEach(function(person) {
            person.numRels = 0;
        });

        _this.relationships.forEach(function (rel) {
            delete rel.fromId; delete rel.toId;
            _this.people.forEach(function(person, i) {
                if (rel.from === person.id) {
                    person.numRels++;
                    rel.fromId = i;
                }
                if (rel.to === person.id) {
                    person.numRels++;
                    rel.toId = i;
                }
            });
        });

        var activeRels = new Array();

        _this.relationships.forEach(function (rel) {
            // Fix up the peopl who are not in a relationship with a living person 
            if (!rel.hasOwnProperty('fromId') || !rel.hasOwnProperty('toId')) {
                if (!rel.hasOwnProperty('fromId') && rel.hasOwnProperty('toId'))
                    _this.people[rel.toId].numRels--;
                if (rel.hasOwnProperty('fromId') && !rel.hasOwnProperty('toId'))
                    _this.people[rel.fromId].numRels--;
                return;
            } else
                activeRels.push(rel);
        });

        _this.relationships = activeRels;

        console.log(_this.relationships);
        _this.relationships.forEach(function (rel) {
            if (rel.hasOwnProperty('fromId') && rel.hasOwnProperty('toId')) {
    

                // for each relationship, add a part of the matrix
                var i = rel.fromId;
                var j = rel.toId;
    
                var iPerc = (i < _this.children.length) ? _this.chiPerc / _this.people[i].numRels : _this.parPerc / _this.people[i].numRels;
                var jPerc = (j < _this.children.length) ? _this.chiPerc / _this.people[j].numRels : _this.parPerc / _this.people[j].numRels;
    
                _this.matrix[i][i] -= iPerc;
                _this.matrix[j][j] -= jPerc;
                _this.matrix[i][j] = iPerc;
                _this.matrix[j][i] = jPerc;
            }   
        });



        // set up the colors properly based on the type of person
        _this.colorList = new Array();
        for (var i=0; i < _this.numPeople; i++) {
            if (i < _this.children.length)
                _this.colorList[i] = getColor(_this.children[i].gender, "child");
            else {
                var cur = _this.parents[i - _this.children.length];
                if (timepoint == null && cur.divorceDate != "")
                // use divorce method
                    _this.colorList[i] = getColor(cur.gender, "divorce");
                else
                    _this.colorList[i] = getColor(cur.gender, "parent");
            
            }//    _this.colorList[i] = getColor(_this.parents[i - _this.children.length].gender, "parent");
        }

    };

    this.draw = function() {

            _this.fill = d3.scale.ordinal()
            .domain(d3.range(4))
            .range(_this.colorList);

            _this.fillType = d3.scale.ordinal()
                 .domain(["adoption", "biological", "byu", "eternity", "time", "civil"])
                 .range(["#FFCD81", "#A1CB87", "#C9BCD6", "#AD85FF", "#f7fcb9", "#FFB2E6"]);


            _this.chord = d3.layout.chord()
            .padding(.01)
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
                .append("g").attr("transform", "translate(7," + _this.height / 2 + ")");
            } else {
                if (_this.innerElement == null) {
                    if (_this.nameAsTitle) 
                        _this.drawTitle(_this.element, _this.parents[_this.parents.length - 1].name);
                    _this.innerElement = d3.select(_this.element).append("div");
                }    
                _this.innerElement.html("");
                _this.svg = _this.innerElement.append("svg")
                .attr("width", _this.width)
                .attr("height", _this.height)
                .append("g")
                .attr("transform", "translate(" + _this.width / 2 + "," + _this.height / 2 + ")");
            }
            if (_this.drawNumSigOthers) {
                 _this.updateNumSigOthers(_this.innerElement, _this.parents.length - 1);
            }

            console.log(_this.width + ", " + _this.height);

            var g = _this.svg.append("g");

            g.selectAll("path")
            .data(_this.chord.groups)
            .enter().append("path")
            .attr("class", "chordperson")
            .style("fill", function(d) { return _this.fill(d.index); })
            .style("stroke", function(d) { if (d.index >= _this.people.length - 2) return '#000000'; else return _this.fill(d.index); })
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
                    var info = "";
                    var d = this.__data__;
                    if (_this.timepoint == null && _this.people[d.index].divorceDate)
                        info = "<br>Divorced: "+ _this.people[d.index].divorceDate;
                    return _this.people[d.index].name + info; 
                }
            });


            _this.svg.append("g")
            .attr("class", "chord")
            .selectAll("path")
            .data(_this.chord.chords)
            .enter().append("path")
            .attr("class", "chordpath")
            .attr("d", d3.svg.chord().radius(_this.innerRadius))
            .style("fill", function(d) { 
                var ret = "none";
                _this.relationships.forEach(function (rel) {
                    if ( (rel.fromId === d.source.index && rel.toId === d.target.index) ||
                        (rel.fromId === d.source.subindex && rel.toId === d.target.subindex) ) {
                            ret = _this.fillType(rel.type);
                            console.log("Filling with color " + ret + " for type " + rel.type);
                            console.log(d.source.index + "    " + d.source.subindex);
                        }
                });

                return ret; })
            .style("stroke", function(d) { 
                var ret = "none";
                _this.relationships.forEach(function (rel) {
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
                    _this.relationships.forEach(function(rel) {
                        if ( (rel.fromId === d.source.index && rel.toId === d.target.index) ||
                            (rel.fromId === d.source.subindex && rel.toId === d.target.subindex) )
                            ret = rel.desc;
                    });
                    return ret; //_this.people[d.index].name; 
                }
            });

    }

    // drawing code below:
    this.drawChord = function(munit) {

        d3.json(_this.json_location(munit), function(data) {

            if (!data || !data.parents || !data.children || !data.relationships)
                return;

            // recalculate the radii
            _this.innerRadius = Math.min(_this.width, _this.height) * .31,
            _this.outerRadius = _this.innerRadius * 1.3;

            data.parents = data.parents.reverse();
            _this.data = data

            _this.setMatrix(_this.originalTime);

            _this.draw();


        }); // end of json call
    } // end of drawChord


    this.drawTimeSlider = function(element) {

            var stepperdiv = d3.select(element).append("div").style("margin-top", "30px");
            stepperdiv.append("button").text("Prev").on("click", _this.goPrevious);
            stepperdiv.append("button").text("All Time").on("click", _this.allTime);
            stepperdiv.append("button").text("Next").on("click", _this.goNext);
            stepperdiv.append("span").attr("id","timeText").html("All Time");
            
            // Add the time slider
            var min = 1830, max = 1870;
                var time_slider_scale = d3.scale.linear().domain([min, max]).range([min, max]);
            var time_slider_axis = d3.svg.axis().orient("bottom").ticks(10).scale(time_slider_scale).tickFormat(d3.format(".0f"));
            _this.slider = d3.slider().axis(time_slider_axis).min(min).max(max).on("slide", _this.redraw);
            d3.select(element).append("div").attr("id", "sliderDiv").call(_this.slider);

    }

    this.goPrevious = function(event, time) {
        _this.slider.value(_this.slider.value() - 1);
        _this.redraw(null, _this.slider.value());
    }
    this.allTime = function(event, time) {
        _this.redraw(null,null);
    }
    this.goNext = function(event, time) {
        _this.slider.value(_this.slider.value() + 1);
        _this.redraw(null, _this.slider.value());
    }

    this.redraw = function(event, time) {

        if (time === null) {
            _this.setMatrix(null);
            d3.select("#timeText").html("All Time");
        } else {
            _this.setMatrix(time + "-01-01");
            d3.select("#timeText").html(time + "-01-01");
        }
        _this.draw();
    }

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
